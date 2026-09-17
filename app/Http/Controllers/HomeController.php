<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    // ==================== Home Page ====================

    public function index()
    {
        $categories = Category::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // ==================== Available Products ====================

        $products = Product::query()
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
            ->latest()
            ->get();

        return inertia('home', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }
}
