<script setup lang="ts">
// ==================== Imports ====================

import { Head, Link } from "@inertiajs/vue3";
import { ArrowLeft, FolderTree, Package } from "lucide-vue-next";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { t } from "@/Utils/i18n";

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
        <div class="space-y-6">
            <!-- ==================== Page Header ==================== -->

            <div>
                <Link
                    href="/admin/categories"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-muted transition hover:text-brand-700"
                >
                    <ArrowLeft class="h-4 w-4" />
                    {{ t("backToCategories") }}
                </Link>

                <div
                    class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
                >
                    <div class="flex min-w-0 items-center gap-4">
                        <div
                            class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-background ring-1 ring-border"
                        >
                            <img
                                v-if="category.image"
                                :src="`/storage/${category.image}`"
                                :alt="category.name"
                                class="h-full w-full object-cover"
                            />

                            <FolderTree v-else class="h-6 w-6 text-brand-600" />
                        </div>

                        <div class="min-w-0">
                            <p
                                class="text-xs font-bold uppercase tracking-[0.16em] text-brand-600"
                            >
                                {{ t("categoryDetails") }}
                            </p>

                            <h1
                                class="mt-1 truncate text-2xl font-bold tracking-tight text-heading"
                            >
                                {{ category.name }}
                            </h1>

                            <p class="mt-1 text-sm text-muted">
                                {{ t("category") }} #{{ category.id }}
                            </p>
                        </div>
                    </div>

                    <span
                        class="inline-flex w-fit shrink-0 rounded-full px-3 py-1.5 text-xs font-bold"
                        :class="
                            category.is_active
                                ? 'border border-green-100 bg-green-50 text-green-700'
                                : 'border border-border bg-background text-muted'
                        "
                    >
                        {{ category.is_active ? t("active") : t("inactive") }}
                    </span>
                </div>
            </div>

            <!-- ==================== Category Information ==================== -->

            <section
                class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
            >
                <div>
                    <h2 class="font-semibold text-heading">
                        {{ t("categoryInfo") }}
                    </h2>

                    <p class="mt-1 text-sm text-muted">
                        {{ t("categoryInformationDescription") }}
                    </p>
                </div>

                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div
                        class="rounded-xl border border-border bg-background p-4"
                    >
                        <p
                            class="text-xs font-bold uppercase tracking-wide text-muted"
                        >
                            {{ t("parentCategory") }}
                        </p>

                        <p class="mt-2 text-sm font-semibold text-heading">
                            {{ category.parent?.name || t("mainCategory") }}
                        </p>
                    </div>

                    <div
                        class="rounded-xl border border-border bg-background p-4"
                    >
                        <p
                            class="text-xs font-bold uppercase tracking-wide text-muted"
                        >
                            {{ t("category") }}
                        </p>

                        <p class="mt-2 text-sm font-semibold text-heading">
                            #{{ category.id }}
                        </p>
                    </div>
                </div>

                <div
                    class="mt-4 rounded-xl border border-border bg-background p-4"
                >
                    <p
                        class="text-xs font-bold uppercase tracking-wide text-muted"
                    >
                        {{ t("description") }}
                    </p>

                    <p
                        class="mt-2 whitespace-pre-line text-sm leading-6 text-body"
                    >
                        {{ category.description || t("noDescriptionProvided") }}
                    </p>
                </div>
            </section>

            <!-- ==================== Subcategories ==================== -->

            <section
                class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
            >
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="font-semibold text-heading">
                            {{ t("subcategories") }}
                        </h2>

                        <p class="mt-1 text-sm text-muted">
                            {{ category.children.length }}
                            {{
                                category.children.length === 1
                                    ? t("subcategory")
                                    : t("subcategories")
                            }}
                        </p>
                    </div>

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                    >
                        <FolderTree class="h-5 w-5" />
                    </div>
                </div>

                <!-- ==================== Subcategory List ==================== -->

                <div
                    v-if="category.children.length"
                    class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <div
                        v-for="child in category.children"
                        :key="child.id"
                        class="rounded-xl border border-border bg-background p-4 transition hover:border-brand-100 hover:bg-brand-50"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p
                                    class="truncate text-sm font-bold text-heading"
                                >
                                    {{ child.name }}
                                </p>

                                <p class="mt-1 text-xs text-muted">
                                    {{ t("category") }} #{{ child.id }}
                                </p>
                            </div>

                            <span
                                v-if="child.is_active"
                                class="shrink-0 rounded-full border border-green-100 bg-green-50 px-2 py-1 text-[11px] font-bold text-green-700"
                            >
                                {{ t("active") }}
                            </span>

                            <span
                                v-else
                                class="shrink-0 rounded-full border border-border bg-surface px-2 py-1 text-[11px] font-bold text-muted"
                            >
                                {{ t("inactive") }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- ==================== Empty Subcategories ==================== -->

                <div
                    v-else
                    class="mt-5 rounded-xl border border-dashed border-border bg-background p-8 text-center"
                >
                    <FolderTree class="mx-auto h-7 w-7 text-muted" />

                    <p class="mt-2 text-sm font-medium text-body">
                        {{ t("noSubcategories") }}
                    </p>

                    <p class="mt-1 text-xs text-muted">
                        {{ t("noSubcategoriesDescription") }}
                    </p>
                </div>
            </section>

            <!-- ==================== Products ==================== -->

            <section
                class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
            >
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                        >
                            <Package class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="font-semibold text-heading">
                                {{ t("products") }}
                            </h2>

                            <p class="text-sm text-muted">
                                {{ category.products.length }}
                                {{
                                    category.products.length === 1
                                        ? t("product")
                                        : t("products")
                                }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ==================== Product List ==================== -->

                <div
                    v-if="category.products.length"
                    class="mt-5 divide-y divide-border overflow-hidden rounded-xl border border-border"
                >
                    <div
                        v-for="product in category.products"
                        :key="product.id"
                        class="flex flex-col gap-4 bg-surface p-4 transition hover:bg-background sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <p
                                    class="truncate text-sm font-bold text-heading"
                                >
                                    {{ product.name }}
                                </p>

                                <span
                                    v-if="product.is_active"
                                    class="rounded-full bg-green-50 px-2 py-0.5 text-[10px] font-bold text-green-700"
                                >
                                    {{ t("active") }}
                                </span>

                                <span
                                    v-else
                                    class="rounded-full bg-background px-2 py-0.5 text-[10px] font-bold text-muted"
                                >
                                    {{ t("inactive") }}
                                </span>
                            </div>

                            <p class="mt-1 text-xs text-muted">
                                {{ t("product") }} #{{ product.id }}
                            </p>

                            <div
                                class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-body"
                            >
                                <span>
                                    {{ t("stock") }}:
                                    <strong>{{ product.stock }}</strong>
                                </span>

                                <span>
                                    {{ t("price") }}:
                                    <strong>{{ product.price }}</strong>
                                </span>
                            </div>
                        </div>

                        <Link
                            :href="`/admin/products/${product.id}`"
                            class="inline-flex w-full shrink-0 items-center justify-center rounded-xl border border-border bg-surface px-4 py-2.5 text-sm font-semibold text-body transition hover:border-brand-100 hover:bg-brand-50 hover:text-brand-700 sm:w-auto"
                        >
                            {{ t("view") }}
                        </Link>
                    </div>
                </div>

                <!-- ==================== Empty Products ==================== -->

                <div
                    v-else
                    class="mt-5 rounded-xl border border-dashed border-border bg-background p-8 text-center"
                >
                    <Package class="mx-auto h-7 w-7 text-muted" />

                    <p class="mt-2 text-sm font-medium text-body">
                        {{ t("noProductsInCategory") }}
                    </p>

                    <p class="mt-1 text-xs text-muted">
                        {{ t("productsInCategoryAppear") }}
                    </p>
                </div>
            </section>
        </div>
    </AdminLayout>
</template>
