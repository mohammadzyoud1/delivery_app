<?php

namespace App\Http\Controllers;

use App\Models\DeliveryAssignment;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    // ==================== Delivery Orders ====================

    public function index(Request $request)
    {
        // ==================== Current Delivery User ====================

        $deliveryUserId = $request->user()->id;

        // ==================== Load Orders ====================

        $orders = Order::query()
            ->with([
                'customer',
                'items.product.images',
                'items.variant',
                'deliveryAssignment',
            ])
            ->whereIn('status', [
                'accepted',
                'preparing',
                'on_the_way',
                'at_the_door',
            ])
            ->latest()
            ->get();

        // ==================== Return Delivery Page ====================

        return inertia('Delivery/Orders', [
            'orders' => $orders,
            'deliveryUserId' => $deliveryUserId,
        ]);
    }

    // ==================== Take Order ====================

    public function take(Request $request, Order $order)
    {
        // ==================== Take Order Transaction ====================

        DB::transaction(function () use ($request, $order) {
            // ==================== Lock Order ====================

            $order = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            // ==================== Check Order Status ====================

            if ($order->status !== 'accepted') {
                abort(422, 'This order is no longer available.');
            }

            // ==================== Check Existing Assignment ====================

            if ($order->deliveryAssignment()->exists()) {
                abort(422, 'This order has already been assigned.');
            }

            // ==================== Create Assignment ====================

            DeliveryAssignment::create([
                'order_id' => $order->id,
                'delivery_user_id' => $request->user()->id,
                'assigned_by' => null,
                'type' => 'normal',
                'taken_at' => now(),
            ]);

            // ==================== Update Order Status ====================

            $order->update([
                'status' => 'preparing',
            ]);

            // ==================== Record Status History ====================

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'preparing',
                'changed_by' => $request->user()->id,
                'note' => 'Order taken by delivery.',
                'created_at' => now(),
            ]);
        });

        // ==================== Return ====================

        return back()->with(
            'success',
            "Order #{$order->id} has been taken successfully.",
        );
    }

    // ==================== Update Delivery Status ====================

    public function updateStatus(Request $request, Order $order)
    {
        // ==================== Validate Status ====================

        $validated = $request->validate([
            'status' => [
                'required',
                'string',
                'in:on_the_way,at_the_door,delivered',
            ],
        ]);

        // ==================== Update Status Transaction ====================

        DB::transaction(function () use ($request, $order, $validated) {
            // ==================== Lock Order ====================

            $order = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            // ==================== Get Assignment ====================

            $assignment = $order->deliveryAssignment;

            // ==================== Verify Assignment ====================

            if (
                !$assignment ||
                $assignment->delivery_user_id !== $request->user()->id
            ) {
                abort(403);
            }

            // ==================== Validate Status Transition ====================

            $allowedTransitions = [
                'preparing' => ['on_the_way'],
                'on_the_way' => ['at_the_door'],
                'at_the_door' => ['delivered'],
            ];

            $currentStatus = $order->status;
            $newStatus = $validated['status'];

            if (
                !isset($allowedTransitions[$currentStatus]) ||
                !in_array(
                    $newStatus,
                    $allowedTransitions[$currentStatus],
                    true
                )
            ) {
                abort(
                    422,
                    'This status change is not allowed.'
                );
            }

            // ==================== Update Order ====================

            $order->update([
                'status' => $newStatus,
            ]);

            // ==================== Record Status History ====================

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $newStatus,
                'changed_by' => $request->user()->id,
                'note' => null,
                'created_at' => now(),
            ]);

            // ==================== Complete Assignment ====================

            if ($newStatus === 'delivered') {
                $assignment->update([
                    'completed_at' => now(),
                ]);
            }
        });

        // ==================== Return ====================

        return back()->with(
            'success',
            "Order #{$order->id} status updated successfully.",
        );
    }
}
