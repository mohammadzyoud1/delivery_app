<?php

namespace App\Http\Controllers;

use App\Models\Product;

class CatalogController extends Controller
{
    // ==================== Product Details ====================

    public function show(Product $product)
    {
        // ==================== Active Product Check ====================

        if (!$product->is_active) {
            abort(404);
        }

        // ==================== Load Product Data ====================

        $product->load([
            'images',
            'category',
            'options.values',
            'variants.variantValues.optionValue',
            'seller',
        ]);

        return inertia('Products/ProductDetails', [
            'product' => $product,
        ]);
    }
}
