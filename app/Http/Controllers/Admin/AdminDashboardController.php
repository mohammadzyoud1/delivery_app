<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;

class AdminDashboardController extends Controller
{
    // ==================== Admin Dashboard ====================

    public function index()
    {
        // ==================== Dashboard Statistics ====================

        $stats = [
            'orders' => Order::count(),
            'users' => User::count(),
            'sellers' => User::whereHas('roles', function ($query) {
                $query->where('name', 'seller');
            })->count(),
            'delivery' => User::whereHas('roles', function ($query) {
                $query->where('name', 'delivery');
            })->count(),
            'products' => Product::count(),
        ];

        // ==================== Recent Orders ====================

        $recentOrders = Order::query()
            ->latest()
            ->take(5)
            ->get([
                'id',
                'status',
                'total_amount',
            ]);

        // ==================== Return Dashboard ====================

        return inertia('Admin/Dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }
}
