<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class CheckoutController extends Controller
{
    // ==================== Checkout Page ====================

    public function index(Request $request)
    {
        // ==================== Load User Cart ====================

        $cart = $request
            ->user()
            ->cart()
            ->with([
                'items.product.images',
                'items.variant.variantValues.optionValue',
            ])
            ->first();

        // ==================== Empty Cart Check ====================

        if (!$cart || !$cart->items->count()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        // ==================== Return Checkout Page ====================

        return inertia('Cart/Checkout', [
            'cart' => $cart,
        ]);
    }

    public function store(Request $request)
    {
        // ==================== Validate Checkout ====================

        $validated = $request->validate([
            'delivery_address' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        // ==================== Create Order Transaction ====================
        try {
            $order = DB::transaction(function () use ($request, $validated) {
                // ==================== Load Cart ====================

                $cart = $request
                    ->user()
                    ->cart()
                    ->with([
                        'items.product',
                        'items.variant.variantValues.optionValue',
                    ])
                    ->first();

                if (!$cart || !$cart->items->count()) {
                    throw new RuntimeException('Your cart is empty.');
                }

                // ==================== Check Stock & Prepare Items ====================

                $orderItems = [];
                $totalAmount = 0;

                foreach ($cart->items as $cartItem) {
                    // ==================== Lock Product ====================

                    $product = Product::query()
                        ->where('id', $cartItem->product_id)
                        ->lockForUpdate()
                        ->first();

                    if (!$product || !$product->is_active) {
                        throw new RuntimeException(
                            "The product \"{$cartItem->product->name}\" is no longer available.",
                        );
                    }

                    // ==================== Product With Variant ====================

                    if ($cartItem->variant_id) {
                        $variant = ProductVariant::query()
                            ->with('variantValues.optionValue')
                            ->where('id', $cartItem->variant_id)
                            ->where('product_id', $product->id)
                            ->lockForUpdate()
                            ->first();

                        if (!$variant) {
                            throw new RuntimeException(
                                'One of the selected product variants is no longer available.',
                            );
                        }

                        // ==================== Check Variant Stock ====================

                        if ($variant->stock < $cartItem->quantity) {
                            throw new RuntimeException(
                                "Not enough stock available for \"{$product->name}\".",
                            );
                        }

                        $unitPrice = (float) $variant->price;

                        // ==================== Variant Name Snapshot ====================

                        $variantName = $variant
                            ->variantValues
                            ->map(function ($variantValue) {
                                return $variantValue->optionValue?->value;
                            })
                            ->filter()
                            ->implode(' / ');

                        // ==================== Reduce Variant Stock ====================

                        $variant->decrement('stock', $cartItem->quantity);
                    } else {
                        // ==================== Check Product Stock ====================

                        if ($product->stock < $cartItem->quantity) {
                            throw new RuntimeException(
                                "Not enough stock available for \"{$product->name}\".",
                            );
                        }

                        $unitPrice = (float) $product->price;
                        $variantName = null;

                        // ==================== Reduce Product Stock ====================

                        $product->decrement('stock', $cartItem->quantity);
                    }

                    // ==================== Calculate Item Subtotal ====================

                    $subtotal = $unitPrice * $cartItem->quantity;

                    $totalAmount += $subtotal;

                    // ==================== Prepare Order Item ====================

                    $orderItems[] = [
                        'product_id' => $product->id,
                        'variant_id' => $cartItem->variant_id,
                        'variant_name' => $variantName,
                        'seller_id' => $product->seller_id,
                        'quantity' => $cartItem->quantity,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                    ];
                }

                // ==================== Create Order ====================

                $order = Order::create([
                    'customer_id' => $request->user()->id,
                    'delivery_address' => $validated['delivery_address'],
                    'status' => 'accepted',
                    'custom_request' => null,
                    'rejection_reason' => null,
                    'total_amount' => $totalAmount,
                    'delivery_locked' => false,
                ]);

                // ==================== Create Order Items ====================

                $order->items()->createMany($orderItems);

                // ==================== Create Initial Status History ====================

                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'status' => 'accepted',
                    'changed_by' => $request->user()->id,
                    'note' => 'Order placed successfully.',
                    'created_at' => now(),
                ]);

                // ==================== Clear Cart ====================

                $cart->items()->delete();

                return $order;
            });
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('cart.index')
                ->with('error', $exception->getMessage());
        }

        // ==================== Order Created ====================

        return redirect()
            ->route('cart.index')
            ->with(
                'success',
                "Order #{$order->id} placed successfully.",
            );
    }
}
