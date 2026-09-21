<script setup lang="ts">
// ==================== Imports ====================

import { Head, Link } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Eye, Package, Store } from "lucide-vue-next";

// ==================== Interfaces ====================

interface Seller {
    id: number;
    first_name: string;
    last_name: string;
}

interface Category {
    id: number;
    name: string;
}

interface ProductImage {
    id: number;
    image: string;
}

interface ProductVariant {
    id: number;
    sku: string | null;
    price: string | number;
    stock: number;
}

interface Product {
    id: number;
    seller_id: number;
    category_id: number;
    name: string;
    description: string | null;
    price: string | number;
    stock: number;
    unit: string | null;
    is_active: boolean;
    created_at: string;
    seller: Seller;
    category: Category;
    images: ProductImage[];
    variants: ProductVariant[];
}

// ==================== Props ====================

const props = defineProps<{
    products: Product[];
}>();

// ==================== Helpers ====================

const fullName = (seller: Seller): string => {
    return `${seller.first_name} ${seller.last_name}`;
};

const productPrice = (product: Product): string => {
    if (product.variants.length > 0) {
        const prices = product.variants.map((variant) => Number(variant.price));

        const minPrice = Math.min(...prices);

        return `From ${minPrice.toFixed(2)}`;
    }

    return Number(product.price).toFixed(2);
};

const productStock = (product: Product): number => {
    if (product.variants.length > 0) {
        return product.variants.reduce(
            (total, variant) => total + variant.stock,
            0,
        );
    }

    return product.stock;
};

const formatDate = (date: string): string => {
    return new Date(date).toLocaleDateString();
};
</script>

<template>
    <Head title="Products" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- ==================== Header ==================== -->

            <div>
                <p
                    class="text-xs font-medium uppercase tracking-wide text-gray-400"
                >
                    Admin Panel
                </p>

                <h1 class="mt-1 text-2xl font-bold text-gray-900">Products</h1>

                <p class="mt-1 text-sm text-gray-500">
                    Manage products listed by sellers.
                </p>
            </div>

            <!-- ==================== Summary ==================== -->

            <div
                class="flex items-center gap-3 rounded-2xl border border-gray-200 bg-white p-5"
            >
                <div
                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-gray-100"
                >
                    <Package class="h-5 w-5 text-gray-700" />
                </div>

                <div>
                    <p class="text-xs text-gray-400">Total Products</p>

                    <p class="text-xl font-bold text-gray-900">
                        {{ products.length }}
                    </p>
                </div>
            </div>

            <!-- ==================== Empty State ==================== -->

            <div
                v-if="products.length === 0"
                class="rounded-2xl border border-gray-200 bg-white px-6 py-14 text-center"
            >
                <Package class="mx-auto h-10 w-10 text-gray-300" />

                <h2 class="mt-4 text-lg font-semibold text-gray-900">
                    No products yet
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Products created by sellers will appear here.
                </p>
            </div>

            <!-- ==================== Desktop Table ==================== -->

            <div
                v-else
                class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white md:block"
            >
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="border-b border-gray-100 bg-gray-50">
                            <tr>
                                <th
                                    class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                                >
                                    Product
                                </th>

                                <th
                                    class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                                >
                                    Seller
                                </th>

                                <th
                                    class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                                >
                                    Category
                                </th>

                                <th
                                    class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                                >
                                    Price
                                </th>

                                <th
                                    class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                                >
                                    Stock
                                </th>

                                <th
                                    class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                                >
                                    Status
                                </th>

                                <th
                                    class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wide text-gray-500"
                                >
                                    Action
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            <tr
                                v-for="product in products"
                                :key="product.id"
                                class="transition hover:bg-gray-50"
                            >
                                <!-- Product -->

                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gray-100"
                                        >
                                            <img
                                                v-if="product.images.length > 0"
                                                :src="product.images[0].image"
                                                :alt="product.name"
                                                class="h-full w-full object-cover"
                                            />

                                            <Package
                                                v-else
                                                class="h-5 w-5 text-gray-400"
                                            />
                                        </div>

                                        <div class="min-w-0">
                                            <p
                                                class="max-w-xs truncate font-medium text-gray-900"
                                            >
                                                {{ product.name }}
                                            </p>

                                            <p class="text-xs text-gray-400">
                                                #{{ product.id }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Seller -->

                                <td class="px-5 py-4">
                                    <div
                                        class="flex items-center gap-2 text-sm text-gray-700"
                                    >
                                        <Store class="h-4 w-4 text-gray-400" />

                                        {{ fullName(product.seller) }}
                                    </div>
                                </td>

                                <!-- Category -->

                                <td class="px-5 py-4 text-sm text-gray-600">
                                    {{ product.category.name }}
                                </td>

                                <!-- Price -->

                                <td
                                    class="whitespace-nowrap px-5 py-4 text-sm font-medium text-gray-900"
                                >
                                    {{ productPrice(product) }}
                                </td>

                                <!-- Stock -->

                                <td class="px-5 py-4">
                                    <span
                                        :class="
                                            productStock(product) > 0
                                                ? 'text-green-600'
                                                : 'text-red-600'
                                        "
                                        class="text-sm font-medium"
                                    >
                                        {{ productStock(product) }}
                                    </span>
                                </td>

                                <!-- Status -->

                                <td class="px-5 py-4">
                                    <span
                                        v-if="product.is_active"
                                        class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700"
                                    >
                                        Active
                                    </span>

                                    <span
                                        v-else
                                        class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600"
                                    >
                                        Inactive
                                    </span>
                                </td>

                                <!-- Action -->

                                <td class="px-5 py-4 text-right">
                                    <Link
                                        :href="`/admin/products/${product.id}`"
                                        class="inline-flex items-center gap-2 rounded-xl border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                                    >
                                        <Eye class="h-4 w-4" />
                                        View
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ==================== Mobile Cards ==================== -->

            <div v-if="products.length > 0" class="space-y-3 md:hidden">
                <div
                    v-for="product in products"
                    :key="product.id"
                    class="rounded-2xl border border-gray-200 bg-white p-4"
                >
                    <div class="flex gap-3">
                        <div
                            class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gray-100"
                        >
                            <img
                                v-if="product.images.length > 0"
                                :src="product.images[0].image"
                                :alt="product.name"
                                class="h-full w-full object-cover"
                            />

                            <Package v-else class="h-6 w-6 text-gray-400" />
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <h2
                                        class="truncate font-semibold text-gray-900"
                                    >
                                        {{ product.name }}
                                    </h2>

                                    <p class="mt-1 text-xs text-gray-400">
                                        #{{ product.id }}
                                    </p>
                                </div>

                                <span
                                    v-if="product.is_active"
                                    class="shrink-0 rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700"
                                >
                                    Active
                                </span>

                                <span
                                    v-else
                                    class="shrink-0 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600"
                                >
                                    Inactive
                                </span>
                            </div>

                            <p class="mt-2 text-sm text-gray-500">
                                {{ fullName(product.seller) }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="mt-4 grid grid-cols-3 gap-3 border-t border-gray-100 pt-4"
                    >
                        <div>
                            <p class="text-xs text-gray-400">Category</p>

                            <p
                                class="mt-1 truncate text-sm font-medium text-gray-800"
                            >
                                {{ product.category.name }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-400">Price</p>

                            <p class="mt-1 text-sm font-medium text-gray-800">
                                {{ productPrice(product) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-400">Stock</p>

                            <p
                                class="mt-1 text-sm font-medium"
                                :class="
                                    productStock(product) > 0
                                        ? 'text-green-600'
                                        : 'text-red-600'
                                "
                            >
                                {{ productStock(product) }}
                            </p>
                        </div>
                    </div>

                    <Link
                        :href="`/admin/products/${product.id}`"
                        class="mt-4 flex w-full items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-gray-800"
                    >
                        <Eye class="h-4 w-4" />
                        View Product
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
