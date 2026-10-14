<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Food;
use App\Models\Order;
use App\Models\Restaurant;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('dashboard', [
            'customerCount' => Customer::count(),
            'restaurantCount' => Restaurant::count(),
            'foodCount' => Food::count(),
            'orderCount' => Order::count(),
            'deliveryCount' => Delivery::count(),
            'recentOrders' => Order::with(['customer', 'food.restaurant'])->latest()->take(6)->get(),
        ]);
    }
}
