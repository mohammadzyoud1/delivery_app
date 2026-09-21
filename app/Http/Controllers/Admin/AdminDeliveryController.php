<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAssignment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDeliveryController extends Controller
{
    // ==================== Assign Delivery ====================

    public function assign(Request $request, Order $order)
    {
        $validated = $request->validate([
            'delivery_user_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'admin_note' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        DB::transaction(function () use ($request, $order, $validated) {
            // ==================== Lock Order ====================

            $order = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            // ==================== Validate Order ====================

            if ($order->status === 'rejected') {
                abort(422, 'A rejected order cannot be assigned.');
            }

            if ($order->status === 'delivered') {
                abort(422, 'A delivered order cannot be assigned.');
            }

            // ==================== Validate Delivery User ====================

            $deliveryUser = User::query()
                ->where('id', $validated['delivery_user_id'])
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'delivery');
                })
                ->first();

            if (!$deliveryUser) {
                abort(422, 'The selected user is not a delivery user.');
            }

            // ==================== Prevent Duplicate Assignment ====================

            if ($order->deliveryAssignment()->exists()) {
                abort(422, 'This order already has a delivery assignment.');
            }

            // ==================== Create Assignment ====================

            DeliveryAssignment::create([
                'order_id' => $order->id,
                'delivery_user_id' => $deliveryUser->id,
                'assigned_by' => $request->user()->id,
                'type' => 'admin',
                'admin_note' => $validated['admin_note'] ?? null,
                'taken_at' => null,
                'completed_at' => null,
            ]);

            // ==================== Lock Order ====================

            $order->update([
                'delivery_locked' => true,
            ]);
        });

        return back()->with(
            'success',
            "Order #{$order->id} has been assigned successfully."
        );
    }

    // ==================== Reassign Delivery ====================

    public function reassign(Request $request, Order $order)
    {
        $validated = $request->validate([
            'delivery_user_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'admin_note' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        DB::transaction(function () use ($request, $order, $validated) {
            // ==================== Lock Order ====================

            $order = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            // ==================== Validate Order ====================

            if ($order->status === 'rejected') {
                abort(422, 'A rejected order cannot be reassigned.');
            }

            if ($order->status === 'delivered') {
                abort(422, 'A delivered order cannot be reassigned.');
            }

            // ==================== Load Assignment ====================

            $assignment = $order->deliveryAssignment;

            if (!$assignment) {
                abort(422, 'This order does not have a delivery assignment.');
            }

            // ==================== Validate Delivery User ====================

            $deliveryUser = User::query()
                ->where('id', $validated['delivery_user_id'])
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'delivery');
                })
                ->first();

            if (!$deliveryUser) {
                abort(422, 'The selected user is not a delivery user.');
            }

            // ==================== Prevent Same User ====================

            if ($assignment->delivery_user_id === $deliveryUser->id) {
                abort(422, 'This delivery user is already assigned to the order.');
            }

            // ==================== Reassign Delivery ====================

            $assignment->update([
                'delivery_user_id' => $deliveryUser->id,
                'assigned_by' => $request->user()->id,
                'type' => 'admin',
                'admin_note' => $validated['admin_note'] ?? null,
            ]);
        });

        return back()->with(
            'success',
            "Order #{$order->id} has been reassigned successfully."
        );
    }
}
