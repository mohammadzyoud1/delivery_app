<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // ==================== Show Cart ====================

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

        // ==================== Return Cart Page ====================

        return inertia('Cart/Cart', [
            'cart' => $cart,
        ]);
    }

    // ==================== Add Item To Cart ====================

    public function store(Request $request)
    {
        // ==================== Validate Request ====================

        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        // ==================== Load Product ====================

        $product = Product::query()
            ->where('id', $validated['product_id'])
            ->where('is_active', true)
            ->with('variants')
            ->firstOrFail();

        // ==================== Determine Stock ====================

        $variant = null;

        if ($validated['variant_id']) {
            $variant = $product
                ->variants
                ->firstWhere('id', $validated['variant_id']);

            if (!$variant) {
                abort(422, 'Invalid product variant.');
            }

            $stock = $variant->stock;
        } else {
            $stock = $product->stock;
        }

        // ==================== Check Stock ====================

        if ($stock < $validated['quantity']) {
            return back()->withErrors([
                'quantity' => 'Not enough stock available.',
            ]);
        }

        // ==================== Get Or Create Cart ====================

        $cart = Cart::firstOrCreate([
            'user_id' => $request->user()->id,
        ]);

        // ==================== Find Existing Item ====================

        $cartItem = $cart
            ->items()
            ->where('product_id', $product->id)
            ->where('variant_id', $validated['variant_id'])
            ->first();

        // ==================== Add Or Increase Item ====================

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $validated['quantity'];

            if ($newQuantity > $stock) {
                return back()->withErrors([
                    'quantity' => 'Not enough stock available for this quantity.',
                ]);
            }

            $cartItem->update([
                'quantity' => $newQuantity,
            ]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'variant_id' => $validated['variant_id'],
                'quantity' => $validated['quantity'],
            ]);
        }

        // ==================== Success ====================

        return back()->with('success', 'Product added to cart.');
    }

    // ==================== Update Cart Item ====================

    public function update(Request $request, CartItem $cartItem)
    {
        // ==================== Verify Ownership ====================

        if ($cartItem->cart->user_id !== $request->user()->id) {
            abort(403);
        }

        // ==================== Validate Quantity ====================

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        // ==================== Load Product Stock ====================

        $cartItem->load([
            'product',
            'variant',
        ]);

        $stock = $cartItem->variant
            ? $cartItem->variant->stock
            : $cartItem->product->stock;

        // ==================== Check Stock ====================

        $isReducingQuantity =
            $validated['quantity'] < $cartItem->quantity;

        if (!$isReducingQuantity && $validated['quantity'] > $stock) {
            return back()->withErrors([
                'quantity' => "Only {$stock} available.",
            ]);
        }

        // ==================== Update Quantity ====================

        $cartItem->update([
            'quantity' => $validated['quantity'],
        ]);

        return back()->with('success', 'Cart updated successfully.');
    }

    // ==================== Remove Cart Item ====================

    public function destroy(Request $request, CartItem $cartItem)
    {
        // ==================== Verify Ownership ====================

        if ($cartItem->cart->user_id !== $request->user()->id) {
            abort(403);
        }

        // ==================== Delete Item ====================

        $cartItem->delete();

        return back()->with('success', 'Item removed from cart.');
    }
}
