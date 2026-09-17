<script setup lang="ts">
// ==================== Imports ====================
import Button from "@/Components/UI/Button.vue";
import { Heart, ShoppingCart } from "lucide-vue-next";
import { Link } from "@inertiajs/vue3";

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

    <div
        class="group overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
    >
        <!-- ==================== Product Image ==================== -->

        <Link :href="`/products/${product.id}`" class="block">
            <div class="relative aspect-square bg-gray-100">
                <img
                    v-if="product.images.length"
                    :src="`/storage/${product.images[0].image}`"
                    :alt="product.name"
                    class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                />

                <div
                    v-else
                    class="flex h-full items-center justify-center text-sm text-gray-400"
                >
                    No image
                </div>

                <!-- ==================== Favorite Button ==================== -->

                <button
                    type="button"
                    aria-label="Add to favorites"
                    class="absolute right-3 top-3 flex h-10 w-10 items-center justify-center rounded-full bg-white/90 text-gray-700 shadow-sm backdrop-blur transition hover:bg-white hover:text-red-500"
                    @click.prevent.stop="emit('favorite', product)"
                >
                    <Heart :size="20" />
                </button>
            </div>
        </Link>

        <!-- ==================== Product Information ==================== -->

        <div class="p-4">
            <Link :href="`/products/${product.id}`" class="block">
                <h3 class="truncate text-base font-semibold text-gray-900">
                    {{ product.name }}
                </h3>

                <p class="mt-2 text-lg font-bold text-gray-900">
                    {{ Number(product.price).toFixed(2) }}
                </p>

                <p v-if="product.unit" class="mt-1 text-sm text-gray-500">
                    Per {{ product.unit }}
                </p>
            </Link>

            <!-- ==================== Add To Cart ==================== -->

            <Button
                type="button"
                variant="primary"
                size="md"
                fullWidth
                @click="emit('addToCart', product)"
            >
                <ShoppingCart :size="18" />
                Add to Cart
            </Button>
        </div>
    </div>
</template>
