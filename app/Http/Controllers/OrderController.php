<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // ==================== My Orders ====================

    public function index(Request $request)
    {
        // ==================== Load Customer Orders ====================

        $orders = $request
            ->user()
            ->orders()
            ->with([
                'items.product.images',
                'items.variant',
            ])
            ->latest()
            ->get();

        // ==================== Return Orders Page ====================

        return inertia('Orders/Orders', [
            'orders' => $orders,
        ]);
    }

    // ==================== Order Details ====================

    public function show(Request $request, Order $order)
    {
        // ==================== Verify Order Ownership ====================

        if ($order->customer_id !== $request->user()->id) {
            abort(403);
        }

        // ==================== Load Order Details ====================

        $order->load([
            'items.product.images',
            'items.variant',
            'statusHistories.changedBy',
        ]);

        // ==================== Return Order Details Page ====================

        return inertia('Orders/OrderDetails', [
            'order' => $order,
        ]);
    }
}
