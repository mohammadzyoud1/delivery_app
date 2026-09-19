<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

class AdminOrderController extends Controller
{
    // ==================== Admin Orders ====================

    public function index()
    {
        // ==================== Load Orders ====================

        $orders = Order::query()
            ->with([
                'customer',
                'items.product.images',
                'items.variant',
                'deliveryAssignment.deliveryUser',
            ])
            ->latest()
            ->get();

        // ==================== Return Orders Page ====================

        return inertia('Admin/Orders', [
            'orders' => $orders,
        ]);
    }
}
