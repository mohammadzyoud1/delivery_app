<script setup lang="ts">
// ==================== Imports ====================

import { Head, Link } from "@inertiajs/vue3";
import { ArrowLeft, FolderTree, Package } from "lucide-vue-next";
import AdminLayout from "@/Layouts/AdminLayout.vue";

// ==================== Types ====================

interface ParentCategory {
    id: number;
    name: string;
}

interface ChildCategory {
    id: number;
    name: string;
    is_active: boolean;
}

interface Product {
    id: number;
    name: string;
    price: string | number;
    stock: number;
    is_active: boolean;
}

// ==================== Props ====================

defineProps<{
    category: {
        id: number;
        name: string;
        description: string | null;
        image: string | null;
        is_active: boolean;
        parent: ParentCategory | null;
        children: ChildCategory[];
        products: Product[];
    };
}>();
</script>

<template>
    <Head title="Category Details" />

    <AdminLayout>
        <!-- ==================== Header ==================== -->

        <div class="mb-6">
            <Link
                href="/admin/categories"
                class="mb-4 inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900"
            >
                <ArrowLeft class="h-4 w-4" />
                Back to Categories
            </Link>

            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
            >
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-gray-100"
                    >
                        <img
                            v-if="category.image"
                            :src="`/storage/${category.image}`"
                            alt=""
                            class="h-full w-full object-cover"
                        />

                        <FolderTree v-else class="h-6 w-6 text-gray-400" />
                    </div>

                    <div>
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-gray-400"
                        >
                            Category Details
                        </p>

                        <h1 class="mt-1 text-2xl font-bold text-gray-900">
                            {{ category.name }}
                        </h1>
                    </div>
                </div>

                <span
                    class="w-fit rounded-full px-3 py-1 text-xs font-semibold"
                    :class="
                        category.is_active
                            ? 'bg-green-50 text-green-700'
                            : 'bg-gray-100 text-gray-600'
                    "
                >
                    {{ category.is_active ? "Active" : "Inactive" }}
                </span>
            </div>
        </div>

        <!-- ==================== Category Information ==================== -->

        <section class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6">
            <h2 class="font-semibold text-gray-900">Category Information</h2>

            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <div>
                    <p class="text-xs text-gray-500">Parent Category</p>

                    <p class="mt-1 text-sm font-semibold text-gray-900">
                        {{ category.parent?.name || "Main Category" }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Category ID</p>

                    <p class="mt-1 text-sm font-semibold text-gray-900">
                        #{{ category.id }}
                    </p>
                </div>
            </div>

            <div class="mt-5">
                <p class="text-xs text-gray-500">Description</p>

                <p
                    class="mt-1 whitespace-pre-line text-sm leading-6 text-gray-600"
                >
                    {{ category.description || "No description provided." }}
                </p>
            </div>
        </section>

        <!-- ==================== Subcategories ==================== -->

        <section
            class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 sm:p-6"
        >
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-gray-900">Subcategories</h2>

                    <p class="mt-1 text-sm text-gray-500">
                        {{ category.children.length }} subcategories
                    </p>
                </div>
            </div>

            <div
                v-if="category.children.length"
                class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3"
            >
                <div
                    v-for="child in category.children"
                    :key="child.id"
                    class="rounded-xl bg-gray-50 p-4"
                >
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold text-gray-900">
                            {{ child.name }}
                        </p>

                        <span
                            class="rounded-full px-2 py-1 text-[11px] font-semibold"
                            :class="
                                child.is_active
                                    ? 'bg-green-50 text-green-700'
                                    : 'bg-gray-200 text-gray-600'
                            "
                        >
                            {{ child.is_active ? "Active" : "Inactive" }}
                        </span>
                    </div>
                </div>
            </div>

            <div v-else class="mt-4 rounded-xl bg-gray-50 p-6 text-center">
                <p class="text-sm text-gray-500">No subcategories.</p>
            </div>
        </section>

        <!-- ==================== Products ==================== -->

        <section
            class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 sm:p-6"
        >
            <div class="flex items-center gap-3">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100"
                >
                    <Package class="h-5 w-5 text-gray-600" />
                </div>

                <div>
                    <h2 class="font-semibold text-gray-900">Products</h2>

                    <p class="text-sm text-gray-500">
                        {{ category.products.length }} products in this category
                    </p>
                </div>
            </div>

            <div
                v-if="category.products.length"
                class="mt-5 divide-y divide-gray-100"
            >
                <div
                    v-for="product in category.products"
                    :key="product.id"
                    class="flex items-center justify-between gap-4 py-4"
                >
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-gray-900">
                            {{ product.name }}
                        </p>

                        <p class="mt-1 text-xs text-gray-500">
                            Stock: {{ product.stock }} · Price:
                            {{ product.price }}
                        </p>
                    </div>

                    <Link
                        :href="`/admin/products/${product.id}`"
                        class="shrink-0 text-sm font-semibold text-gray-900 hover:underline"
                    >
                        View
                    </Link>
                </div>
            </div>

            <div v-else class="mt-5 rounded-xl bg-gray-50 p-6 text-center">
                <p class="text-sm text-gray-500">
                    No products in this category.
                </p>
            </div>
        </section>
    </AdminLayout>
</template>
