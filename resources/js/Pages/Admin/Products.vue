<script setup lang="ts">
// ==================== Imports ====================

import { Head, Link } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Eye, Package, Store } from "lucide-vue-next";
import { t } from "@/Utils/i18n";
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

        return `${t("from")} ${minPrice.toFixed(2)}`;
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
            <!-- ==================== Page Header ==================== -->

            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
            >
                <div>
                    <p
                        class="text-xs font-bold uppercase tracking-[0.16em] text-brand-600"
                    >
                        {{ t("adminPanel") }}
                    </p>

                    <h1
                        class="mt-1 text-2xl font-bold tracking-tight text-heading"
                    >
                        {{ t("products") }}
                    </h1>

                    <p class="mt-1 max-w-2xl text-sm text-muted">
                        {{ t("manageSellerProductsDescription") }}
                    </p>
                </div>

                <div
                    class="inline-flex w-fit items-center gap-2 rounded-full border border-border bg-surface px-4 py-2 text-sm font-semibold text-body shadow-sm"
                >
                    <Package class="h-4 w-4 text-brand-600" />

                    {{ products.length }} {{ t("products") }}
                </div>
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
                            <Package class="h-5 w-5" />
                        </div>

                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-muted"
                            >
                                {{ t("totalProducts") }}
                            </p>

                            <p class="mt-0.5 text-xl font-bold text-heading">
                                {{ products.length }}
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
                            <Package class="h-5 w-5" />
                        </div>

                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-muted"
                            >
                                {{ t("activeProducts") }}
                            </p>

                            <p class="mt-0.5 text-xl font-bold text-heading">
                                {{
                                    products.filter(
                                        (product) => product.is_active,
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
                            <Store class="h-5 w-5" />
                        </div>

                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-muted"
                            >
                                {{ t("sellers") }}
                            </p>

                            <p class="mt-0.5 text-xl font-bold text-heading">
                                {{
                                    new Set(
                                        products.map(
                                            (product) => product.seller_id,
                                        ),
                                    ).size
                                }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==================== Empty State ==================== -->

            <div
                v-if="products.length === 0"
                class="rounded-2xl border border-border bg-surface px-6 py-14 text-center shadow-sm"
            >
                <div
                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"
                >
                    <Package class="h-6 w-6" />
                </div>

                <h2 class="mt-4 text-base font-semibold text-heading">
                    {{ t("noProducts") }}
                </h2>

                <p class="mx-auto mt-1 max-w-md text-sm text-muted">
                    {{ t("sellerProductsAppear") }}
                </p>
            </div>

            <!-- ==================== Desktop Table ==================== -->

            <div
                v-else
                class="hidden overflow-hidden rounded-2xl border border-border bg-surface shadow-sm md:block"
            >
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1000px] text-left">
                        <thead class="border-b border-border bg-background">
                            <tr>
                                <th
                                    class="px-5 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    {{ t("product") }}
                                </th>

                                <th
                                    class="px-5 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    {{ t("seller") }}
                                </th>

                                <th
                                    class="px-5 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    {{ t("category") }}
                                </th>

                                <th
                                    class="px-5 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    {{ t("price") }}
                                </th>

                                <th
                                    class="px-5 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    {{ t("stock") }}
                                </th>

                                <th
                                    class="px-5 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    {{ t("status") }}
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-border">
                            <tr
                                v-for="product in products"
                                :key="product.id"
                                class="transition-colors hover:bg-background"
                            >
                                <!-- ==================== Product ==================== -->

                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-background ring-1 ring-border"
                                        >
                                            <img
                                                v-if="product.images.length > 0"
                                                :src="product.images[0].image"
                                                :alt="product.name"
                                                class="h-full w-full object-cover"
                                            />

                                            <Package
                                                v-else
                                                class="h-5 w-5 text-muted"
                                            />
                                        </div>

                                        <div class="min-w-0">
                                            <p
                                                class="max-w-xs truncate text-sm font-semibold text-heading"
                                            >
                                                {{ product.name }}
                                            </p>

                                            <p
                                                class="mt-0.5 text-xs text-muted"
                                            >
                                                {{ t("product") }} #{{
                                                    product.id
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- ==================== Seller ==================== -->

                                <td class="px-5 py-4">
                                    <div
                                        class="flex items-center gap-2 text-sm text-body"
                                    >
                                        <div
                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-50 text-brand-600"
                                        >
                                            <Store class="h-4 w-4" />
                                        </div>

                                        <span class="font-medium">
                                            {{ fullName(product.seller) }}
                                        </span>
                                    </div>
                                </td>

                                <!-- ==================== Category ==================== -->

                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex max-w-[160px] truncate rounded-lg bg-background px-2.5 py-1 text-sm font-medium text-body"
                                    >
                                        {{ product.category.name }}
                                    </span>
                                </td>

                                <!-- ==================== Price ==================== -->

                                <td
                                    class="whitespace-nowrap px-5 py-4 text-sm font-bold text-heading"
                                >
                                    {{ productPrice(product) }}
                                </td>

                                <!-- ==================== Stock ==================== -->

                                <td class="px-5 py-4">
                                    <span
                                        class="text-sm font-bold"
                                        :class="
                                            productStock(product) > 0
                                                ? 'text-green-600'
                                                : 'text-red-600'
                                        "
                                    >
                                        {{ productStock(product) }}
                                    </span>
                                </td>

                                <!-- ==================== Status ==================== -->

                                <td class="px-5 py-4">
                                    <span
                                        v-if="product.is_active"
                                        class="inline-flex rounded-full border border-green-100 bg-green-50 px-3 py-1 text-xs font-bold text-green-700"
                                    >
                                        {{ t("active") }}
                                    </span>

                                    <span
                                        v-else
                                        class="inline-flex rounded-full border border-border bg-background px-3 py-1 text-xs font-bold text-muted"
                                    >
                                        {{ t("inactive") }}
                                    </span>
                                </td>

                                <!-- ==================== Action ==================== -->

                                <td class="px-5 py-4 text-right">
                                    <Link
                                        :href="`/admin/products/${product.id}`"
                                        class="inline-flex items-center gap-2 rounded-xl border border-border bg-surface px-3.5 py-2 text-sm font-semibold text-body transition hover:border-brand-100 hover:bg-brand-50 hover:text-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2"
                                    >
                                        <Eye class="h-4 w-4" />
                                        {{ t("view") }}
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ==================== Mobile Cards ==================== -->

            <div v-if="products.length > 0" class="space-y-4 md:hidden">
                <div
                    v-for="product in products"
                    :key="product.id"
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm"
                >
                    <!-- ==================== Product Header ==================== -->

                    <div class="flex gap-3">
                        <div
                            class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-background ring-1 ring-border"
                        >
                            <img
                                v-if="product.images.length > 0"
                                :src="product.images[0].image"
                                :alt="product.name"
                                class="h-full w-full object-cover"
                            />

                            <Package v-else class="h-6 w-6 text-muted" />
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h2
                                        class="truncate font-semibold text-heading"
                                    >
                                        {{ product.name }}
                                    </h2>

                                    <p class="mt-1 text-xs text-muted">
                                        {{ t("product") }} #{{ product.id }}
                                    </p>
                                </div>

                                <span
                                    v-if="product.is_active"
                                    class="shrink-0 rounded-full border border-green-100 bg-green-50 px-2.5 py-1 text-xs font-bold text-green-700"
                                >
                                    {{ t("active") }}
                                </span>

                                <span
                                    v-else
                                    class="shrink-0 rounded-full border border-border bg-background px-2.5 py-1 text-xs font-bold text-muted"
                                >
                                    {{ t("inactive") }}
                                </span>
                            </div>

                            <div
                                class="mt-2 flex items-center gap-1.5 text-sm text-body"
                            >
                                <Store class="h-3.5 w-3.5 text-brand-600" />

                                <span class="truncate">
                                    {{ fullName(product.seller) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== Product Details ==================== -->

                    <div
                        class="mt-5 grid grid-cols-3 gap-3 border-t border-border pt-4"
                    >
                        <div class="min-w-0">
                            <p
                                class="text-[11px] font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("category") }}
                            </p>

                            <p
                                class="mt-1 truncate text-sm font-semibold text-heading"
                            >
                                {{ product.category.name }}
                            </p>
                        </div>

                        <div>
                            <p
                                class="text-[11px] font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("price") }}
                            </p>

                            <p
                                class="mt-1 whitespace-nowrap text-sm font-semibold text-heading"
                            >
                                {{ productPrice(product) }}
                            </p>
                        </div>

                        <div>
                            <p
                                class="text-[11px] font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("stock") }}
                            </p>

                            <p
                                class="mt-1 text-sm font-bold"
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

                    <!-- ==================== Created Date ==================== -->

                    <div
                        class="mt-4 flex items-center justify-between border-t border-border pt-4"
                    >
                        <span class="text-sm text-muted">
                            {{ t("added") }}
                        </span>

                        <span class="text-sm font-medium text-body">
                            {{ formatDate(product.created_at) }}
                        </span>
                    </div>

                    <!-- ==================== View Button ==================== -->

                    <Link
                        :href="`/admin/products/${product.id}`"
                        class="mt-4 flex w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2"
                    >
                        <Eye class="h-4 w-4" />
                        {{ t("view") }}
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
