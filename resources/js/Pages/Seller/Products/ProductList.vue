<script setup lang="ts">
// ==================== Imports ====================

import { computed, ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import SellerLayout from "@/Layouts/SellerLayout.vue";
import {
    CheckCircle2,
    Edit3,
    Package,
    Plus,
    Power,
    XCircle,
} from "lucide-vue-next";

// ==================== Types ====================

interface ProductImage {
    id: number;
    image: string;
}

interface Product {
    id: number;
    name: string;
    description: string | null;
    price: number;
    stock: number;
    unit: string | null;
    is_active: boolean;
    images: ProductImage[];
}

// ==================== Props ====================

const props = defineProps<{
    products: Product[];
    initialFilter: Filter;
}>();

// ==================== Product Filter ====================

type Filter = "all" | "active" | "inactive";

const activeFilter = ref<Filter>(props.initialFilter);

const filteredProducts = computed(() => {
    if (activeFilter.value === "active") {
        return props.products.filter((product) => product.is_active);
    }

    if (activeFilter.value === "inactive") {
        return props.products.filter((product) => !product.is_active);
    }

    return props.products;
});

// ==================== Product Status ====================

const toggleStatus = (product: Product) => {
    router.patch(`/seller/products/${product.id}/toggle-status`);
};

// ==================== Price Formatting ====================

const formatPrice = (price: number) => {
    return Number(price).toFixed(2);
};
</script>

<template>
    <SellerLayout>
        <div class="mx-auto w-full max-w-7xl">
            <!-- ==================== Page Header ==================== -->

            <section
                class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
            >
                <div>
                    <p
                        class="text-sm font-bold uppercase tracking-wider text-brand-600"
                    >
                        Store Management
                    </p>

                    <h1
                        class="mt-1 text-2xl font-extrabold tracking-tight text-heading sm:text-3xl"
                    >
                        My Products
                    </h1>

                    <p class="mt-2 text-sm leading-6 text-muted">
                        Manage the products you sell in your store.
                    </p>
                </div>

                <!-- ==================== Add Product ==================== -->

                <Link
                    href="/seller/products/create"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 py-3 text-sm font-bold text-white shadow-sm transition-all duration-200 hover:bg-brand-700 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 sm:w-auto"
                >
                    <Plus :size="18" :stroke-width="2.5" />
                    Add Product
                </Link>
            </section>

            <!-- ==================== Product Filters ==================== -->

            <section
                class="mb-6 overflow-x-auto rounded-2xl border border-border bg-surface p-1.5 shadow-sm"
            >
                <div class="flex min-w-max gap-1">
                    <button
                        type="button"
                        class="flex min-w-[90px] items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-bold transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500"
                        :class="
                            activeFilter === 'all'
                                ? 'bg-brand-50 text-brand-700 shadow-sm'
                                : 'text-muted hover:bg-background hover:text-heading'
                        "
                        @click="activeFilter = 'all'"
                    >
                        <Package :size="16" />
                        All
                    </button>

                    <button
                        type="button"
                        class="flex min-w-[100px] items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-bold transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500"
                        :class="
                            activeFilter === 'active'
                                ? 'bg-green-50 text-green-700 shadow-sm'
                                : 'text-muted hover:bg-background hover:text-heading'
                        "
                        @click="activeFilter = 'active'"
                    >
                        <CheckCircle2 :size="16" />
                        Active
                    </button>

                    <button
                        type="button"
                        class="flex min-w-[100px] items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-bold transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500"
                        :class="
                            activeFilter === 'inactive'
                                ? 'bg-slate-100 text-slate-700 shadow-sm'
                                : 'text-muted hover:bg-background hover:text-heading'
                        "
                        @click="activeFilter = 'inactive'"
                    >
                        <XCircle :size="16" />
                        Inactive
                    </button>
                </div>
            </section>

            <!-- ==================== Product Count ==================== -->

            <div
                v-if="filteredProducts.length"
                class="mb-4 flex items-center justify-between"
            >
                <p class="text-sm font-medium text-muted">
                    Showing
                    <span class="font-bold text-heading">
                        {{ filteredProducts.length }}
                    </span>
                    {{ filteredProducts.length === 1 ? "product" : "products" }}
                </p>
            </div>

            <!-- ==================== Empty State ==================== -->

            <div
                v-if="!filteredProducts.length"
                class="rounded-2xl border border-border bg-surface px-6 py-14 text-center shadow-sm"
            >
                <div
                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-background text-muted"
                >
                    <Package :size="26" :stroke-width="1.7" />
                </div>

                <h3 class="mt-4 text-base font-extrabold text-heading">
                    No products found
                </h3>

                <p class="mx-auto mt-1.5 max-w-sm text-sm leading-6 text-muted">
                    There are no products in this category.
                </p>

                <Link
                    v-if="activeFilter === 'all'"
                    href="/seller/products/create"
                    class="mt-5 inline-flex items-center gap-2 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-brand-700"
                >
                    <Plus :size="17" />
                    Add Product
                </Link>
            </div>

            <!-- ==================== Product Grid ==================== -->

            <div
                v-else
                class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <article
                    v-for="product in filteredProducts"
                    :key="product.id"
                    :class="[
                        'group flex h-full flex-col overflow-hidden rounded-2xl border bg-surface shadow-sm transition-all duration-200',
                        product.is_active
                            ? 'border-border hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-lg hover:shadow-slate-900/5'
                            : 'border-border bg-slate-50/70',
                    ]"
                >
                    <!-- ==================== Product Image ==================== -->

                    <div
                        class="relative aspect-square overflow-hidden bg-background"
                    >
                        <img
                            v-if="product.images.length"
                            :src="`/storage/${product.images[0].image}`"
                            :alt="product.name"
                            :class="[
                                'h-full w-full object-cover transition-transform duration-500',
                                product.is_active
                                    ? 'group-hover:scale-105'
                                    : 'grayscale opacity-60',
                            ]"
                        />

                        <div
                            v-else
                            class="flex h-full flex-col items-center justify-center gap-2 text-muted"
                        >
                            <Package :size="26" :stroke-width="1.5" />

                            <span class="text-xs font-medium"> No image </span>
                        </div>

                        <!-- ==================== Status Badge ==================== -->

                        <span
                            v-if="product.is_active"
                            class="absolute left-3 top-3 inline-flex items-center gap-1.5 rounded-full border border-green-200 bg-green-50 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-green-700 shadow-sm"
                        >
                            <CheckCircle2 :size="13" />
                            Active
                        </span>

                        <span
                            v-else
                            class="absolute left-3 top-3 inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white/95 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-slate-600 shadow-sm backdrop-blur"
                        >
                            <XCircle :size="13" />
                            Inactive
                        </span>
                    </div>

                    <!-- ==================== Product Information ==================== -->

                    <div class="flex flex-1 flex-col p-4">
                        <div>
                            <h3
                                class="truncate text-base font-extrabold text-heading"
                            >
                                {{ product.name }}
                            </h3>

                            <p
                                class="mt-2 text-xl font-extrabold tracking-tight text-heading"
                            >
                                {{ formatPrice(product.price) }}
                            </p>

                            <p class="mt-1 text-sm font-medium text-muted">
                                Stock: {{ product.stock }}

                                <span v-if="product.unit">
                                    {{ product.unit }}
                                </span>
                            </p>
                        </div>

                        <!-- ==================== Product Actions ==================== -->

                        <div class="mt-auto grid grid-cols-2 gap-2 pt-5">
                            <!-- ==================== Edit Product ==================== -->

                            <Link
                                :href="`/seller/products/${product.id}/edit`"
                                class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-brand-600 px-3 py-2.5 text-sm font-bold text-white shadow-sm transition-all duration-200 hover:bg-brand-700 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2"
                            >
                                <Edit3 :size="16" />
                                Edit
                            </Link>

                            <!-- ==================== Toggle Status ==================== -->

                            <button
                                type="button"
                                :class="
                                    product.is_active
                                        ? 'border border-red-200 bg-red-50 text-red-700 hover:bg-red-100'
                                        : 'border border-green-200 bg-green-50 text-green-700 hover:bg-green-100'
                                "
                                class="inline-flex items-center justify-center gap-1.5 rounded-xl px-3 py-2.5 text-sm font-bold transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2"
                                @click="toggleStatus(product)"
                            >
                                <Power :size="16" />
                                {{
                                    product.is_active
                                        ? "Deactivate"
                                        : "Activate"
                                }}
                            </button>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </SellerLayout>
</template>
