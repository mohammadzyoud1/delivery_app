<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    // ==================== Admin Categories ====================

    public function index()
    {
        // ==================== Load Categories ====================

        $categories = Category::query()
            ->with('parent')
            ->withCount('products')
            ->latest()
            ->get();

        // ==================== Return Categories Page ====================

        return inertia('Admin/Categories', [
            'categories' => $categories,
        ]);
    }

    // ==================== Category Details ====================

    public function show(Category $category)
    {
        // ==================== Load Category Data ====================

        $category->load([
            'parent',
            'children',
            'products',
        ]);

        // ==================== Return Category Details ====================

        return inertia('Admin/CategoryDetails', [
            'category' => $category,
        ]);
    }

    // ==================== Create Category ====================

    public function create()
    {
        // ==================== Load Parent Categories ====================

        $parentCategories = Category::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        // ==================== Return Create Page ====================

        return inertia('Admin/CategoryCreate', [
            'parentCategories' => $parentCategories,
        ]);
    }

    // ==================== Store Category ====================

    public function store(Request $request)
    {
        // ==================== Validate Category ====================

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:categories,id',
            ],
            'image' => [
                'nullable',
                'image',
                'max:2048',
            ],
        ]);

        // ==================== Store Image ====================

        if ($request->hasFile('image')) {
            $validated['image'] = $request
                ->file('image')
                ->store('categories', 'public');
        }

        // ==================== Create Category ====================

        Category::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
            'image' => $validated['image'] ?? null,
            'is_active' => true,
        ]);

        // ==================== Return Success ====================

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }
}
