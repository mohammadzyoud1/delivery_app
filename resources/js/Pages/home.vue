<script setup lang="ts">
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import ProductCard from "@/Components/Products/ProductCard.vue";
import CategorySlider from "@/Components/Categories/CategorySlider.vue";
import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import ProductSearch from "@/Components/Products/ProductSearch.vue";
import Button from "@/Components/UI/Button.vue";
interface Category {
    id: number;
    name: string;
    image: string | null;
}
const search = ref("");
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
const props = defineProps<{
    categories: Category[];
    products: Product[];
}>();
const logout = () => {
    router.post("/logout");
};
</script>

<template>
    <CustomerLayout>
        <!-- ==================== Home Content ==================== -->

        <ProductSearch v-model="search" />
        <div class="mt-6">
            <CategorySlider :categories="categories" />
            <!-- ==================== Products ==================== -->

            <div class="mt-8">
                <h2 class="text-xl font-bold text-gray-900">Products</h2>

                <div
                    v-if="!props.products.length"
                    class="mt-4 rounded-2xl bg-white p-8 text-center"
                >
                    <p class="text-sm text-gray-500">No products available.</p>
                </div>

                <div
                    v-else
                    class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                >
                    <ProductCard
                        v-for="product in props.products"
                        :key="product.id"
                        :product="product"
                        @favorite="console.log('favorite', $event)"
                        @add-to-cart="handleAddToCart"
                    />
                </div>
            </div>
        </div>
        <Button type="button" variant="danger" fullWidth @click="logout">
            Logout
        </Button>
    </CustomerLayout>
</template>
