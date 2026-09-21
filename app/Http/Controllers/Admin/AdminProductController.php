<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;

class AdminProductController extends Controller
{
    // ==================== Admin Products ====================

    public function index()
    {
        // ==================== Load Products ====================

        $products = Product::query()
            ->with([
                'seller',
                'category',
                'images',
                'variants',
            ])
            ->latest()
            ->get();

        // ==================== Return Products Page ====================

        return inertia('Admin/Products', [
            'products' => $products,
        ]);
    }

    // ==================== Product Details ====================

    public function show(Product $product)
    {
        // ==================== Load Product Data ====================

        $product->load([
            'seller',
            'category',
            'images',
            'options.values',
            'variants.variantValues.option',
            'variants.variantValues.optionValue',
        ]);

        // ==================== Return Product Details Page ====================

        return inertia('Admin/ProductDetails', [
            'product' => $product,
        ]);
    }
}
