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

interface StatusHistory {
    id: number;
    status: string;
    note: string | null;
    created_at: string;
}

interface Order {
    id: number;
    status: string;
    delivery_address: string;
    custom_request: string | null;
    rejection_reason: string | null;
    total_amount: number;
    created_at: string;
    items: OrderItem[];
    status_histories: StatusHistory[];
}

const orderStatuses = [
    "accepted",
    "preparing",
    "on_the_way",
    "at_the_door",
    "delivered",
];
// ==================== Props ====================

defineProps<{
    order: Order;
}>();
</script>

<template>
    <CustomerLayout>
        <div class="mx-auto max-w-5xl">
            <!-- ==================== Back ==================== -->

            <Link
                href="/orders"
                class="inline-flex items-center text-sm font-medium text-gray-600 transition hover:text-gray-900"
            >
                ← Back to Orders
            </Link>

            <!-- ==================== Order Header ==================== -->

            <div class="mt-4 rounded-2xl bg-white p-5 shadow-sm sm:p-6">
                <div
                    class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            Order #{{ order.id }}
                        </h1>

                        <p class="mt-1 text-sm text-gray-500">
                            {{ order.created_at }}
                        </p>
                    </div>

                    <span
                        class="w-fit rounded-full bg-gray-100 px-3 py-1 text-sm font-semibold capitalize text-gray-700"
                    >
                        {{ order.status.replace(/_/g, " ") }}
                    </span>
                </div>
            </div>

            <!-- ==================== Order Tracking ==================== -->

            <div class="mt-4 rounded-2xl bg-white p-5 shadow-sm sm:p-6">
                <h2 class="text-lg font-bold text-gray-900">Order Tracking</h2>

                <div class="mt-5 space-y-5">
                    <div
                        v-for="history in order.status_histories"
                        :key="history.id"
                        class="flex gap-3"
                    >
                        <div
                            class="mt-1 h-3 w-3 shrink-0 rounded-full bg-gray-900"
                        ></div>

                        <div class="min-w-0">
                            <p class="font-semibold capitalize text-gray-900">
                                {{ history.status.replace(/_/g, " ") }}
                            </p>

                            <p
                                v-if="history.note"
                                class="mt-1 text-sm text-gray-500"
                            >
                                {{ history.note }}
                            </p>

                            <p class="mt-1 text-xs text-gray-400">
                                {{ history.created_at }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==================== Order Items ==================== -->

            <div class="mt-4 rounded-2xl bg-white p-5 shadow-sm sm:p-6">
                <h2 class="text-lg font-bold text-gray-900">Items</h2>

                <div class="mt-5 space-y-4">
                    <div
                        v-for="item in order.items"
                        :key="item.id"
                        class="flex gap-4"
                    >
                        <div
                            class="h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-gray-100"
                        >
                            <img
                                v-if="item.product.images.length"
                                :src="`/storage/${item.product.images[0].image}`"
                                :alt="item.product.name"
                                class="h-full w-full object-cover"
                            />

                            <div
                                v-else
                                class="flex h-full items-center justify-center text-xs text-gray-400"
                            >
                                No image
                            </div>
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-gray-900">
                                {{ item.product.name }}
                            </p>

                            <p
                                v-if="item.variant_name"
                                class="mt-1 text-sm text-gray-500"
                            >
                                {{ item.variant_name }}
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                Qty: {{ item.quantity }}
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                {{ Number(item.unit_price).toFixed(2) }}
                                each
                            </p>
                        </div>

                        <p class="font-semibold text-gray-900">
                            {{ Number(item.subtotal).toFixed(2) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- ==================== Delivery Information ==================== -->

            <div class="mt-4 rounded-2xl bg-white p-5 shadow-sm sm:p-6">
                <h2 class="text-lg font-bold text-gray-900">
                    Delivery Information
                </h2>

                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-500">
                        Delivery Address
                    </p>

                    <p class="mt-1 text-sm text-gray-900">
                        {{ order.delivery_address }}
                    </p>
                </div>
            </div>

            <!-- ==================== Rejection Reason ==================== -->

            <div
                v-if="order.rejection_reason"
                class="mt-4 rounded-2xl border border-red-200 bg-red-50 p-5"
            >
                <h2 class="font-bold text-red-800">Order Rejected</h2>

                <p class="mt-2 text-sm text-red-700">
                    {{ order.rejection_reason }}
                </p>
            </div>

            <!-- ==================== Order Total ==================== -->

            <div class="mt-4 rounded-2xl bg-white p-5 shadow-sm sm:p-6">
                <div class="flex items-center justify-between">
                    <span class="font-medium text-gray-500"> Total </span>

                    <span class="text-xl font-bold text-gray-900">
                        {{ Number(order.total_amount).toFixed(2) }}
                    </span>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>
