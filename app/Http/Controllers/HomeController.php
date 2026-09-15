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
        $products = Product::query()
            ->where('is_active', true)
            ->with('images')
            ->latest()
            ->get();

        return inertia('home', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }
}
