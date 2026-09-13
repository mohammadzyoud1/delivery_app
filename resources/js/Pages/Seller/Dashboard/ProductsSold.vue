<script setup lang="ts">
import { computed } from "vue";

import SellerLayout from "@/Layouts/SellerLayout.vue";

// ==================== Types ====================

interface ProductImage {
    id: number;
    image: string;
}

interface Product {
    id: number;
    name: string;
    images: ProductImage[];
}

interface Variant {
    id: number;
    sku: string | null;
}

interface Order {
    id: number;
    status: string;
    created_at: string;
}

interface SoldItem {
    id: number;
    product_id: number;
    quantity: number;
    unit_price: number;
    subtotal: number;
    product: Product;
    variant: Variant | null;
    order: Order;
}

// ==================== Props ====================

const props = defineProps<{
    soldItems: SoldItem[];
}>();

// ==================== Group Products ====================

const soldProducts = computed(() => {
    const grouped = new Map<
        number,
        {
            product: Product;
            quantity: number;
            revenue: number;
        }
    >();

    props.soldItems.forEach((item) => {
        if (!grouped.has(item.product_id)) {
            grouped.set(item.product_id, {
                product: item.product,
                quantity: 0,
                revenue: 0,
            });
        }

        const currentProduct = grouped.get(item.product_id)!;

        currentProduct.quantity += Number(item.quantity);
        currentProduct.revenue += Number(item.subtotal);
    });

    return Array.from(grouped.values());
});

// ==================== Total Sold ====================

const totalSold = computed(() => {
    return props.soldItems.reduce((total, item) => {
        return total + Number(item.quantity);
    }, 0);
});

// ==================== Total Revenue ====================

const totalRevenue = computed(() => {
    return props.soldItems.reduce((total, item) => {
        return total + Number(item.subtotal);
    }, 0);
});

// ==================== Helpers ====================

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
                    Products Sold
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    View your successfully delivered sales.
                </p>
            </div>

            <!-- ==================== Summary ==================== -->

            <div class="mb-6 grid grid-cols-2 gap-4">
                <div
                    class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm"
                >
                    <p class="text-sm text-gray-500">Products Sold</p>

                    <p class="mt-1 text-2xl font-bold text-gray-900">
                        {{ totalSold }}
                    </p>
                </div>

                <div
                    class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm"
                >
                    <p class="text-sm text-gray-500">Sales Revenue</p>

                    <p class="mt-1 text-2xl font-bold text-gray-900">
                        {{ formatPrice(totalRevenue) }}
                    </p>
                </div>
            </div>

            <!-- ==================== Empty State ==================== -->

            <div
                v-if="!soldProducts.length"
                class="rounded-2xl border border-gray-100 bg-white p-8 text-center shadow-sm"
            >
                <h3 class="text-base font-semibold text-gray-900">
                    No products sold yet
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Products will appear here after an order is delivered.
                </p>
            </div>

            <!-- ==================== Sold Products ==================== -->

            <div
                v-else
                class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3"
            >
                <div
                    v-for="item in soldProducts"
                    :key="item.product.id"
                    class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm"
                >
                    <!-- ==================== Product Image ==================== -->

                    <div class="aspect-square bg-gray-100">
                        <img
                            v-if="item.product.images.length"
                            :src="`/storage/${item.product.images[0].image}`"
                            :alt="item.product.name"
                            class="h-full w-full object-cover"
                        />

                        <div
                            v-else
                            class="flex h-full items-center justify-center text-sm text-gray-400"
                        >
                            No image
                        </div>
                    </div>

                    <!-- ==================== Product Information ==================== -->

                    <div class="p-4">
                        <h3 class="truncate font-semibold text-gray-900">
                            {{ item.product.name }}
                        </h3>

                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-sm text-gray-500">
                                Quantity sold
                            </span>

                            <span class="font-semibold text-gray-900">
                                {{ item.quantity }}
                            </span>
                        </div>

                        <div class="mt-2 flex items-center justify-between">
                            <span class="text-sm text-gray-500"> Revenue </span>

                            <span class="font-semibold text-gray-900">
                                {{ formatPrice(item.revenue) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SellerLayout>
</template>
