<?php
namespace App\Http\Controllers\API;
use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseJsonTrait;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Models\{Payment};
use App\Services\{PaymobService, OrderService};
class PaymentController extends Controller
{
    use ResponseJsonTrait;
    private $apiKey;
    private $integrationId;
    private $iframeId;
    private $paymobService;
    private $orderService;
    public function __construct(PaymobService $paymobService, OrderService $orderService)
    {
        $this->apiKey = config('services.paymob.api_key');
        $this->integrationId = config('services.paymob.integration_id');
        $this->iframeId = config('services.paymob.iframe_id');
        $this->paymobService = $paymobService;
        $this->orderService = $orderService;
    }
    private function getAuthToken()
    {
        $response = Http::post('https://accept.paymob.com/api/auth/tokens', [
            'api_key' => $this->apiKey
        ]);

        if (!$response->successful()) {
            \Log::error('Failed to get auth token', [
                'response_body' => $response->body(),
                'response_json' => $response->json(),
                'status' => $response->status(),
                'api_key' => substr($this->apiKey, 0, 5) . '...' // Log partial API key for security
            ]);
            throw new \Exception('Failed to authenticate with payment provider: ' . ($response->json('detail') ?? 'Unknown error'));
        }

        return $response->json('token');
    }
    public function createCheckoutSession(Request $request)
    {
        try {
            $user = $request->user('api');
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            $cart = $user->cart()->with(['cartItems.product'])->first();
            if (!$cart || $cart->cartItems->isEmpty()) {
                return response()->json(['error' => 'Cart is empty'], 400);
            }
            $order = $this->orderService->createOrderFromCart($cart, $user);
            $order->load(['orderItems.product', 'user']);
            $payment = Payment::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'amount' => $cart->total_price,
                'status' => 'pending',
                'payment_method' => 'paymob',
                'metadata' => [
                    'order_id' => $order->id,
                    'cart_id' => $cart->id
                ]
            ]);
            $authToken = $this->paymobService->getAuthToken();
            Log::info('Successfully obtained auth token');
            $merchantOrderId = 'CART-' . $cart->id . '-' . time();
            $orderData = $this->paymobService->createOrder($authToken, $cart, $merchantOrderId);
            $payment->update(['paymob_order_id' => $orderData['id']]);
            $paymentKeyData = $this->paymobService->createPaymentKey(
                $authToken,
                $orderData['id'],
                $cart->total_price,
                $user
            );
            return response()->json([
                'payment_url' => $this->paymobService->getPaymentUrl($paymentKeyData['token'])
            ]);
        } catch (\Exception $e) {
            Log::error('Payment error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to create checkout session: ' . $e->getMessage()], 500);
        }
    }
    public function handleCallback(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('Received Paymob callback - Full Request Data:', [
                'request_data' => $data,
                'request_headers' => $request->headers->all(),
                'request_method' => $request->method(),
                'request_url' => $request->fullUrl()
            ]);

            $orderId = $data['order'] ?? null;
            $merchantOrderId = $data['merchant_order_id'] ?? null;
            $success = $data['success'] ?? false;
            $errorOccurred = $data['error_occured'] ?? false;
            $txnResponseCode = $data['txn_response_code'] ?? null;
            $dataMessage = $data['data.message'] ?? null;

            Log::info('Raw Paymob callback data:', [
                'order_id' => $orderId,
                'merchant_order_id' => $merchantOrderId,
                'success' => $success,
                'error_occured' => $errorOccurred,
                'txn_response_code' => $txnResponseCode,
                'data_message' => $dataMessage,
                'full_data' => $data
            ]);


            $isSuccess = (
                $success === true ||
                $txnResponseCode === 'APPROVED' ||
                $txnResponseCode === '000' ||
                (isset($data['status']) && $data['status'] === 'Paid')
            );

            Log::info('Payment validation result:', [
                'success' => $success,
                'error_occurred' => $errorOccurred,
                'txn_response_code' => $txnResponseCode,
                'is_success' => $isSuccess,
                'message' => $dataMessage,
                'merchant_order_id' => $merchantOrderId,
                'transaction_id' => $data['id'] ?? null,
                'validation_conditions' => [
                    'success_is_true' => $success === true,
                    'txn_response_code_is_approved' => in_array($txnResponseCode, ['APPROVED', '000']),
                    'status_is_paid' => isset($data['status']) && $data['status'] === 'Paid'
                ]
            ]);

            $payment = Payment::where('paymob_order_id', $orderId)
                ->with(['order.orderItems.product', 'order.user'])
                ->first();

            if (!$payment) {
                Log::error('Payment not found in database:', [
                    'order_id' => $orderId,
                    'merchant_order_id' => $merchantOrderId,
                    'search_criteria' => ['paymob_order_id' => $orderId]
                ]);
                return response()->json(['error' => 'Payment not found'], 404);
            }

            Log::info('Found payment record:', [
                'payment_id' => $payment->id,
                'order_id' => $payment->order_id,
                'status' => $payment->status,
                'amount' => $payment->amount,
                'paymob_amount' => ($data['amount_cents'] ?? 0) / 100,
                'paymob_order_id' => $payment->paymob_order_id,
                'metadata' => $payment->metadata
            ]);

            // Update payment status
            $payment->update([
                'status' => $isSuccess ? 'completed' : 'failed',
                'paid_at' => $isSuccess ? now() : null,
                'metadata' => array_merge($payment->metadata ?? [], [
                    'transaction_id' => $data['id'] ?? null,
                    'payment_method' => $data['source_data.sub_type'] ?? null,
                    'card_type' => $data['source_data.sub_type'] ?? null,
                    'last_four_digits' => $data['source_data.pan'] ?? null,
                    'error_message' => $dataMessage,
                    'paymob_response' => [
                        'merchant_order_id' => $merchantOrderId,
                        'transaction_id' => $data['id'] ?? null,
                        'amount_cents' => $data['amount_cents'] ?? null,
                        'currency' => $data['currency'] ?? null,
                        'created_at' => $data['created_at'] ?? null,
                        'raw_response' => $data
                    ]
                ])
            ]);

            Log::info('Payment status updated:', [
                'payment_id' => $payment->id,
                'new_status' => $isSuccess ? 'completed' : 'failed',
                'paid_at' => $isSuccess ? now() : null,
                'updated_metadata' => $payment->metadata
            ]);

            if ($isSuccess) {
                $this->orderService->updateOrderStatus($payment->order, 'completed');
                $this->orderService->clearUserCart($payment->order->user);
                Log::info('Payment successful - Order updated:', [
                    'order_id' => $payment->order->id,
                    'new_status' => 'completed'
                ]);
                return redirect()->route('payment.success', ['order_id' => $payment->order->id]);
            } else {
                $this->orderService->updateOrderStatus($payment->order, 'failed');
                Log::info('Payment failed - Order updated:', [
                    'order_id' => $payment->order->id,
                    'new_status' => 'failed',
                    'error_message' => $dataMessage
                ]);
                return redirect()->route('payment.failed', [
                    'error_message' => $dataMessage ?? 'Payment failed',
                    'order_id' => $payment->order->id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Payment callback error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            return response()->json(['error' => 'Failed to process payment callback: ' . $e->getMessage()], 500);
        }
    }
    private function getBillingData($user)
    {
        return [
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "email" => $user->email,
            "phone_number" => $user->phone_number,
            "country" => $user->country,
            "city" => $user->city,
            "street" => $user->street,
            "apartment" => $user->apartment,
            "floor" => $user->floor,
            "building" => $user->building,
            "postal_code" => $user->postal_code
        ];
    }
}