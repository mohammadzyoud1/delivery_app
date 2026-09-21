<script setup lang="ts">
// ==================== Imports ====================

import { Head, Link } from "@inertiajs/vue3";
import { FolderTree, Plus } from "lucide-vue-next";
import AdminLayout from "@/Layouts/AdminLayout.vue";

// ==================== Types ====================

interface ParentCategory {
    id: number;
    name: string;
}

interface Category {
    id: number;
    name: string;
    description: string | null;
    image: string | null;
    is_active: boolean;
    parent: ParentCategory | null;
    products_count: number;
}

// ==================== Props ====================

defineProps<{
    categories: Category[];
}>();
</script>

<template>
    <Head title="Categories" />

    <AdminLayout>
        <!-- ==================== Header ==================== -->

        <div
            class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <p
                    class="text-xs font-semibold uppercase tracking-wide text-gray-400"
                >
                    Catalog
                </p>

                <h1 class="mt-1 text-2xl font-bold text-gray-900">
                    Categories
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Manage product categories and subcategories.
                </p>
            </div>

            <Link
                href="/admin/categories/create"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-gray-800"
            >
                <Plus class="h-4 w-4" />
                Add Category
            </Link>
        </div>

        <!-- ==================== Empty State ==================== -->

        <div
            v-if="categories.length === 0"
            class="rounded-2xl border border-gray-200 bg-white p-10 text-center"
        >
            <FolderTree class="mx-auto h-10 w-10 text-gray-300" />

            <h2 class="mt-4 text-lg font-semibold text-gray-900">
                No categories yet
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Create your first product category.
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
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Category
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Parent
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Products
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Status
                            </th>

                            <th class="px-6 py-4"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        <tr
                            v-for="category in categories"
                            :key="category.id"
                            class="hover:bg-gray-50"
                        >
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-xl bg-gray-100"
                                    >
                                        <img
                                            v-if="category.image"
                                            :src="`/storage/${category.image}`"
                                            alt=""
                                            class="h-full w-full object-cover"
                                        />

                                        <FolderTree
                                            v-else
                                            class="h-5 w-5 text-gray-400"
                                        />
                                    </div>

                                    <div>
                                        <p
                                            class="text-sm font-semibold text-gray-900"
                                        >
                                            {{ category.name }}
                                        </p>

                                        <p
                                            v-if="category.description"
                                            class="mt-0.5 max-w-xs truncate text-xs text-gray-500"
                                        >
                                            {{ category.description }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ category.parent?.name || "Main Category" }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ category.products_count }}
                            </td>

                            <td class="px-6 py-4">
                                <span
                                    class="rounded-full px-3 py-1 text-xs font-semibold"
                                    :class="
                                        category.is_active
                                            ? 'bg-green-50 text-green-700'
                                            : 'bg-gray-100 text-gray-600'
                                    "
                                >
                                    {{
                                        category.is_active
                                            ? "Active"
                                            : "Inactive"
                                    }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <Link
                                    :href="`/admin/categories/${category.id}`"
                                    class="text-sm font-semibold text-gray-900 hover:underline"
                                >
                                    View
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ==================== Mobile Cards ==================== -->

        <div v-if="categories.length" class="space-y-3 md:hidden">
            <div
                v-for="category in categories"
                :key="category.id"
                class="rounded-2xl border border-gray-200 bg-white p-4"
            >
                <div class="flex items-start justify-between gap-3">
                    <div class="flex min-w-0 items-center gap-3">
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gray-100"
                        >
                            <img
                                v-if="category.image"
                                :src="`/storage/${category.image}`"
                                alt=""
                                class="h-full w-full object-cover"
                            />

                            <FolderTree v-else class="h-5 w-5 text-gray-400" />
                        </div>

                        <div class="min-w-0">
                            <p class="truncate font-semibold text-gray-900">
                                {{ category.name }}
                            </p>

                            <p class="mt-0.5 text-xs text-gray-500">
                                {{ category.parent?.name || "Main Category" }}
                            </p>
                        </div>
                    </div>

                    <span
                        class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold"
                        :class="
                            category.is_active
                                ? 'bg-green-50 text-green-700'
                                : 'bg-gray-100 text-gray-600'
                        "
                    >
                        {{ category.is_active ? "Active" : "Inactive" }}
                    </span>
                </div>

                <div
                    class="mt-4 flex items-center justify-between border-t border-gray-100 pt-3"
                >
                    <p class="text-sm text-gray-500">
                        {{ category.products_count }} products
                    </p>

                    <Link
                        :href="`/admin/categories/${category.id}`"
                        class="text-sm font-semibold text-gray-900"
                    >
                        View
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
