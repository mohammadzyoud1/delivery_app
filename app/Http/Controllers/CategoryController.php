<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    // ==================== Category Page ====================

    public function show(string $category)
    {
        $category = Category::where('name', $category)
            ->where('is_active', true)
            ->firstOrFail();
        // ==================== Available Products ====================

        $products = $category
            ->products()
            ->where('is_active', true)
            ->where(function ($query) {
                // ==================== Products Without Variants ====================

                $query
                    ->where(function ($query) {
                        $query
                            ->whereDoesntHave('variants')
                            ->where('stock', '>', 0);
                    })
                    // ==================== Products With Available Variants ====================
                    ->orWhere(function ($query) {
                        $query->whereHas('variants', function ($variantQuery) {
                            $variantQuery->where('stock', '>', 0);
                        });
                    });
            })
            ->with([
                'images',
                'variants',
            ])
            ->get();

        // ==================== Return Category Page ====================

        return inertia('Categories/CategoryDetails', [
            'category' => $category,
            'products' => $products,
        ]);
    }
}
