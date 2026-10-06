<script setup lang="ts">
// ==================== Imports ====================

import Button from "@/Components/UI/Button.vue";
import { Heart, ShoppingCart } from "lucide-vue-next";
import { Link } from "@inertiajs/vue3";
import { t } from "@/Utils/i18n";

// ==================== Types ====================

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

defineProps<{
    product: Product;
}>();

// ==================== Events ====================

const emit = defineEmits<{
    favorite: [product: Product];
    addToCart: [product: Product];
}>();
</script>

<template>
    <!-- ==================== Product Card ==================== -->

    <article
        class="group flex h-full flex-col overflow-hidden rounded-2xl border border-border bg-surface shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-lg hover:shadow-slate-900/5"
    >
        <!-- ==================== Product Image ==================== -->

        <Link
            :href="`/products/${product.id}`"
            class="block focus-visible:outline-none"
        >
            <div class="relative aspect-square overflow-hidden bg-background">
                <!-- ==================== Product Image ==================== -->

                <img
                    v-if="product.images.length"
                    :src="`/storage/${product.images[0].image}`"
                    :alt="product.name"
                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                />

                <!-- ==================== Empty Image State ==================== -->

                <div
                    v-else
                    class="flex h-full flex-col items-center justify-center gap-2 text-muted"
                >
                    <ShoppingCart :size="26" :stroke-width="1.5" />

                    <span class="text-xs font-medium">
                        {{ t(`noimg`) }}
                    </span>
                </div>

                <!-- ==================== Favorite Button ==================== -->

                <button
                    type="button"
                    aria-label="Add to favorites"
                    class="absolute right-3 top-3 flex h-9 w-9 items-center justify-center rounded-full border border-white/60 bg-white/90 text-body shadow-sm backdrop-blur-sm transition-all duration-200 hover:scale-105 hover:bg-white hover:text-red-500 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2"
                    @click.prevent.stop="emit('favorite', product)"
                >
                    <Heart :size="18" :stroke-width="2" />
                </button>
            </div>
        </Link>

        <!-- ==================== Product Information ==================== -->

        <div class="flex flex-1 flex-col p-3.5 sm:p-4">
            <Link
                :href="`/products/${product.id}`"
                class="block focus-visible:outline-none"
            >
                <!-- ==================== Product Name ==================== -->

                <h3
                    class="truncate text-sm font-semibold text-heading transition-colors duration-200 group-hover:text-brand-700 sm:text-base"
                >
                    {{ product.name }}
                </h3>

                <!-- ==================== Product Price ==================== -->

                <p
                    class="mt-2 text-lg font-extrabold tracking-tight text-heading sm:text-xl"
                >
                    {{ Number(product.price).toFixed(2) }}
                </p>

                <!-- ==================== Product Unit ==================== -->

                <p
                    v-if="product.unit"
                    class="mt-0.5 text-xs font-medium text-muted sm:text-sm"
                >
                    {{ t("per") }} {{ product.unit }}
                </p>
            </Link>

            <!-- ==================== Add To Cart ==================== -->

            <div class="mt-auto pt-4">
                <Button
                    type="button"
                    variant="primary"
                    size="md"
                    fullWidth
                    @click="emit('addToCart', product)"
                >
                    <ShoppingCart :size="17" :stroke-width="2" />

                    {{ t("addCart") }}
                </Button>
            </div>
        </div>
    </article>
</template>
