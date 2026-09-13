<script setup lang="ts">
// ==================== Imports ====================

import CustomerLayout from "@/Layouts/CustomerLayout.vue";

// ==================== Types ====================

interface Category {
    id: number;
    name: string;
    description: string | null;
    image: string | null;
}
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

defineProps<{
    category: Category;
    products: Product[];
}>();
</script>

<template>
    <CustomerLayout>
        <!-- ==================== Category Header ==================== -->

        <section>
            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                {{ category.name }}
            </h1>

            <p
                v-if="category.description"
                class="mt-2 text-sm text-gray-500 sm:text-base"
            >
                {{ category.description }}
            </p>
        </section>

        <!-- ==================== Products ==================== -->

        <section class="mt-8">
            <h2 class="text-lg font-bold text-gray-900">Products</h2>

            <div
                v-if="products.length"
                class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4"
            >
                <div
                    v-for="product in products"
                    :key="product.id"
                    class="rounded-xl border border-gray-200 bg-white p-4"
                >
                    <div
                        class="aspect-square overflow-hidden rounded-lg bg-gray-100"
                    >
                        <img
                            v-if="product.images.length"
                            :src="product.images[0].image"
                            :alt="product.name"
                            class="h-full w-full object-cover"
                        />

                        <div
                            v-else
                            class="flex h-full items-center justify-center text-sm text-gray-400"
                        >
                            No image
                        </div>
                    </div>
                    <h3 class="font-semibold text-gray-900">
                        {{ product.name }}
                    </h3>

                    <p
                        v-if="product.description"
                        class="mt-1 text-sm text-gray-500"
                    >
                        {{ product.description }}
                    </p>

                    <p class="mt-3 font-bold text-gray-900">
                        {{ product.price }}
                    </p>

                    <p class="mt-1 text-xs text-gray-500">
                        Stock: {{ product.stock }}
                    </p>
                </div>
            </div>

            <p v-else class="mt-4 text-sm text-gray-500">
                No products available in this category.
            </p>
        </section>
    </CustomerLayout>
</template>
