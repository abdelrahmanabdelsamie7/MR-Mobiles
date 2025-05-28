<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\{Mobile, Accessory, Brand, Contact, User, Order};
class StatisticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admins')->only('getStatistics');
    }
    public function getStatistics()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'mobiles_count' => Mobile::count(),
                'accessories_count' => Accessory::count(),
                'brands_count' => Brand::count(),
                'contact_data' => Contact::count(),
                'users_count' => User::count(),
                'orders' => Order::where('payment_status', 'confirmed')->count(),
            ]
        ]);
    }
}