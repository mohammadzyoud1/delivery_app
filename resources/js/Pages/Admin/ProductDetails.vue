<script setup lang="ts">
// ==================== Imports ====================

import { Head, Link } from "@inertiajs/vue3";
import { ArrowLeft, Package, UserRound } from "lucide-vue-next";
import AdminLayout from "@/Layouts/AdminLayout.vue";

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
        <!-- ==================== Header ==================== -->

        <div class="mb-6">
            <Link
                href="/admin/products"
                class="mb-4 inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900"
            >
                <ArrowLeft class="h-4 w-4" />
                Back to Products
            </Link>

            <div>
                <p
                    class="text-xs font-semibold uppercase tracking-wide text-gray-400"
                >
                    Product Details
                </p>

                <h1 class="mt-1 text-2xl font-bold text-gray-900">
                    {{ product.name }}
                </h1>
            </div>
        </div>

        <!-- ==================== Product Overview ==================== -->

        <div class="grid gap-6 lg:grid-cols-3">
            <section
                class="overflow-hidden rounded-2xl border border-gray-200 bg-white lg:col-span-2"
            >
                <div class="p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-11 w-11 items-center justify-center rounded-xl bg-gray-100"
                            >
                                <Package class="h-5 w-5 text-gray-600" />
                            </div>

                            <div>
                                <h2 class="font-semibold text-gray-900">
                                    Product Information
                                </h2>
                                <p class="text-sm text-gray-500">
                                    Product #{{ product.id }}
                                </p>
                            </div>
                        </div>

                        <span
                            class="rounded-full px-3 py-1 text-xs font-semibold"
                            :class="
                                product.is_active
                                    ? 'bg-green-50 text-green-700'
                                    : 'bg-gray-100 text-gray-600'
                            "
                        >
                            {{ product.is_active ? "Active" : "Inactive" }}
                        </span>
                    </div>

                    <!-- ==================== Product Images ==================== -->

                    <div
                        v-if="product.images.length"
                        class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3"
                    >
                        <img
                            v-for="image in product.images"
                            :key="image.id"
                            :src="`/storage/${image.image}`"
                            alt=""
                            class="aspect-square w-full rounded-xl object-cover"
                        />
                    </div>

                    <!-- ==================== Description ==================== -->

                    <div class="mt-6">
                        <h3 class="text-sm font-semibold text-gray-900">
                            Description
                        </h3>

                        <p
                            class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-600"
                        >
                            {{
                                product.description ||
                                "No description provided."
                            }}
                        </p>
                    </div>
                </div>
            </section>

            <!-- ==================== Seller Information ==================== -->

            <section
                class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6"
            >
                <h2 class="font-semibold text-gray-900">Seller</h2>

                <Link
                    :href="`/admin/users/${product.seller.id}`"
                    class="mt-4 flex items-center gap-3 rounded-xl bg-gray-50 p-4 hover:bg-gray-100"
                >
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-900 text-white"
                    >
                        <UserRound class="h-5 w-5" />
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ sellerName() }}
                        </p>
                        <p class="text-xs text-gray-500">View seller</p>
                    </div>
                </Link>
            </section>
        </div>

        <!-- ==================== Basic Details ==================== -->

        <section
            class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 sm:p-6"
        >
            <h2 class="font-semibold text-gray-900">Product Details</h2>

            <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <p class="text-xs text-gray-500">Category</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">
                        {{ product.category?.name || "—" }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Price</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">
                        {{ product.price }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Stock</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">
                        {{ product.stock }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Unit</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">
                        {{ product.unit || "—" }}
                    </p>
                </div>
            </div>
        </section>

        <!-- ==================== Options ==================== -->

        <section
            v-if="product.options.length"
            class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 sm:p-6"
        >
            <h2 class="font-semibold text-gray-900">Options</h2>

            <div class="mt-4 flex flex-wrap gap-3">
                <div
                    v-for="option in product.options"
                    :key="option.id"
                    class="rounded-xl bg-gray-50 px-4 py-3"
                >
                    <p class="text-sm font-semibold text-gray-900">
                        {{ option.name }}
                    </p>

                    <p class="mt-1 text-xs text-gray-500">
                        {{
                            option.values.map((value) => value.value).join(", ")
                        }}
                    </p>
                </div>
            </div>
        </section>

        <!-- ==================== Variants ==================== -->

        <section
            v-if="product.variants.length"
            class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 sm:p-6"
        >
            <h2 class="font-semibold text-gray-900">Variants</h2>

            <!-- ==================== Desktop Variants ==================== -->

            <div class="mt-4 hidden overflow-x-auto md:block">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr
                            class="border-b border-gray-100 text-xs text-gray-500"
                        >
                            <th class="px-4 py-3 font-medium">Variant</th>
                            <th class="px-4 py-3 font-medium">SKU</th>
                            <th class="px-4 py-3 font-medium">Price</th>
                            <th class="px-4 py-3 font-medium">Stock</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="variant in product.variants"
                            :key="variant.id"
                            class="border-b border-gray-50 last:border-0"
                        >
                            <td class="px-4 py-4 font-medium text-gray-900">
                                {{ variantName(variant) }}
                            </td>

                            <td class="px-4 py-4 text-gray-600">
                                {{ variant.sku || "—" }}
                            </td>

                            <td class="px-4 py-4 text-gray-600">
                                {{ variant.price }}
                            </td>

                            <td class="px-4 py-4 text-gray-600">
                                {{ variant.stock }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- ==================== Mobile Variants ==================== -->

            <div class="mt-4 space-y-3 md:hidden">
                <div
                    v-for="variant in product.variants"
                    :key="variant.id"
                    class="rounded-xl bg-gray-50 p-4"
                >
                    <p class="font-semibold text-gray-900">
                        {{ variantName(variant) }}
                    </p>

                    <div class="mt-2 grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-xs text-gray-500">SKU</p>
                            <p class="text-gray-700">
                                {{ variant.sku || "—" }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500">Price</p>
                            <p class="text-gray-700">
                                {{ variant.price }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500">Stock</p>
                            <p class="text-gray-700">
                                {{ variant.stock }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AdminLayout>
</template>
