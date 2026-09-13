<script setup lang="ts">
// ==================== Imports ====================

import { computed, ref } from "vue";

import SellerLayout from "@/Layouts/SellerLayout.vue";

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

// ==================== Price Formatting ====================

const formatPrice = (price: number) => {
    return Number(price).toFixed(2);
};
</script>

<template>
    <SellerLayout>
        <div class="mx-auto w-full max-w-7xl">
            <!-- ==================== Page Header ==================== -->

            <div class="mb-6">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                    My Products
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Manage the products you sell in your store.
                </p>
            </div>

            <!-- ==================== Product Filters ==================== -->

            <div
                class="mb-6 flex w-full overflow-x-auto rounded-xl bg-gray-100 p-1"
            >
                <button
                    type="button"
                    class="min-w-[80px] flex-1 rounded-lg px-4 py-2 text-sm font-medium transition"
                    :class="
                        activeFilter === 'all'
                            ? 'bg-white text-gray-900 shadow-sm'
                            : 'text-gray-500 hover:text-gray-900'
                    "
                    @click="activeFilter = 'all'"
                >
                    All
                </button>

                <button
                    type="button"
                    class="min-w-[80px] flex-1 rounded-lg px-4 py-2 text-sm font-medium transition"
                    :class="
                        activeFilter === 'active'
                            ? 'bg-white text-gray-900 shadow-sm'
                            : 'text-gray-500 hover:text-gray-900'
                    "
                    @click="activeFilter = 'active'"
                >
                    Active
                </button>

                <button
                    type="button"
                    class="min-w-[80px] flex-1 rounded-lg px-4 py-2 text-sm font-medium transition"
                    :class="
                        activeFilter === 'inactive'
                            ? 'bg-white text-gray-900 shadow-sm'
                            : 'text-gray-500 hover:text-gray-900'
                    "
                    @click="activeFilter = 'inactive'"
                >
                    Inactive
                </button>
            </div>

            <!-- ==================== Empty State ==================== -->

            <div
                v-if="!filteredProducts.length"
                class="rounded-2xl border border-gray-100 bg-white p-8 text-center shadow-sm"
            >
                <h3 class="text-base font-semibold text-gray-900">
                    No products found
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    There are no products in this category.
                </p>
            </div>

            <!-- ==================== Product Grid ==================== -->

            <div
                v-else
                class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <div
                    v-for="product in filteredProducts"
                    :key="product.id"
                    :class="[
                        'overflow-hidden rounded-2xl border bg-white shadow-sm transition',
                        product.is_active
                            ? 'border-gray-100 hover:-translate-y-0.5 hover:shadow-md'
                            : 'border-gray-200 opacity-75',
                    ]"
                >
                    <!-- ==================== Product Image ==================== -->

                    <div class="relative aspect-square bg-gray-100">
                        <img
                            v-if="product.images.length"
                            :src="`/storage/${product.images[0].image}`"
                            :alt="product.name"
                            :class="[
                                'h-full w-full object-cover',
                                !product.is_active && 'grayscale opacity-60',
                            ]"
                        />

                        <div
                            v-else
                            class="flex h-full items-center justify-center text-sm text-gray-400"
                        >
                            No image
                        </div>

                        <!-- ==================== Inactive Badge ==================== -->

                        <span
                            v-if="!product.is_active"
                            class="absolute left-3 top-3 rounded-md bg-gray-800 px-2.5 py-1 text-xs font-semibold tracking-wide text-white shadow-sm"
                        >
                            INACTIVE
                        </span>

                        <!-- ==================== Active Badge ==================== -->

                        <span
                            v-else
                            class="absolute left-3 top-3 rounded-md bg-white px-2.5 py-1 text-xs font-semibold tracking-wide text-gray-900 shadow-sm"
                        >
                            ACTIVE
                        </span>
                    </div>

                    <!-- ==================== Product Information ==================== -->

                    <div class="p-4">
                        <h3 class="truncate font-semibold text-gray-900">
                            {{ product.name }}
                        </h3>

                        <p class="mt-2 text-lg font-bold text-gray-900">
                            {{ formatPrice(product.price) }}
                        </p>

                        <p class="mt-1 text-sm text-gray-500">
                            Stock: {{ product.stock }}

                            <span v-if="product.unit">
                                {{ product.unit }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </SellerLayout>
</template>
