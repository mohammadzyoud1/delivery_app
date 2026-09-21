<script setup lang="ts">
// ==================== Imports ====================

import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import { Link } from "@inertiajs/vue3";
import { ClipboardList, Package } from "lucide-vue-next";

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
        <!-- ==================== Orders Page ==================== -->

        <main class="bg-background">
            <div
                class="mx-auto w-full max-w-7xl px-5 py-6 sm:px-8 sm:py-8 lg:py-10"
            >
                <!-- ==================== Page Header ==================== -->

                <div class="mb-7">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                        >
                            <ClipboardList :size="21" :stroke-width="2" />
                        </div>

                        <div>
                            <h1
                                class="text-2xl font-extrabold tracking-tight text-heading sm:text-3xl"
                            >
                                My Orders
                            </h1>

                            <p class="mt-1 text-sm text-muted">
                                View and track your orders.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ==================== Empty Orders ==================== -->

                <div
                    v-if="!orders.length"
                    class="rounded-2xl border border-border bg-surface px-6 py-12 text-center shadow-sm sm:px-8"
                >
                    <div
                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"
                    >
                        <Package :size="26" :stroke-width="1.8" />
                    </div>

                    <h2 class="mt-5 text-base font-bold text-heading">
                        No orders yet
                    </h2>

                    <p class="mx-auto mt-1.5 max-w-sm text-sm text-muted">
                        Once you place an order, it will appear here so you can
                        track it.
                    </p>

                    <Link
                        href="/"
                        class="mt-6 inline-flex items-center justify-center rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:bg-brand-700 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2"
                    >
                        Start Shopping
                    </Link>
                </div>

                <!-- ==================== Orders List ==================== -->

                <div v-else class="grid gap-5 lg:grid-cols-2">
                    <article
                        v-for="order in orders"
                        :key="order.id"
                        class="flex h-full flex-col overflow-hidden rounded-2xl border border-border bg-surface shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-lg hover:shadow-slate-900/5"
                    >
                        <!-- ==================== Order Header ==================== -->

                        <div class="border-b border-border p-5 sm:p-6">
                            <div
                                class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"
                            >
                                <div class="min-w-0">
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wider text-muted"
                                    >
                                        Order
                                    </p>

                                    <h2
                                        class="mt-1 text-base font-extrabold text-heading sm:text-lg"
                                    >
                                        #{{ order.id }}
                                    </h2>

                                    <p class="mt-1 text-xs text-muted">
                                        {{ order.created_at }}
                                    </p>
                                </div>

                                <span
                                    class="w-fit rounded-full border px-3.5 py-1.5 text-xs font-bold capitalize"
                                    :class="
                                        order.status === 'rejected'
                                            ? 'border-red-200 bg-red-50 text-red-700'
                                            : order.status === 'delivered'
                                              ? 'border-green-200 bg-green-50 text-green-700'
                                              : 'border-brand-100 bg-brand-50 text-brand-700'
                                    "
                                >
                                    {{ order.status.replace(/_/g, " ") }}
                                </span>
                            </div>
                        </div>

                        <!-- ==================== Order Items ==================== -->

                        <div class="flex flex-1 flex-col p-5 sm:p-6">
                            <div class="space-y-4">
                                <div
                                    v-for="item in order.items"
                                    :key="item.id"
                                    class="flex gap-3.5"
                                >
                                    <!-- ==================== Product Image ==================== -->

                                    <div
                                        class="h-16 w-16 shrink-0 overflow-hidden rounded-xl border border-border bg-background"
                                    >
                                        <img
                                            v-if="item.product.images.length"
                                            :src="`/storage/${item.product.images[0].image}`"
                                            :alt="item.product.name"
                                            class="h-full w-full object-cover"
                                        />

                                        <div
                                            v-else
                                            class="flex h-full items-center justify-center text-muted"
                                        >
                                            <Package
                                                :size="18"
                                                :stroke-width="1.5"
                                            />
                                        </div>
                                    </div>

                                    <!-- ==================== Product Information ==================== -->

                                    <div class="min-w-0 flex-1">
                                        <p
                                            class="truncate text-sm font-bold text-heading"
                                        >
                                            {{ item.product.name }}
                                        </p>

                                        <p
                                            v-if="item.variant_name"
                                            class="mt-1 truncate text-xs text-muted"
                                        >
                                            {{ item.variant_name }}
                                        </p>

                                        <p
                                            class="mt-1.5 text-xs font-medium text-muted"
                                        >
                                            Qty:
                                            <span class="font-bold text-body">
                                                {{ item.quantity }}
                                            </span>
                                        </p>
                                    </div>

                                    <!-- ==================== Item Total ==================== -->

                                    <p
                                        class="shrink-0 text-sm font-bold text-heading"
                                    >
                                        {{ Number(item.subtotal).toFixed(2) }}
                                    </p>
                                </div>
                            </div>

                            <!-- ==================== Order Total ==================== -->

                            <div
                                class="mt-5 flex items-center justify-between border-t border-border pt-4"
                            >
                                <span class="text-sm font-medium text-muted">
                                    Total
                                </span>

                                <span
                                    class="text-xl font-extrabold tracking-tight text-heading"
                                >
                                    {{ Number(order.total_amount).toFixed(2) }}
                                </span>
                            </div>

                            <!-- ==================== Order Details ==================== -->

                            <div class="mt-auto pt-5">
                                <Link
                                    :href="`/orders/${order.id}`"
                                    class="flex w-full items-center justify-center rounded-xl bg-brand-600 px-4 py-3 text-sm font-bold text-white shadow-sm transition-all duration-200 hover:bg-brand-700 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2"
                                >
                                    View Order
                                </Link>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </main>
    </CustomerLayout>
</template>
