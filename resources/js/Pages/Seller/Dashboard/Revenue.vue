<script setup lang="ts">
import { computed } from "vue";

import SellerLayout from "@/Layouts/SellerLayout.vue";

// ==================== Types ====================

interface Order {
    id: number;
    status: string;
    created_at: string;
}

interface RevenueItem {
    id: number;
    order_id: number;
    quantity: number;
    unit_price: number;
    subtotal: number;
    order: Order;
}

// ==================== Props ====================

const props = defineProps<{
    revenueItems: RevenueItem[];
}>();

// ==================== Total Revenue ====================

const totalRevenue = computed(() => {
    return props.revenueItems.reduce((total, item) => {
        return total + Number(item.subtotal);
    }, 0);
});

// ==================== This Month Revenue ====================

const thisMonthRevenue = computed(() => {
    const now = new Date();

    return props.revenueItems.reduce((total, item) => {
        const orderDate = new Date(item.order.created_at);

        if (
            orderDate.getMonth() === now.getMonth() &&
            orderDate.getFullYear() === now.getFullYear()
        ) {
            return total + Number(item.subtotal);
        }

        return total;
    }, 0);
});

// ==================== Total Orders ====================

const totalOrders = computed(() => {
    return new Set(props.revenueItems.map((item) => item.order_id)).size;
});

// ==================== Helpers ====================

const formatPrice = (price: number) => {
    return Number(price).toFixed(2);
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString();
};
</script>

<template>
    <SellerLayout>
        <div class="mx-auto w-full max-w-7xl">
            <!-- ==================== Page Header ==================== -->

            <div class="mb-6">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                    Revenue
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Track your revenue from successfully delivered orders.
                </p>
            </div>

            <!-- ==================== Revenue Summary ==================== -->

            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <!-- Total Revenue -->

                <div
                    class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm"
                >
                    <p class="text-sm text-gray-500">Total Revenue</p>

                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        {{ formatPrice(totalRevenue) }}
                    </p>
                </div>

                <!-- This Month -->

                <div
                    class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm"
                >
                    <p class="text-sm text-gray-500">This Month</p>

                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        {{ formatPrice(thisMonthRevenue) }}
                    </p>
                </div>

                <!-- Delivered Orders -->

                <div
                    class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm"
                >
                    <p class="text-sm text-gray-500">Delivered Orders</p>

                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        {{ totalOrders }}
                    </p>
                </div>
            </div>

            <!-- ==================== Empty State ==================== -->

            <div
                v-if="!revenueItems.length"
                class="rounded-2xl border border-gray-100 bg-white p-8 text-center shadow-sm"
            >
                <h3 class="text-base font-semibold text-gray-900">
                    No revenue yet
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Revenue will appear here after your products are
                    successfully delivered.
                </p>
            </div>

            <!-- ==================== Revenue History ==================== -->

            <div
                v-else
                class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm"
            >
                <div class="border-b border-gray-100 px-5 py-4">
                    <h3 class="font-semibold text-gray-900">Revenue History</h3>
                </div>

                <div class="divide-y divide-gray-100">
                    <div
                        v-for="item in revenueItems"
                        :key="item.id"
                        class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <!-- Order Information -->

                        <div>
                            <p class="font-semibold text-gray-900">
                                Order #{{ item.order_id }}
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                {{ formatDate(item.order.created_at) }}
                            </p>
                        </div>

                        <!-- Revenue Information -->

                        <div class="sm:text-right">
                            <p class="font-semibold text-gray-900">
                                {{ formatPrice(item.subtotal) }}
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                {{ item.quantity }} ×
                                {{ formatPrice(item.unit_price) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SellerLayout>
</template>
