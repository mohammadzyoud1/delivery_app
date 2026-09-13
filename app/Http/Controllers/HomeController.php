<?php

namespace App\Http\Controllers;

use App\Models\Category;

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

        return inertia('home', [
            'categories' => $categories,
        ]);
    }
}
