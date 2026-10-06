<script setup lang="ts">
// ==================== Imports ====================

import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import ProductCard from "@/Components/Products/ProductCard.vue";
import { router } from "@inertiajs/vue3";
import { t } from "@/Utils/i18n";
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

// ==================== Props ====================

const props = defineProps<{
    category: Category;
    products: Product[];
}>();

// ==================== Product Actions ====================

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
        <!-- ==================== Category Header ==================== -->

        <main class="bg-background">
            <div
                class="mx-auto w-full max-w-7xl px-5 py-6 sm:px-8 sm:py-8 lg:py-10"
            >
                <section
                    class="overflow-hidden rounded-3xl border border-border bg-surface shadow-sm"
                >
                    <!-- ==================== Category Image ==================== -->

                    <div
                        v-if="category.image"
                        class="relative h-48 overflow-hidden sm:h-60 lg:h-72"
                    >
                        <img
                            :src="`/storage/${category.image}`"
                            :alt="category.name"
                            class="h-full w-full object-cover"
                        />

                        <div
                            class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-slate-950/20 to-transparent"
                        ></div>

                        <div
                            class="absolute inset-x-0 bottom-0 p-5 sm:p-7 lg:p-8"
                        >
                            <h1
                                class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl lg:text-4xl"
                            >
                                {{ t(`${category.name}`) }}
                            </h1>

                            <p
                                v-if="category.description"
                                class="mt-2 max-w-2xl text-sm leading-6 text-white/85 sm:text-base"
                            >
                                {{ category.description }}
                            </p>
                        </div>
                    </div>

                    <!-- ==================== Category Without Image ==================== -->

                    <div
                        v-else
                        class="bg-gradient-to-br from-brand-50 via-surface to-background p-6 sm:p-8 lg:p-10"
                    >
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-100 text-brand-700"
                        >
                            <span class="text-xl font-extrabold uppercase">
                                {{ category.name.charAt(0) }}
                            </span>
                        </div>

                        <h1
                            class="mt-5 text-2xl font-extrabold tracking-tight text-heading sm:text-3xl lg:text-4xl"
                        >
                            {{ t(`${category.name}`) }}
                        </h1>

                        <p
                            v-if="category.description"
                            class="mt-2 max-w-2xl text-sm leading-6 text-muted sm:text-base"
                        >
                            {{ category.description }}
                        </p>
                    </div>
                </section>

                <!-- ==================== Products Section ==================== -->

                <section class="mt-8">
                    <!-- ==================== Products Header ==================== -->

                    <div
                        class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between"
                    >
                        <div>
                            <h2
                                class="text-xl font-extrabold tracking-tight text-heading sm:text-2xl"
                            >
                                {{ t("products") }}
                            </h2>

                            <p class="mt-1 text-sm text-muted">
                                {{ t("browseProducts") }}
                                {{ t(`${category.name}`) }}.
                            </p>
                        </div>

                        <p
                            v-if="products.length"
                            class="text-sm font-medium text-muted"
                        >
                            {{ products.length }}
                            {{
                                products.length === 1
                                    ? t("product")
                                    : t("products")
                            }}
                        </p>
                    </div>

                    <!-- ==================== Product Grid ==================== -->

                    <div
                        v-if="products.length"
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

                    <!-- ==================== Empty State ==================== -->

                    <div
                        v-else
                        class="mt-5 rounded-2xl border border-border bg-surface px-6 py-12 text-center shadow-sm"
                    >
                        <h3 class="text-base font-bold text-heading">
                            {{ t("noProducts") }}
                        </h3>

                        <p
                            class="mx-auto mt-1.5 max-w-sm text-sm leading-6 text-muted"
                        >
                            {{ t("noProductsin") }}{{ t(`${category.name}`) }}
                        </p>
                    </div>
                </section>
            </div>
        </main>
    </CustomerLayout>
</template>
