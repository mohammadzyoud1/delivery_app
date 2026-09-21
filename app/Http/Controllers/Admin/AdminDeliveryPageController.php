<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

class AdminDeliveryPageController extends Controller
{
    // ==================== Admin Delivery ====================

    public function index()
    {
        // ==================== Load Delivery Orders ====================

        $orders = Order::query()
            ->with([
                'customer',
                'deliveryAssignment.deliveryUser',
                'deliveryAssignment.assignedBy',
            ])
            ->whereIn('status', [
                'accepted',
                'preparing',
                'on_the_way',
                'at_the_door',
                'delivered',
            ])
            ->latest()
            ->get();

        // ==================== Return Delivery Page ====================

        return inertia('Admin/Delivery', [
            'orders' => $orders,
        ]);
    }
}
