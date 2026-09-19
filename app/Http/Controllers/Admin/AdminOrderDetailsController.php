<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOrderDetailsController extends Controller
{
    // ==================== Admin Order Details ====================

    public function show(Request $request, Order $order)
    {
        // ==================== Load Order Data ====================

        $order->load([
            'customer',
            'items.product.images',
            'items.variant',
            'items.seller',
            'statusHistories.changedBy',
            'deliveryAssignment.deliveryUser',
            'deliveryAssignment.assignedBy',
        ]);

        // ==================== Available Delivery Users ====================

        $deliveryUsers = User::query()
            ->whereHas('roles', function ($query) {
                $query->where('name', 'delivery');
            })
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get([
                'id',
                'first_name',
                'last_name',
            ]);
        // ==================== Return Order Details ====================

        return inertia('Admin/OrderDetails', [
            'order' => $order,
            'deliveryUsers' => $deliveryUsers,
        ]);
    }

    // ==================== Reject Order ====================

    public function reject(Request $request, Order $order)
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($request, $order, $validated) {
            // ==================== Lock Order ====================

            $order = Order::query()
                ->lockForUpdate()
                ->with('items')
                ->findOrFail($order->id);

            // ==================== Validate Order Status ====================

            if ($order->status === 'rejected') {
                abort(422, 'This order has already been rejected.');
            }

            if ($order->status === 'delivered') {
                abort(422, 'A delivered order cannot be rejected.');
            }

            // ==================== Restore Stock ====================

            foreach ($order->items as $item) {
                if ($item->variant_id) {
                    $item->variant()->lockForUpdate()->firstOrFail();

                    $item->variant()->increment(
                        'stock',
                        $item->quantity
                    );
                } else {
                    $item->product()->lockForUpdate()->firstOrFail();

                    $item->product()->increment(
                        'stock',
                        $item->quantity
                    );
                }
            }

            // ==================== Reject Order ====================

            $order->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
            ]);

            // ==================== Record Status History ====================

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'rejected',
                'changed_by' => $request->user()->id,
                'note' => $validated['rejection_reason'],
                'created_at' => now(),
            ]);
        });

        // ==================== Return ====================

        return back()->with(
            'success',
            "Order #{$order->id} has been rejected successfully."
        );
    }
}
