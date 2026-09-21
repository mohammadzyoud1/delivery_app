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

            <div
                class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
            >
                <div>
                    <p
                        class="mb-2 text-xs font-bold uppercase tracking-[0.18em] text-brand-600"
                    >
                        Seller Center
                    </p>

                    <h1
                        class="text-2xl font-bold tracking-tight text-heading sm:text-3xl"
                    >
                        Revenue
                    </h1>

                    <p class="mt-2 max-w-2xl text-sm text-muted sm:text-base">
                        Track your earnings from successfully delivered orders.
                    </p>
                </div>
            </div>

            <!-- ==================== Revenue Summary ==================== -->

            <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <!-- Total Revenue -->

                <div
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm transition-shadow hover:shadow-md"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-muted">
                                Total Revenue
                            </p>

                            <p
                                class="mt-2 text-2xl font-bold tracking-tight text-heading"
                            >
                                {{ formatPrice(totalRevenue) }}
                            </p>
                        </div>

                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                        >
                            <span class="text-lg font-bold">$</span>
                        </div>
                    </div>
                </div>

                <!-- This Month -->

                <div
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm transition-shadow hover:shadow-md"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-muted">
                                This Month
                            </p>

                            <p
                                class="mt-2 text-2xl font-bold tracking-tight text-heading"
                            >
                                {{ formatPrice(thisMonthRevenue) }}
                            </p>
                        </div>

                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                        >
                            <span class="text-lg font-bold">$</span>
                        </div>
                    </div>
                </div>

                <!-- Delivered Orders -->

                <div
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm transition-shadow hover:shadow-md"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-muted">
                                Delivered Orders
                            </p>

                            <p
                                class="mt-2 text-2xl font-bold tracking-tight text-heading"
                            >
                                {{ totalOrders }}
                            </p>
                        </div>

                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                        >
                            <span class="text-lg font-bold">#</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==================== Empty State ==================== -->

            <div
                v-if="!revenueItems.length"
                class="rounded-2xl border border-border bg-surface px-6 py-12 text-center shadow-sm"
            >
                <div
                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"
                >
                    <span class="text-xl font-bold">$</span>
                </div>

                <h3 class="mt-4 text-base font-semibold text-heading">
                    No revenue yet
                </h3>

                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-muted">
                    Revenue will appear here after your products are
                    successfully delivered.
                </p>
            </div>

            <!-- ==================== Revenue History ==================== -->

            <div
                v-else
                class="overflow-hidden rounded-2xl border border-border bg-surface shadow-sm"
            >
                <!-- Section Header -->

                <div
                    class="flex flex-col gap-1 border-b border-border px-5 py-4 sm:px-6"
                >
                    <h2 class="text-base font-semibold text-heading">
                        Revenue History
                    </h2>

                    <p class="text-sm text-muted">
                        Earnings from your delivered order items.
                    </p>
                </div>

                <!-- Revenue Items -->

                <div class="divide-y divide-border">
                    <div
                        v-for="item in revenueItems"
                        :key="item.id"
                        class="flex flex-col gap-4 px-5 py-5 transition-colors hover:bg-background sm:flex-row sm:items-center sm:justify-between sm:px-6"
                    >
                        <!-- Order Information -->

                        <div class="min-w-0">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-sm font-bold text-brand-600"
                                >
                                    $
                                </div>

                                <div class="min-w-0">
                                    <p
                                        class="truncate font-semibold text-heading"
                                    >
                                        Order #{{ item.order_id }}
                                    </p>

                                    <p class="mt-0.5 text-sm text-muted">
                                        {{ formatDate(item.order.created_at) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Revenue Information -->

                        <div
                            class="flex items-center justify-between gap-6 sm:justify-end"
                        >
                            <div class="sm:text-right">
                                <p class="font-semibold text-heading">
                                    {{ formatPrice(item.subtotal) }}
                                </p>

                                <p class="mt-1 text-sm text-muted">
                                    {{ item.quantity }} ×
                                    {{ formatPrice(item.unit_price) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SellerLayout>
</template>
