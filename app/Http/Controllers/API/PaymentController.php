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
            Log::info('Received Paymob callback:', $data);
            $payment = Payment::where('paymob_order_id', $data['order'])
                ->with(['order.orderItems.product', 'order.user'])
                ->first();
            if (!$payment) {
                Log::error('Payment not found for order:', ['order_id' => $data['order']]);
                return response()->json(['error' => 'Payment not found'], 404);
            }
            $payment->update([
                'status' => $data['success'] ? 'completed' : 'failed',
                'paid_at' => $data['success'] ? now() : null,
                'metadata' => array_merge($payment->metadata ?? [], [
                    'transaction_id' => $data['id'] ?? null,
                    'payment_method' => $data['source_data']['sub_type'] ?? null,
                    'card_type' => $data['source_data']['card_subtype'] ?? null,
                    'last_four_digits' => $data['source_data']['last_four_digits'] ?? null
                ])
            ]);
            if ($data['success']) {
                $this->orderService->updateOrderStatus($payment->order, 'completed');
                $this->orderService->clearUserCart($payment->order->user);
            } else {
                $this->orderService->updateOrderStatus($payment->order, 'failed');
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Payment completed successfully',
                'payment_id' => $payment->id,
                'order_id' => $payment->order->id
            ]);

        } catch (\Exception $e) {
            Log::error('Payment callback error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json(['error' => 'Failed to process payment callback'], 500);
        }
    }
    private function getBillingData($user)
    {
        return [
            "first_name" => $user->first_name ?? 'Test',
            "last_name" => $user->last_name ?? 'User',
            "email" => $user->email ?? 'test@example.com',
            "phone_number" => $user->phone_number ?? '01000000000',
            "country" => $user->country ?? 'EG',
            "city" => $user->city ?? 'Cairo',
            "street" => $user->street ?? 'Nasr City',
            "apartment" => $user->apartment ?? 'N/A',
            "floor" => $user->floor ?? 'N/A',
            "building" => $user->building ?? 'N/A',
            "postal_code" => $user->postal_code ?? '12345'
        ];
    }
}