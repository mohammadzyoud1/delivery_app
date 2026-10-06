<script setup lang="ts">
// ==================== Imports ====================

import { Head, Link } from "@inertiajs/vue3";
import { ArrowLeft, Package, UserRound } from "lucide-vue-next";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { t } from "@/Utils/i18n";

// ==================== Types ====================

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

interface OptionValue {
    id: number;
    value: string;
}

interface ProductOption {
    id: number;
    name: string;
    values: OptionValue[];
}

interface VariantValue {
    id: number;
    option_value: OptionValue;
}

interface ProductVariant {
    id: number;
    sku: string | null;
    price: string | number;
    stock: number;
    variant_values: VariantValue[];
}

interface Product {
    id: number;
    name: string;
    description: string | null;
    price: string | number;
    stock: number;
    unit: string | null;
    is_active: boolean;
    seller: Seller;
    category: Category | null;
    images: ProductImage[];
    options: ProductOption[];
    variants: ProductVariant[];
}

// ==================== Props ====================

const props = defineProps<{
    product: Product;
}>();

// ==================== Helpers ====================

const sellerName = (): string => {
    return `${props.product.seller.first_name} ${props.product.seller.last_name}`;
};

const variantName = (variant: ProductVariant): string => {
    if (variant.variant_values.length === 0) {
        return "Default variant";
    }

    return variant.variant_values
        .map((variantValue) => variantValue.option_value.value)
        .join(" / ");
};
</script>

<template>
    <Head :title="`Product #${product.id}`" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- ==================== Page Header ==================== -->

            <div>
                <Link
                    href="/admin/products"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-muted transition hover:text-brand-700"
                >
                    <ArrowLeft class="h-4 w-4" />
                    {{ t("backToProducts") }}
                </Link>

                <div
                    class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
                >
                    <div class="min-w-0">
                        <p
                            class="text-xs font-bold uppercase tracking-[0.16em] text-brand-600"
                        >
                            {{ t("productDetails") }}
                        </p>

                        <h1
                            class="mt-1 truncate text-2xl font-bold tracking-tight text-heading"
                        >
                            {{ product.name }}
                        </h1>

                        <p class="mt-1 text-sm text-muted">
                            {{ t("product") }} #{{ product.id }}
                        </p>
                    </div>

                    <span
                        class="inline-flex w-fit shrink-0 rounded-full px-3 py-1.5 text-xs font-bold"
                        :class="
                            product.is_active
                                ? 'border border-green-100 bg-green-50 text-green-700'
                                : 'border border-border bg-background text-muted'
                        "
                    >
                        {{ product.is_active ? t("active") : t("inactive") }}
                    </span>
                </div>
            </div>

            <!-- ==================== Main Overview ==================== -->

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- ==================== Product Information ==================== -->

                <section
                    class="overflow-hidden rounded-2xl border border-border bg-surface shadow-sm lg:col-span-2"
                >
                    <div class="p-5 sm:p-6">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                            >
                                <Package class="h-5 w-5" />
                            </div>

                            <div>
                                <h2 class="font-semibold text-heading">
                                    {{ t("productInfo") }}
                                </h2>

                                <p class="text-sm text-muted">
                                    {{ t("overviewAndImages") }}
                                </p>
                            </div>
                        </div>

                        <!-- ==================== Product Images ==================== -->

                        <div
                            v-if="product.images.length"
                            class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4"
                        >
                            <div
                                v-for="image in product.images"
                                :key="image.id"
                                class="group aspect-square overflow-hidden rounded-xl border border-border bg-background"
                            >
                                <img
                                    :src="`/storage/${image.image}`"
                                    :alt="product.name"
                                    class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                                />
                            </div>
                        </div>

                        <!-- ==================== No Images ==================== -->

                        <div
                            v-else
                            class="mt-6 flex aspect-[16/7] items-center justify-center rounded-xl border border-dashed border-border bg-background"
                        >
                            <div class="text-center">
                                <Package class="mx-auto h-8 w-8 text-muted" />

                                <p class="mt-2 text-sm font-medium text-body">
                                    {{ t("noimg") }}
                                </p>
                            </div>
                        </div>

                        <!-- ==================== Description ==================== -->

                        <div class="mt-6 border-t border-border pt-6">
                            <h3 class="text-sm font-bold text-heading">
                                {{ t("description") }}
                            </h3>

                            <p
                                class="mt-2 whitespace-pre-line text-sm leading-6 text-body"
                            >
                                {{
                                    product.description ||
                                    t("noDescriptionProvided")
                                }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- ==================== Seller Information ==================== -->

                <section
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-purple-50 text-purple-600"
                        >
                            <UserRound class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="font-semibold text-heading">
                                {{ t("seller") }}
                            </h2>

                            <p class="text-sm text-muted">
                                {{ t("productOwner") }}
                            </p>
                        </div>
                    </div>

                    <Link
                        :href="`/admin/users/${product.seller.id}`"
                        class="mt-5 flex items-center gap-3 rounded-xl border border-border bg-background p-4 transition hover:border-brand-100 hover:bg-brand-50"
                    >
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-600 text-white"
                        >
                            <UserRound class="h-5 w-5" />
                        </div>

                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-heading">
                                {{ sellerName() }}
                            </p>

                            <p class="mt-0.5 text-xs text-muted">
                                {{ t("viewSellerProfile") }}
                            </p>
                        </div>

                        <ArrowLeft
                            class="ml-auto h-4 w-4 rotate-180 text-muted"
                        />
                    </Link>
                </section>
            </div>

            <!-- ==================== Product Details ==================== -->

            <section
                class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
            >
                <div>
                    <h2 class="font-semibold text-heading">
                        {{ t("productDetails") }}
                    </h2>

                    <p class="mt-1 text-sm text-muted">
                        {{ t("productInformationDescription") }}
                    </p>
                </div>

                <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Category -->

                    <div
                        class="rounded-xl border border-border bg-background p-4"
                    >
                        <p
                            class="text-xs font-bold uppercase tracking-wide text-muted"
                        >
                            {{ t("category") }}
                        </p>

                        <p
                            class="mt-2 truncate text-sm font-semibold text-heading"
                        >
                            {{ product.category?.name || "—" }}
                        </p>
                    </div>

                    <!-- Price -->

                    <div
                        class="rounded-xl border border-border bg-background p-4"
                    >
                        <p
                            class="text-xs font-bold uppercase tracking-wide text-muted"
                        >
                            {{ t("price") }}
                        </p>

                        <p class="mt-2 text-sm font-semibold text-heading">
                            {{ product.price }}
                        </p>
                    </div>

                    <!-- Stock -->

                    <div
                        class="rounded-xl border border-border bg-background p-4"
                    >
                        <p
                            class="text-xs font-bold uppercase tracking-wide text-muted"
                        >
                            {{ t("stock") }}
                        </p>

                        <p
                            class="mt-2 text-sm font-bold"
                            :class="
                                product.stock > 0
                                    ? 'text-green-600'
                                    : 'text-red-600'
                            "
                        >
                            {{ product.stock }}
                        </p>
                    </div>

                    <!-- Unit -->

                    <div
                        class="rounded-xl border border-border bg-background p-4"
                    >
                        <p
                            class="text-xs font-bold uppercase tracking-wide text-muted"
                        >
                            {{ t("unit") }}
                        </p>

                        <p class="mt-2 text-sm font-semibold text-heading">
                            {{ product.unit || "—" }}
                        </p>
                    </div>
                </div>
            </section>

            <!-- ==================== Options ==================== -->

            <section
                v-if="product.options.length"
                class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
            >
                <div>
                    <h2 class="font-semibold text-heading">
                        {{ t("options") }}
                    </h2>

                    <p class="mt-1 text-sm text-muted">
                        {{ t("availableChoicesDescription") }}
                    </p>
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="option in product.options"
                        :key="option.id"
                        class="rounded-xl border border-border bg-background p-4"
                    >
                        <p class="text-sm font-bold text-heading">
                            {{ option.name }}
                        </p>

                        <div class="mt-3 flex flex-wrap gap-2">
                            <span
                                v-for="value in option.values"
                                :key="value.id"
                                class="rounded-lg border border-border bg-surface px-2.5 py-1 text-xs font-medium text-body"
                            >
                                {{ value.value }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ==================== Variants ==================== -->

            <section
                v-if="product.variants.length"
                class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
            >
                <div>
                    <h2 class="font-semibold text-heading">
                        {{ t("variants") }}
                    </h2>

                    <p class="mt-1 text-sm text-muted">
                        {{ t("variantInformationDescription") }}
                    </p>
                </div>

                <!-- ==================== Desktop Variants ==================== -->

                <div
                    class="mt-5 hidden overflow-hidden rounded-xl border border-border md:block"
                >
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[650px] text-left">
                            <thead class="border-b border-border bg-background">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-xs font-bold uppercase tracking-wide text-muted"
                                    >
                                        {{ t("variant") }}
                                    </th>

                                    <th
                                        class="px-4 py-3 text-xs font-bold uppercase tracking-wide text-muted"
                                    >
                                        SKU
                                    </th>

                                    <th
                                        class="px-4 py-3 text-xs font-bold uppercase tracking-wide text-muted"
                                    >
                                        {{ t("price") }}
                                    </th>

                                    <th
                                        class="px-4 py-3 text-xs font-bold uppercase tracking-wide text-muted"
                                    >
                                        {{ t("stock") }}
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-border">
                                <tr
                                    v-for="variant in product.variants"
                                    :key="variant.id"
                                    class="transition-colors hover:bg-background"
                                >
                                    <td class="px-4 py-4">
                                        <p class="font-semibold text-heading">
                                            {{ variantName(variant) }}
                                        </p>
                                    </td>

                                    <td class="px-4 py-4">
                                        <span
                                            class="text-sm font-medium text-body"
                                        >
                                            {{ variant.sku || "—" }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-4">
                                        <span
                                            class="text-sm font-bold text-heading"
                                        >
                                            {{ variant.price }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-4">
                                        <span
                                            class="text-sm font-bold"
                                            :class="
                                                variant.stock > 0
                                                    ? 'text-green-600'
                                                    : 'text-red-600'
                                            "
                                        >
                                            {{ variant.stock }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ==================== Mobile Variants ==================== -->

                <div class="mt-5 space-y-3 md:hidden">
                    <div
                        v-for="variant in product.variants"
                        :key="variant.id"
                        class="rounded-xl border border-border bg-background p-4"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <p class="font-semibold text-heading">
                                {{ variantName(variant) }}
                            </p>

                            <span
                                class="shrink-0 rounded-full px-2.5 py-1 text-xs font-bold"
                                :class="
                                    variant.stock > 0
                                        ? 'bg-green-50 text-green-700'
                                        : 'bg-red-50 text-red-700'
                                "
                            >
                                {{
                                    variant.stock > 0
                                        ? t("inStock")
                                        : t("outOfStock")
                                }}
                            </span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div>
                                <p
                                    class="text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    SKU
                                </p>

                                <p class="mt-1 text-sm font-medium text-body">
                                    {{ variant.sku || "—" }}
                                </p>
                            </div>

                            <div>
                                <p
                                    class="text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    {{ t("price") }}
                                </p>

                                <p class="mt-1 text-sm font-bold text-heading">
                                    {{ variant.price }}
                                </p>
                            </div>

                            <div>
                                <p
                                    class="text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    {{ t("stock") }}
                                </p>

                                <p
                                    class="mt-1 text-sm font-bold"
                                    :class="
                                        variant.stock > 0
                                            ? 'text-green-600'
                                            : 'text-red-600'
                                    "
                                >
                                    {{ variant.stock }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ==================== No Variants ==================== -->

            <section
                v-else
                class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
            >
                <div
                    class="flex items-center gap-3 rounded-xl border border-border bg-background p-4"
                >
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                    >
                        <Package class="h-5 w-5" />
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-heading">
                            {{ t("noVariants") }}
                        </p>

                        <p class="mt-0.5 text-xs text-muted">
                            {{ t("onePriceAndStock") }}
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </AdminLayout>
</template>
