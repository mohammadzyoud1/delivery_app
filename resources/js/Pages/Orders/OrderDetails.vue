<script setup lang="ts">
// ==================== Imports ====================

import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import { Link } from "@inertiajs/vue3";
import {
    ArrowLeft,
    Check,
    Clock3,
    MapPin,
    Package,
    Truck,
} from "lucide-vue-next";

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

// ==================== Props ====================

defineProps<{
    order: Order;
}>();
</script>

<template>
    <CustomerLayout>
        <!-- ==================== Order Details Page ==================== -->

        <main class="bg-background">
            <div
                class="mx-auto w-full max-w-6xl px-5 py-6 sm:px-8 sm:py-8 lg:py-10"
            >
                <!-- ==================== Back Navigation ==================== -->

                <Link
                    href="/orders"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-muted transition-colors duration-200 hover:text-heading focus-visible:outline-none"
                >
                    <ArrowLeft :size="17" :stroke-width="2" />
                    Back to Orders
                </Link>

                <!-- ==================== Order Header ==================== -->

                <section
                    class="mt-5 overflow-hidden rounded-2xl border border-border bg-surface shadow-sm"
                >
                    <div class="p-5 sm:p-6">
                        <div
                            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div>
                                <p
                                    class="text-xs font-semibold uppercase tracking-wider text-muted"
                                >
                                    Order
                                </p>

                                <h1
                                    class="mt-1 text-2xl font-extrabold tracking-tight text-heading sm:text-3xl"
                                >
                                    #{{ order.id }}
                                </h1>

                                <p class="mt-1.5 text-sm text-muted">
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
                </section>

                <!-- ==================== Main Content ==================== -->

                <div class="mt-5 grid gap-5 lg:grid-cols-[minmax(0,1fr)_340px]">
                    <!-- ==================== Main Column ==================== -->

                    <div class="space-y-5">
                        <!-- ==================== Order Tracking ==================== -->

                        <section
                            class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                                >
                                    <Truck :size="20" :stroke-width="2" />
                                </div>

                                <div>
                                    <h2
                                        class="text-base font-bold text-heading sm:text-lg"
                                    >
                                        Order Tracking
                                    </h2>

                                    <p class="mt-0.5 text-xs text-muted">
                                        Follow the progress of your order.
                                    </p>
                                </div>
                            </div>

                            <!-- ==================== Status History ==================== -->

                            <div class="mt-7">
                                <div
                                    v-for="(
                                        history, index
                                    ) in order.status_histories"
                                    :key="history.id"
                                    class="relative flex gap-4"
                                >
                                    <!-- ==================== Timeline Line ==================== -->

                                    <div
                                        v-if="
                                            index <
                                            order.status_histories.length - 1
                                        "
                                        class="absolute left-4 top-9 h-[calc(100%-1.25rem)] w-px bg-border"
                                    ></div>

                                    <!-- ==================== Timeline Icon ==================== -->

                                    <div
                                        class="relative z-10 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-600 text-white shadow-sm"
                                    >
                                        <Check :size="15" :stroke-width="2.5" />
                                    </div>

                                    <!-- ==================== Status Information ==================== -->

                                    <div class="min-w-0 flex-1 pb-6 last:pb-0">
                                        <div
                                            class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between"
                                        >
                                            <p
                                                class="text-sm font-bold capitalize text-heading"
                                            >
                                                {{
                                                    history.status.replace(
                                                        /_/g,
                                                        " ",
                                                    )
                                                }}
                                            </p>

                                            <p
                                                class="text-xs font-medium text-muted"
                                            >
                                                {{ history.created_at }}
                                            </p>
                                        </div>

                                        <p
                                            v-if="history.note"
                                            class="mt-1.5 text-sm leading-6 text-muted"
                                        >
                                            {{ history.note }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- ==================== Order Items ==================== -->

                        <section
                            class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                                >
                                    <Package :size="20" :stroke-width="2" />
                                </div>

                                <div>
                                    <h2
                                        class="text-base font-bold text-heading sm:text-lg"
                                    >
                                        Items
                                    </h2>

                                    <p class="mt-0.5 text-xs text-muted">
                                        {{ order.items.length }}
                                        {{
                                            order.items.length === 1
                                                ? "item"
                                                : "items"
                                        }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6 space-y-4">
                                <div
                                    v-for="item in order.items"
                                    :key="item.id"
                                    class="flex gap-4 border-b border-border pb-4 last:border-b-0 last:pb-0"
                                >
                                    <!-- ==================== Product Image ==================== -->

                                    <div
                                        class="h-20 w-20 shrink-0 overflow-hidden rounded-xl border border-border bg-background"
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
                                                :size="20"
                                                :stroke-width="1.5"
                                            />
                                        </div>
                                    </div>

                                    <!-- ==================== Product Information ==================== -->

                                    <div class="min-w-0 flex-1">
                                        <p
                                            class="truncate text-sm font-bold text-heading sm:text-base"
                                        >
                                            {{ item.product.name }}
                                        </p>

                                        <p
                                            v-if="item.variant_name"
                                            class="mt-1 text-xs text-muted"
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

                                        <p
                                            class="mt-1 text-xs font-medium text-muted"
                                        >
                                            {{
                                                Number(item.unit_price).toFixed(
                                                    2,
                                                )
                                            }}
                                            each
                                        </p>
                                    </div>

                                    <!-- ==================== Item Total ==================== -->

                                    <p
                                        class="shrink-0 text-sm font-bold text-heading sm:text-base"
                                    >
                                        {{ Number(item.subtotal).toFixed(2) }}
                                    </p>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- ==================== Sidebar ==================== -->

                    <aside class="space-y-5 lg:sticky lg:top-6">
                        <!-- ==================== Delivery Information ==================== -->

                        <section
                            class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                                >
                                    <MapPin :size="20" :stroke-width="2" />
                                </div>

                                <div>
                                    <h2
                                        class="text-base font-bold text-heading"
                                    >
                                        Delivery Information
                                    </h2>

                                    <p class="mt-0.5 text-xs text-muted">
                                        Your delivery destination.
                                    </p>
                                </div>
                            </div>

                            <div
                                class="mt-5 rounded-xl border border-border bg-background p-4"
                            >
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-muted"
                                >
                                    Delivery Address
                                </p>

                                <p class="mt-2 text-sm leading-6 text-body">
                                    {{ order.delivery_address }}
                                </p>
                            </div>
                        </section>

                        <!-- ==================== Custom Request ==================== -->

                        <section
                            v-if="order.custom_request"
                            class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
                        >
                            <h2 class="text-base font-bold text-heading">
                                Custom Request
                            </h2>

                            <p class="mt-3 text-sm leading-6 text-muted">
                                {{ order.custom_request }}
                            </p>
                        </section>

                        <!-- ==================== Rejection Reason ==================== -->

                        <section
                            v-if="order.rejection_reason"
                            class="rounded-2xl border border-red-200 bg-red-50 p-5 sm:p-6"
                        >
                            <h2 class="text-base font-bold text-red-800">
                                Order Rejected
                            </h2>

                            <p class="mt-2 text-sm leading-6 text-red-700">
                                {{ order.rejection_reason }}
                            </p>
                        </section>

                        <!-- ==================== Order Total ==================== -->

                        <section
                            class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
                        >
                            <div
                                class="flex items-center justify-between gap-4"
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
                        </section>
                    </aside>
                </div>
            </div>
        </main>
    </CustomerLayout>
</template>
