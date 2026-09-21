<script setup lang="ts">
// ==================== Imports ====================

import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import ProductCard from "@/Components/Products/ProductCard.vue";
import CategorySlider from "@/Components/Categories/CategorySlider.vue";
import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import ProductSearch from "@/Components/Products/ProductSearch.vue";

// ==================== Types ====================

interface Category {
    id: number;
    name: string;
    image: string | null;
}

interface ProductImage {
    id: number;
    image: string;
}

interface ProductVariant {
    id: number;
    price: number;
    stock: number;
}

interface Product {
    id: number;
    name: string;
    price: number;
    unit: string | null;
    images: ProductImage[];
    variants: ProductVariant[];
}

// ==================== State ====================

const search = ref("");

// ==================== Props ====================

const props = defineProps<{
    categories: Category[];
    products: Product[];
}>();

// ==================== Add To Cart ====================

const handleAddToCart = (product: Product) => {
    // ==================== Products With Variants ====================

    if (product.variants.length > 0) {
        router.visit(`/products/${product.id}`);
        return;
    }

    // ==================== Products Without Variants ====================

    router.post("/cart/items", {
        product_id: product.id,
        variant_id: null,
        quantity: 1,
    });
};
</script>

<template>
    <CustomerLayout>
        <!-- ==================== Home Content ==================== -->

        <main class="bg-background">
            <div class="mx-auto w-full max-w-7xl px-5 py-6 sm:px-8 sm:py-8">
                <!-- ==================== Search ==================== -->

                <section>
                    <ProductSearch v-model="search" />
                </section>

                <!-- ==================== Categories ==================== -->

                <section class="mt-8">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h2
                                class="text-lg font-bold tracking-tight text-heading sm:text-xl"
                            >
                                Browse Categories
                            </h2>

                            <p class="mt-1 text-sm text-muted">
                                Find what you're looking for
                            </p>
                        </div>
                    </div>

                    <CategorySlider :categories="categories" />
                </section>

                <!-- ==================== Products ==================== -->

                <section class="mt-10">
                    <div class="flex items-end justify-between">
                        <div>
                            <h2
                                class="text-lg font-bold tracking-tight text-heading sm:text-xl"
                            >
                                Products
                            </h2>

                            <p class="mt-1 text-sm text-muted">
                                Discover products available near you
                            </p>
                        </div>

                        <span
                            v-if="props.products.length"
                            class="hidden text-sm font-medium text-muted sm:block"
                        >
                            {{ props.products.length }} products
                        </span>
                    </div>

                    <!-- ==================== Empty State ==================== -->

                    <div
                        v-if="!props.products.length"
                        class="mt-5 rounded-2xl border border-border bg-surface px-6 py-12 text-center shadow-sm"
                    >
                        <p class="text-sm font-medium text-body">
                            No products available.
                        </p>

                        <p class="mt-1 text-sm text-muted">
                            Check back later for new products.
                        </p>
                    </div>

                    <!-- ==================== Product Grid ==================== -->

                    <div
                        v-else
                        class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                    >
                        <ProductCard
                            v-for="product in props.products"
                            :key="product.id"
                            :product="product"
                            @favorite="console.log('favorite', $event)"
                            @add-to-cart="handleAddToCart"
                        />
                    </div>
                </section>
            </div>
        </main>
    </CustomerLayout>
</template>
