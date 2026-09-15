<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\ProductVariant;
use App\Models\ProductVariantValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // ==================== My Products ====================

    public function index(Request $request)
    {
        $products = $request
            ->user()
            ->products()
            ->with('images')
            ->latest()
            ->get();
        // ==================== Product Filter ====================

        $filter = $request->query('status', 'all');

        if (!in_array($filter, ['all', 'active', 'inactive'])) {
            $filter = 'all';
        }
        return inertia('Seller/Products/ProductList', [
            'products' => $products,
            'initialFilter' => $filter,
        ]);
    }

    public function create()
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return inertia('Seller/Products/AddProduct', [
            'categories' => $categories,
        ]);
    }

    // ==================== Store Product ====================

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'unit' => ['nullable', 'string', 'max:50'],
            // ==================== Product Images ====================
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:2048'],
            // ==================== Product Options ====================
            'options' => ['nullable', 'array'],
            'options.*.name' => ['required', 'string', 'max:255'],
            'options.*.values' => ['required', 'array', 'min:1'],
            'options.*.values.*' => ['required', 'string', 'max:255'],
            // ==================== Product Variants ====================
            'variants' => ['nullable', 'array'],
            'variants.*.sku' => ['nullable', 'string', 'max:255'],
            'variants.*.price' => ['required', 'numeric', 'min:0'],
            'variants.*.stock' => ['required', 'integer', 'min:0'],
            'variants.*.values' => ['required', 'array', 'min:1'],
            'variants.*.values.*' => ['required', 'string', 'max:255'],
        ]);

        $options = $validated['options'] ?? [];
        $variants = $validated['variants'] ?? [];
        if (!empty($variants)) {
            $validated['stock'] = collect($variants)->sum(
                fn($variant) => (int) $variant['stock']
            );
        }

        if (!empty($variants) && empty($options)) {
            return back()->withErrors([
                'variants' => 'Variants require product options.',
            ]);
        }

        if (!empty($variants)) {
            foreach ($variants as $variant) {
                if (count($variant['values']) !== count($options)) {
                    return back()->withErrors([
                        'variants' => 'Variant values do not match the product options.',
                    ]);
                }
            }
        }
        // ==================== Database Transaction ====================

        DB::transaction(function () use ($request, $validated) {
            // ==================== Create Product ====================

            $product = Product::create([
                'seller_id' => $request->user()->id,
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'stock' => $validated['stock'],
                'unit' => $validated['unit'] ?? null,
                'is_active' => true,
            ]);

            // ==================== Store Product Images ====================

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('products', 'public');

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => $path,
                        'sort_order' => $index,
                    ]);
                }
            }

            // ==================== Store Product Options ====================

            $optionValueMap = [];
            $createdOptions = [];

            foreach ($validated['options'] ?? [] as $optionIndex => $optionData) {
                $option = ProductOption::create([
                    'product_id' => $product->id,
                    'name' => $optionData['name'],
                ]);

                $createdOptions[$optionIndex] = $option;

                foreach ($optionData['values'] as $value) {
                    $optionValue = ProductOptionValue::create([
                        'product_option_id' => $option->id,
                        'value' => $value,
                    ]);

                    $optionValueMap[$optionIndex][$value] =
                        $optionValue->id;
                }
            }

            // ==================== Store Product Variants ====================

            foreach ($validated['variants'] ?? [] as $variantData) {
                // ==================== Create Variant ====================

                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $variantData['sku'] ?? null,
                    'price' => $variantData['price'],
                    'stock' => $variantData['stock'],
                ]);

                // ==================== Store Variant Values ====================

                foreach ($variantData['values'] as $index => $value) {
                    $option = $createdOptions[$index] ?? null;

                    if (!$option) {
                        continue;
                    }

                    $optionValueId =
                        $optionValueMap[$index][$value] ?? null;

                    if (!$optionValueId) {
                        continue;
                    }

                    ProductVariantValue::create([
                        'product_variant_id' => $variant->id,
                        'product_option_id' => $option->id,
                        'product_option_value_id' => $optionValueId,
                    ]);
                }
            }
        });

        // ==================== Redirect ====================

        return redirect()
            ->route('seller.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function dashboard(Request $request)
    {
        // ==================== Product Statistics ====================
        $allproductCount = $request
            ->user()
            ->products()
            ->count();
        $activeProductCount = $request
            ->user()
            ->products()
            ->where('is_active', true)
            ->count();
        // ==================== Order Statistics ====================
        $orderCount = OrderItem::query()
            ->where('seller_id', $request->user()->id)
            ->distinct('order_id')
            ->count('order_id');

        // ==================== Sales Statistics ====================

        $completedItems = OrderItem::query()
            ->where('seller_id', $request->user()->id)
            ->whereHas('order', function ($query) {
                $query->where('status', 'delivered');
            });

        $productsSold = (clone $completedItems)->sum('quantity');

        $revenue = (clone $completedItems)->sum('subtotal');

        return inertia('Seller/Dashboard/SellerDashboard', [
            'allProductCount' => $allproductCount,
            'activeProductCount' => $activeProductCount,
            'orderCount' => $orderCount,
            'productsSold' => $productsSold,
            'revenue' => $revenue,
        ]);
    }

    // ==================== Seller Orders ====================

    public function orders(Request $request)
    {
        $user = $request->user();

        $orderItems = OrderItem::query()
            ->where('seller_id', $user->id)
            ->with([
                'order',
                'product',
                'variant',
            ])
            ->latest()
            ->get();

        return inertia('Seller/Dashboard/SellerOrders', [
            'orderItems' => $orderItems,
        ]);
    }

    // ==================== Products Sold ====================

    public function productsSold(Request $request)
    {
        $user = $request->user();

        $soldItems = OrderItem::query()
            ->where('seller_id', $user->id)
            ->whereHas('order', function ($query) {
                $query->where('status', 'delivered');
            })
            ->with([
                'product.images',
                'variant',
                'order',
            ])
            ->latest()
            ->get();

        return inertia('Seller/Dashboard/ProductsSold', [
            'soldItems' => $soldItems,
        ]);
    }

    // ==================== Revenue ====================

    public function revenue(Request $request)
    {
        $user = $request->user();

        $revenueItems = OrderItem::query()
            ->where('seller_id', $user->id)
            ->whereHas('order', function ($query) {
                $query->where('status', 'delivered');
            })
            ->with('order')
            ->latest()
            ->get();

        return inertia('Seller/Dashboard/Revenue', [
            'revenueItems' => $revenueItems,
        ]);
    }

    // ==================== Edit Product ====================

    public function edit(Request $request, Product $product)
    {
        // ==================== Seller Ownership Check ====================

        if ($product->seller_id !== $request->user()->id) {
            abort(403);
        }

        // ==================== Load Product Data ====================

        $product->load([
            'images',
            'options.values',
            'variants.variantValues.optionValue',
        ]);

        // ==================== Load Categories ====================

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // ==================== Return Edit Page ====================

        return inertia('Seller/Products/EditProduct', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    // ==================== Update Product ====================

    public function update(
        Request $request,
        Product $product
    ) {
        // ==================== Seller Ownership Check ====================

        if ($product->seller_id !== $request->user()->id) {
            abort(403);
        }

        // ==================== Validate Product ====================

        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'unit' => ['nullable', 'string', 'max:50'],
            'is_active' => ['required', 'boolean'],
            // ==================== Existing Images ====================
            'removed_image_ids' => ['nullable', 'array'],
            'removed_image_ids.*' => [
                'integer',
                'exists:product_images,id',
            ],
            // ==================== New Images ====================
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:2048'],
            // ==================== Product Options ====================
            'options' => ['nullable', 'array'],
            'options.*.name' => ['required', 'string', 'max:255'],
            'options.*.values' => ['required', 'array', 'min:1'],
            'options.*.values.*' => [
                'required',
                'string',
                'max:255',
            ],
            // ==================== Product Variants ====================
            'variants' => ['nullable', 'array'],
            'variants.*.id' => [
                'nullable',
                'integer',
                'exists:product_variants,id',
            ],
            'variants.*.sku' => [
                'nullable',
                'string',
                'max:255',
            ],
            'variants.*.price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'variants.*.stock' => [
                'required',
                'integer',
                'min:0',
            ],
            'variants.*.values' => [
                'required',
                'array',
                'min:1',
            ],
            'variants.*.values.*' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        // ==================== Prepare Options / Variants ====================

        $options = $validated['options'] ?? [];
        $variants = $validated['variants'] ?? [];

        // ==================== Validate Variant Structure ====================

        if (!empty($variants) && empty($options)) {
            return back()->withErrors([
                'variants' => 'Variants require product options.',
            ]);
        }

        if (!empty($variants)) {
            foreach ($variants as $variant) {
                if (count($variant['values']) !== count($options)) {
                    return back()->withErrors([
                        'variants' =>
                            'Variant values do not match the product options.',
                    ]);
                }
            }

            // ==================== Calculate Variant Stock ====================

            $validated['stock'] = collect($variants)->sum(
                fn($variant) => (int) $variant['stock']
            );
        }

        // ==================== Database Transaction ====================

        DB::transaction(function () use (
            $request,
            $product,
            $validated,
            $options,
            $variants
        ) {
            // ==================== Update Product ====================

            $product->update([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'stock' => $validated['stock'] ?? null,
                'unit' => $validated['unit'] ?? null,
                'is_active' => $validated['is_active'],
            ]);

            // ==================== Remove Existing Images ====================

            $removedImageIds = $validated['removed_image_ids'] ?? [];

            if (!empty($removedImageIds)) {
                $imagesToRemove = $product
                    ->images()
                    ->whereIn('id', $removedImageIds)
                    ->get();

                foreach ($imagesToRemove as $image) {
                    Storage::disk('public')->delete($image->image);
                    $image->delete();
                }
            }

            // ==================== Store New Images ====================

            if ($request->hasFile('images')) {
                $currentImageCount = $product->images()->count();

                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('products', 'public');

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => $path,
                        'sort_order' => $currentImageCount + $index,
                    ]);
                }
            }
            // ==================== Store Existing Option IDs ====================

            $oldOptionIds = $product
                ->options()
                ->pluck('id')
                ->all();

            // ==================== Create Updated Options ====================

            $optionValueMap = [];
            $createdOptions = [];

            foreach ($options as $optionIndex => $optionData) {
                $option = ProductOption::create([
                    'product_id' => $product->id,
                    'name' => $optionData['name'],
                ]);

                $createdOptions[$optionIndex] = $option;

                foreach ($optionData['values'] as $value) {
                    $optionValue = ProductOptionValue::create([
                        'product_option_id' => $option->id,
                        'value' => $value,
                    ]);

                    $optionValueMap[$optionIndex][$value] = $optionValue->id;
                }
            }

            // ==================== Existing Variant IDs ====================

            $submittedVariantIds = collect($variants)
                ->pluck('id')
                ->filter()
                ->map(fn($id) => (int) $id)
                ->all();

            // ==================== Remove Deleted Variants ====================

            $product
                ->variants()
                ->whereNotIn('id', $submittedVariantIds)
                ->get()
                ->each(function ($variant) {
                    $variant->variantValues()->delete();
                    $variant->delete();
                });

            // ==================== Update / Create Variants ====================

            foreach ($variants as $variantData) {
                $variant = null;

                if (!empty($variantData['id'])) {
                    $variant = ProductVariant::query()
                        ->where('id', $variantData['id'])
                        ->where('product_id', $product->id)
                        ->first();
                }

                // ==================== Update Existing Variant ====================

                if ($variant) {
                    $variant->update([
                        'sku' => $variantData['sku'] ?? null,
                        'price' => $variantData['price'],
                        'stock' => $variantData['stock'],
                    ]);
                }

                // ==================== Create New Variant ====================

                if (!$variant) {
                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => $variantData['sku'] ?? null,
                        'price' => $variantData['price'],
                        'stock' => $variantData['stock'],
                    ]);
                }

                // ==================== Replace Variant Value Links ====================

                $variant->variantValues()->delete();

                foreach ($variantData['values'] as $index => $value) {
                    $option = $createdOptions[$index] ?? null;

                    if (!$option) {
                        continue;
                    }

                    $optionValueId = $optionValueMap[$index][$value] ?? null;

                    if (!$optionValueId) {
                        continue;
                    }

                    ProductVariantValue::create([
                        'product_variant_id' => $variant->id,
                        'product_option_id' => $option->id,
                        'product_option_value_id' => $optionValueId,
                    ]);
                }
            }

            // ==================== Remove Old Options ====================

            foreach ($oldOptionIds as $oldOptionId) {
                $oldOption = ProductOption::find($oldOptionId);

                if (!$oldOption) {
                    continue;
                }

                $oldOption->values()->delete();
                $oldOption->delete();
            }
        });

        // ==================== Redirect ====================

        return redirect()
            ->route('seller.products.index')
            ->with('success', 'Product updated successfully.');
    }

    // ==================== Toggle Product Status ====================

    public function toggleStatus(Request $request, Product $product)
    {
        // ==================== Seller Ownership Check ====================

        if ($product->seller_id !== $request->user()->id) {
            abort(403);
        }

        // ==================== Toggle Active Status ====================

        $product->update([
            'is_active' => !$product->is_active,
        ]);

        return back()->with(
            'success',
            $product->is_active
                ? 'Product activated successfully.'
                : 'Product deactivated successfully.'
        );
    }
}
