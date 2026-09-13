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
        $products = $category
            ->products()
            ->with('images')
            ->where('is_active', true)
            ->get();;

        // ==================== Return Category Page ====================

        return inertia('Categories/CategoryDetails', [
            'category' => $category,
            'products' => $products,
        ]);
    }
}
