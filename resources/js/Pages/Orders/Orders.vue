<script setup lang="ts">
// ==================== Imports ====================

import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import { Link } from "@inertiajs/vue3";

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

interface ProductVariant {
    id: number;
    price: number;
}

interface OrderItem {
    id: number;
    quantity: number;
    unit_price: number;
    subtotal: number;
    variant_name: string | null;
    product: Product;
    variant: ProductVariant | null;
}

interface Order {
    id: number;
    status: string;
    delivery_address: string;
    total_amount: number;
    created_at: string;
    items: OrderItem[];
}

// ==================== Props ====================

defineProps<{
    orders: Order[];
}>();
</script>

<template>
    <CustomerLayout>
        <!-- ==================== Page Header ==================== -->

        <div class="mx-auto max-w-6xl">
            <h1 class="text-2xl font-bold text-gray-900">My Orders</h1>

            <p class="mt-1 text-sm text-gray-500">
                View and track your orders.
            </p>

            <!-- ==================== Empty Orders ==================== -->

            <div
                v-if="!orders.length"
                class="mt-6 rounded-2xl bg-white p-8 text-center shadow-sm"
            >
                <p class="text-gray-500">You haven't placed any orders yet.</p>
            </div>

            <!-- ==================== Orders List ==================== -->

            <div v-else class="mt-6 space-y-4">
                <div
                    v-for="order in orders"
                    :key="order.id"
                    class="rounded-2xl bg-white p-5 shadow-sm"
                >
                    <!-- ==================== Order Header ==================== -->

                    <div
                        class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <h2 class="font-semibold text-gray-900">
                                Order #{{ order.id }}
                            </h2>

                            <p class="mt-1 text-xs text-gray-500">
                                {{ order.created_at }}
                            </p>
                        </div>

                        <span
                            class="w-fit rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold capitalize text-gray-700"
                        >
                            {{ order.status.replace(/_/g, " ") }}
                        </span>
                    </div>

                    <!-- ==================== Order Items ==================== -->

                    <div class="mt-4 space-y-3">
                        <div
                            v-for="item in order.items"
                            :key="item.id"
                            class="flex gap-3"
                        >
                            <div
                                class="h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-gray-100"
                            >
                                <img
                                    v-if="item.product.images.length"
                                    :src="`/storage/${item.product.images[0].image}`"
                                    :alt="item.product.name"
                                    class="h-full w-full object-cover"
                                />

                                <div
                                    v-else
                                    class="flex h-full items-center justify-center text-[10px] text-gray-400"
                                >
                                    No image
                                </div>
                            </div>

                            <div class="min-w-0 flex-1">
                                <p
                                    class="truncate text-sm font-semibold text-gray-900"
                                >
                                    {{ item.product.name }}
                                </p>

                                <p
                                    v-if="item.variant_name"
                                    class="mt-1 text-xs text-gray-500"
                                >
                                    {{ item.variant_name }}
                                </p>

                                <p class="mt-1 text-xs text-gray-500">
                                    Qty: {{ item.quantity }}
                                </p>
                            </div>

                            <p class="text-sm font-semibold text-gray-900">
                                {{ Number(item.subtotal).toFixed(2) }}
                            </p>
                        </div>
                    </div>

                    <!-- ==================== Order Footer ==================== -->

                    <div
                        class="mt-5 flex items-center justify-between border-t border-gray-100 pt-4"
                    >
                        <span class="text-sm font-medium text-gray-500">
                            Total
                        </span>

                        <span class="text-lg font-bold text-gray-900">
                            {{ Number(order.total_amount).toFixed(2) }}
                        </span>
                    </div>

                    <!-- ==================== Order Details ==================== -->

                    <div class="mt-4">
                        <Link
                            :href="`/orders/${order.id}`"
                            class="block w-full rounded-xl bg-gray-900 px-4 py-3 text-center text-sm font-semibold text-white transition hover:bg-gray-800"
                        >
                            View Order
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>
