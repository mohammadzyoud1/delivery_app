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
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                Dashboard
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Overview of what's happening in Deliver.
            </p>
        </div>

        <!-- ==================== Statistics ==================== -->

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
            <div class="rounded-2xl border border-gray-200 bg-white p-5">
                <ClipboardList class="mb-4 h-5 w-5 text-gray-500" />

                <p class="text-sm text-gray-500">Orders</p>

                <p class="mt-1 text-2xl font-bold text-gray-900">
                    {{ props.stats.orders }}
                </p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5">
                <Users class="mb-4 h-5 w-5 text-gray-500" />

                <p class="text-sm text-gray-500">Users</p>

                <p class="mt-1 text-2xl font-bold text-gray-900">
                    {{ props.stats.users }}
                </p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5">
                <ShoppingBag class="mb-4 h-5 w-5 text-gray-500" />

                <p class="text-sm text-gray-500">Sellers</p>

                <p class="mt-1 text-2xl font-bold text-gray-900">
                    {{ props.stats.sellers }}
                </p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5">
                <Truck class="mb-4 h-5 w-5 text-gray-500" />

                <p class="text-sm text-gray-500">Delivery</p>

                <p class="mt-1 text-2xl font-bold text-gray-900">
                    {{ props.stats.delivery }}
                </p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5">
                <Package class="mb-4 h-5 w-5 text-gray-500" />

                <p class="text-sm text-gray-500">Products</p>

                <p class="mt-1 text-2xl font-bold text-gray-900">
                    {{ props.stats.products }}
                </p>
            </div>
        </div>

        <!-- ==================== Recent Orders ==================== -->

        <div
            class="mt-8 overflow-hidden rounded-2xl border border-gray-200 bg-white"
        >
            <div
                class="flex items-center justify-between border-b border-gray-100 px-5 py-4 sm:px-6"
            >
                <div>
                    <h2 class="font-semibold text-gray-900">Recent Orders</h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Latest activity in the store.
                    </p>
                </div>

                <Link
                    href="/admin/orders"
                    class="flex items-center gap-1 text-sm font-medium text-gray-700 hover:text-gray-900"
                >
                    View all

                    <ArrowRight class="h-4 w-4" />
                </Link>
            </div>

            <div
                v-if="props.recentOrders.length === 0"
                class="p-8 text-center text-sm text-gray-500"
            >
                No orders yet.
            </div>

            <div v-else class="divide-y divide-gray-100">
                <div
                    v-for="order in props.recentOrders"
                    :key="order.id"
                    class="flex items-center justify-between gap-4 px-5 py-4 sm:px-6"
                >
                    <div>
                        <p class="font-medium text-gray-900">
                            Order #{{ order.id }}
                        </p>

                        <p class="mt-1 text-sm capitalize text-gray-500">
                            {{ formatStatus(order.status) }}
                        </p>
                    </div>

                    <p class="font-semibold text-gray-900">
                        {{ Number(order.total_amount).toFixed(2) }}
                    </p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
