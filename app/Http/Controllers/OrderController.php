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

    public function create(Request $request)
    {
        return inertia('CustomOrder/CustomOrder');
    }

    public function customOrder(Request $request)
    {
        // ==================== Validate Custom Order ====================

        $validated = $request->validate([
            'description' => 'required|string|max:5000',
            'address' => 'required|string|max:1000',
        ]);

        // ==================== Create Custom Order ====================

        $order = $request->user()->orders()->create([
            'type' => 'custom',
            'custom_request' => $validated['description'],
            'delivery_address' => $validated['address'],
            'status' => 'accepted',
        ]);

        // ==================== Redirect ====================

        return redirect()
            ->route('home')
            ->with(
                'success',
                "Your custom order #{$order->id} has been submitted successfully."
            );
    }
}
