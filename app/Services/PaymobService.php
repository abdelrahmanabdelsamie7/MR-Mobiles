<?php
namespace App\Services;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymobService
{
    private $apiKey;
    private $integrationId;
    private $iframeId;
    private $hmacSecret;
    public function __construct()
    {
        $this->apiKey = config('services.paymob.api_key');
        $this->integrationId = config('services.paymob.integration_id');
        $this->iframeId = config('services.paymob.iframe_id');
        $this->hmacSecret = config('services.paymob.hmac_secret');
    }
    public function getAuthToken()
    {
        $response = Http::post('https://accept.paymob.com/api/auth/tokens', [
            'api_key' => $this->apiKey
        ]);
        if (!$response->successful()) {
            Log::error('Failed to get auth token', [
                'response_body' => $response->body(),
                'response_json' => $response->json(),
                'status' => $response->status(),
                'api_key' => substr($this->apiKey, 0, 5) . '...'
            ]);
            throw new \Exception('Failed to authenticate with payment provider: ' . ($response->json('detail') ?? 'Unknown error'));
        }
        return $response->json('token');
    }
    public function createOrder($authToken, $cart, $merchantOrderId)
    {
        $orderData = [
            'auth_token' => $authToken,
            'delivery_needed' => false,
            'merchant_order_id' => $merchantOrderId,
            'amount_cents' => (int)($cart->total_price * 100),
            'currency' => 'EGP',
            'items' => $cart->cartItems->map(function ($item) {
                $product = $item->product;
                if (!$product) {
                    throw new \Exception('Product not found for cart item');
                }
                return [
                    'name' => $product->name ?? 'Unknown Product',
                    'amount_cents' => (int)($item->price * 100),
                    'description' => $product->description ?? '',
                    'quantity' => (int)$item->quantity
                ];
            })->toArray()
        ];

        Log::info('Attempting to create Paymob order with data:', [
            'order_data' => $orderData,
            'cart_total' => $cart->total_price,
            'items_count' => $cart->cartItems->count()
        ]);
        $response = Http::post('https://accept.paymob.com/api/ecommerce/orders', $orderData);
        if (!$response->successful()) {
            Log::error('Failed to create Paymob order', [
                'response_body' => $response->body(),
                'response_json' => $response->json(),
                'status' => $response->status(),
                'order_data_sent' => $orderData,
                'auth_token' => substr($authToken, 0, 10) . '...'
            ]);
            throw new \Exception('Failed to create order: ' . ($response->json('detail') ?? $response->body() ?? 'Unknown error'));
        }
        return $response->json();
    }
    public function createPaymentKey($authToken, $orderId, $amount, $user)
    {
        $paymentKeyData = [
            'auth_token' => $authToken,
            'amount_cents' => (int)($amount * 100),
            'expiration' => 3600,
            'order_id' => $orderId,
            'billing_data' => [
                'first_name' => $user->name ?? 'Customer',
                'last_name' => 'User',
                'email' => $user->email ?? 'customer@example.com',
                'phone_number' => $user->phone ?? '01000000000',
                'apartment' => 'NA',
                'floor' => 'NA',
                'street' => 'NA',
                'building' => 'NA',
                'shipping_method' => 'NA',
                'postal_code' => 'NA',
                'city' => 'NA',
                'country' => 'NA',
                'state' => 'NA'
            ],
            'currency' => 'EGP',
            'integration_id' => $this->integrationId
        ];

        Log::info('Attempting to create payment key with data:', [
            'payment_key_data' => $paymentKeyData,
            'order_id' => $orderId
        ]);

        $response = Http::post('https://accept.paymob.com/api/acceptance/payment_keys', $paymentKeyData);

        if (!$response->successful()) {
            Log::error('Failed to create payment key', [
                'response_body' => $response->body(),
                'response_json' => $response->json(),
                'status' => $response->status(),
                'payment_key_data' => $paymentKeyData,
                'auth_token' => substr($authToken, 0, 10) . '...'
            ]);
            throw new \Exception('Failed to create payment key: ' . ($response->json('detail') ?? $response->body() ?? 'Unknown error'));
        }

        return $response->json();
    }
    public function getPaymentUrl($paymentToken)
    {
        return 'https://accept.paymob.com/api/acceptance/iframes/' . $this->iframeId . '?payment_token=' . $paymentToken;
    }
    private function getBillingData(User $user)
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