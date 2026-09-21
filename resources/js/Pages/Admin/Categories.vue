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
        <div class="space-y-6">
            <!-- ==================== Page Header ==================== -->

            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
            >
                <div>
                    <p
                        class="text-xs font-bold uppercase tracking-[0.16em] text-brand-600"
                    >
                        Catalog
                    </p>

                    <h1
                        class="mt-1 text-2xl font-bold tracking-tight text-heading"
                    >
                        Categories
                    </h1>

                    <p class="mt-1 max-w-2xl text-sm text-muted">
                        Manage product categories and subcategories.
                    </p>
                </div>

                <Link
                    href="/admin/categories/create"
                    class="inline-flex w-fit items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2"
                >
                    <Plus class="h-4 w-4" />
                    Add Category
                </Link>
            </div>

            <!-- ==================== Summary ==================== -->

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                        >
                            <FolderTree class="h-5 w-5" />
                        </div>

                        <div>
                            <p
                                class="text-xs font-bold uppercase tracking-wide text-muted"
                            >
                                Total Categories
                            </p>

                            <p class="mt-0.5 text-xl font-bold text-heading">
                                {{ categories.length }}
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-50 text-green-600"
                        >
                            <FolderTree class="h-5 w-5" />
                        </div>

                        <div>
                            <p
                                class="text-xs font-bold uppercase tracking-wide text-muted"
                            >
                                Active
                            </p>

                            <p class="mt-0.5 text-xl font-bold text-heading">
                                {{
                                    categories.filter(
                                        (category) => category.is_active,
                                    ).length
                                }}
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-purple-50 text-purple-600"
                        >
                            <FolderTree class="h-5 w-5" />
                        </div>

                        <div>
                            <p
                                class="text-xs font-bold uppercase tracking-wide text-muted"
                            >
                                Main Categories
                            </p>

                            <p class="mt-0.5 text-xl font-bold text-heading">
                                {{
                                    categories.filter(
                                        (category) => !category.parent,
                                    ).length
                                }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==================== Empty State ==================== -->

            <div
                v-if="categories.length === 0"
                class="rounded-2xl border border-border bg-surface px-6 py-14 text-center shadow-sm"
            >
                <div
                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"
                >
                    <FolderTree class="h-6 w-6" />
                </div>

                <h2 class="mt-4 text-base font-semibold text-heading">
                    No categories yet
                </h2>

                <p class="mx-auto mt-1 max-w-md text-sm text-muted">
                    Create your first product category to organize the store.
                </p>

                <Link
                    href="/admin/categories/create"
                    class="mt-5 inline-flex items-center gap-2 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-brand-700"
                >
                    <Plus class="h-4 w-4" />
                    Add Category
                </Link>
            </div>

            <!-- ==================== Desktop Table ==================== -->

            <div
                v-else
                class="hidden overflow-hidden rounded-2xl border border-border bg-surface shadow-sm md:block"
            >
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[850px] text-left">
                        <thead class="border-b border-border bg-background">
                            <tr>
                                <th
                                    class="px-6 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    Category
                                </th>

                                <th
                                    class="px-6 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    Parent
                                </th>

                                <th
                                    class="px-6 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    Products
                                </th>

                                <th
                                    class="px-6 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    Status
                                </th>

                                <th
                                    class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    Action
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-border">
                            <tr
                                v-for="category in categories"
                                :key="category.id"
                                class="transition-colors hover:bg-background"
                            >
                                <!-- ==================== Category ==================== -->

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-background ring-1 ring-border"
                                        >
                                            <img
                                                v-if="category.image"
                                                :src="`/storage/${category.image}`"
                                                :alt="category.name"
                                                class="h-full w-full object-cover"
                                            />

                                            <FolderTree
                                                v-else
                                                class="h-5 w-5 text-muted"
                                            />
                                        </div>

                                        <div class="min-w-0">
                                            <p
                                                class="truncate text-sm font-bold text-heading"
                                            >
                                                {{ category.name }}
                                            </p>

                                            <p
                                                v-if="category.description"
                                                class="mt-0.5 max-w-xs truncate text-xs text-muted"
                                            >
                                                {{ category.description }}
                                            </p>

                                            <p
                                                v-else
                                                class="mt-0.5 text-xs text-muted"
                                            >
                                                Category #{{ category.id }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- ==================== Parent ==================== -->

                                <td class="px-6 py-4">
                                    <span
                                        v-if="category.parent"
                                        class="inline-flex rounded-lg bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-700"
                                    >
                                        {{ category.parent.name }}
                                    </span>

                                    <span
                                        v-else
                                        class="text-sm font-medium text-body"
                                    >
                                        Main Category
                                    </span>
                                </td>

                                <!-- ==================== Products ==================== -->

                                <td class="px-6 py-4">
                                    <span
                                        class="text-sm font-bold text-heading"
                                    >
                                        {{ category.products_count }}
                                    </span>

                                    <span class="ml-1 text-xs text-muted">
                                        {{
                                            category.products_count === 1
                                                ? "product"
                                                : "products"
                                        }}
                                    </span>
                                </td>

                                <!-- ==================== Status ==================== -->

                                <td class="px-6 py-4">
                                    <span
                                        v-if="category.is_active"
                                        class="inline-flex rounded-full border border-green-100 bg-green-50 px-3 py-1 text-xs font-bold text-green-700"
                                    >
                                        Active
                                    </span>

                                    <span
                                        v-else
                                        class="inline-flex rounded-full border border-border bg-background px-3 py-1 text-xs font-bold text-muted"
                                    >
                                        Inactive
                                    </span>
                                </td>

                                <!-- ==================== Action ==================== -->

                                <td class="px-6 py-4 text-right">
                                    <Link
                                        :href="`/admin/categories/${category.id}`"
                                        class="inline-flex items-center rounded-xl border border-border bg-surface px-3.5 py-2 text-sm font-semibold text-body transition hover:border-brand-100 hover:bg-brand-50 hover:text-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2"
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

            <div v-if="categories.length > 0" class="space-y-4 md:hidden">
                <div
                    v-for="category in categories"
                    :key="category.id"
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm"
                >
                    <!-- ==================== Card Header ==================== -->

                    <div class="flex items-start justify-between gap-3">
                        <div class="flex min-w-0 items-center gap-3">
                            <div
                                class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-background ring-1 ring-border"
                            >
                                <img
                                    v-if="category.image"
                                    :src="`/storage/${category.image}`"
                                    :alt="category.name"
                                    class="h-full w-full object-cover"
                                />

                                <FolderTree v-else class="h-5 w-5 text-muted" />
                            </div>

                            <div class="min-w-0">
                                <p class="truncate font-semibold text-heading">
                                    {{ category.name }}
                                </p>

                                <p class="mt-0.5 truncate text-xs text-muted">
                                    {{
                                        category.parent?.name || "Main Category"
                                    }}
                                </p>
                            </div>
                        </div>

                        <span
                            v-if="category.is_active"
                            class="shrink-0 rounded-full border border-green-100 bg-green-50 px-2.5 py-1 text-xs font-bold text-green-700"
                        >
                            Active
                        </span>

                        <span
                            v-else
                            class="shrink-0 rounded-full border border-border bg-background px-2.5 py-1 text-xs font-bold text-muted"
                        >
                            Inactive
                        </span>
                    </div>

                    <!-- ==================== Description ==================== -->

                    <p
                        v-if="category.description"
                        class="mt-4 line-clamp-2 text-sm leading-5 text-body"
                    >
                        {{ category.description }}
                    </p>

                    <!-- ==================== Category Stats ==================== -->

                    <div
                        class="mt-4 grid grid-cols-2 gap-3 border-t border-border pt-4"
                    >
                        <div class="rounded-xl bg-background p-3">
                            <p
                                class="text-[11px] font-bold uppercase tracking-wide text-muted"
                            >
                                Products
                            </p>

                            <p class="mt-1 text-sm font-bold text-heading">
                                {{ category.products_count }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-background p-3">
                            <p
                                class="text-[11px] font-bold uppercase tracking-wide text-muted"
                            >
                                Type
                            </p>

                            <p
                                class="mt-1 truncate text-sm font-semibold text-heading"
                            >
                                {{
                                    category.parent
                                        ? "Subcategory"
                                        : "Main Category"
                                }}
                            </p>
                        </div>
                    </div>

                    <!-- ==================== View Button ==================== -->

                    <Link
                        :href="`/admin/categories/${category.id}`"
                        class="mt-4 flex w-full items-center justify-center rounded-xl bg-brand-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2"
                    >
                        View Category
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
