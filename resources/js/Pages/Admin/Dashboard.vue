<script setup lang="ts">
// ==================== Imports ====================

import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import {
    ArrowRight,
    ClipboardList,
    Package,
    ShoppingBag,
    Truck,
    Users,
} from "lucide-vue-next";
import { t } from "@/Utils/i18n";

// ==================== Types ====================

interface Stats {
    orders: number;
    users: number;
    sellers: number;
    delivery: number;
    products: number;
}

interface RecentOrder {
    id: number;
    status: string;
    total_amount: number;
}

// ==================== Props ====================

const props = defineProps<{
    stats: Stats;
    recentOrders: RecentOrder[];
}>();

// ==================== Helpers ====================

const formatStatus = (status: string): string => {
    return status.replace(/_/g, " ");
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <!-- ==================== Page Header ==================== -->

        <div class="mb-8">
            <p
                class="mb-2 text-xs font-bold uppercase tracking-[0.18em] text-brand-600"
            ></p>

            <h1
                class="text-2xl font-bold tracking-tight text-heading sm:text-3xl"
            >
                {{ t("adminDashboard") }}
            </h1>

            <p class="mt-2 text-sm text-muted sm:text-base">
                {{ t("overviewAdmin") }}
            </p>
        </div>

        <!-- ==================== Statistics ==================== -->

        <div class="grid grid-cols-2 gap-4 xl:grid-cols-5">
            <!-- Orders -->

            <div
                class="rounded-2xl border border-border bg-surface p-5 shadow-sm transition-shadow hover:shadow-md"
            >
                <div
                    class="mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                >
                    <ClipboardList class="h-5 w-5" />
                </div>

                <p class="text-sm font-medium text-muted">{{ t("orders") }}</p>

                <p class="mt-1 text-2xl font-bold tracking-tight text-heading">
                    {{ props.stats.orders }}
                </p>
            </div>

            <!-- Users -->

            <div
                class="rounded-2xl border border-border bg-surface p-5 shadow-sm transition-shadow hover:shadow-md"
            >
                <div
                    class="mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                >
                    <Users class="h-5 w-5" />
                </div>

                <p class="text-sm font-medium text-muted">{{ t("users") }}</p>

                <p class="mt-1 text-2xl font-bold tracking-tight text-heading">
                    {{ props.stats.users }}
                </p>
            </div>

            <!-- Sellers -->

            <div
                class="rounded-2xl border border-border bg-surface p-5 shadow-sm transition-shadow hover:shadow-md"
            >
                <div
                    class="mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                >
                    <ShoppingBag class="h-5 w-5" />
                </div>

                <p class="text-sm font-medium text-muted">{{ t("sellers") }}</p>

                <p class="mt-1 text-2xl font-bold tracking-tight text-heading">
                    {{ props.stats.sellers }}
                </p>
            </div>

            <!-- Delivery -->

            <div
                class="rounded-2xl border border-border bg-surface p-5 shadow-sm transition-shadow hover:shadow-md"
            >
                <div
                    class="mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                >
                    <Truck class="h-5 w-5" />
                </div>

                <p class="text-sm font-medium text-muted">
                    {{ t("delivery") }}
                </p>

                <p class="mt-1 text-2xl font-bold tracking-tight text-heading">
                    {{ props.stats.delivery }}
                </p>
            </div>

            <!-- Products -->

            <div
                class="rounded-2xl border border-border bg-surface p-5 shadow-sm transition-shadow hover:shadow-md"
            >
                <div
                    class="mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                >
                    <Package class="h-5 w-5" />
                </div>

                <p class="text-sm font-medium text-muted">
                    {{ t("products") }}
                </p>

                <p class="mt-1 text-2xl font-bold tracking-tight text-heading">
                    {{ props.stats.products }}
                </p>
            </div>
        </div>

        <!-- ==================== Recent Orders ==================== -->

        <div
            class="mt-8 overflow-hidden rounded-2xl border border-border bg-surface shadow-sm"
        >
            <!-- Section Header -->

            <div
                class="flex flex-col gap-3 border-b border-border px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6"
            >
                <div>
                    <h2 class="font-semibold text-heading">
                        {{ t("recentOrders") }}
                    </h2>

                    <p class="mt-1 text-sm text-muted">
                        {{ t("recentOrdersDescription") }}
                    </p>
                </div>

                <Link
                    href="/admin/orders"
                    class="group flex w-fit items-center gap-1.5 rounded-lg px-2 py-1.5 text-sm font-semibold text-brand-600 transition hover:bg-brand-50 hover:text-brand-700 focus-visible:outline-none"
                >
                    {{ t("seeAll") }}

                    <ArrowRight
                        class="h-4 w-4 transition-transform group-hover:translate-x-0.5"
                    />
                </Link>
            </div>

            <!-- Empty State -->

            <div
                v-if="props.recentOrders.length === 0"
                class="px-6 py-12 text-center"
            >
                <div
                    class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"
                >
                    <ClipboardList class="h-5 w-5" />
                </div>

                <h3 class="mt-4 text-sm font-semibold text-heading">
                    {{ t("noOrders") }}
                </h3>

                <p class="mt-1 text-sm text-muted">
                    {{ t("noRecentOrders") }}
                </p>
            </div>

            <!-- Orders -->

            <div v-else class="divide-y divide-border">
                <div
                    v-for="order in props.recentOrders"
                    :key="order.id"
                    class="flex flex-col gap-3 px-5 py-4 transition-colors hover:bg-background sm:flex-row sm:items-center sm:justify-between sm:px-6"
                >
                    <!-- Order Information -->

                    <div class="min-w-0">
                        <p class="font-semibold text-heading">
                            Order #{{ order.id }}
                        </p>

                        <p class="mt-1 text-sm capitalize text-muted">
                            {{ t(order.status) }}
                        </p>
                    </div>

                    <!-- Order Total -->

                    <p class="text-base font-bold text-heading sm:text-right">
                        {{ Number(order.total_amount).toFixed(2) }}
                    </p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
