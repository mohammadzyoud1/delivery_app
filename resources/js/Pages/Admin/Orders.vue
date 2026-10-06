<script setup lang="ts">
// ==================== Imports ====================

import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { Eye } from "lucide-vue-next";
import { t } from "@/Utils/i18n";

// ==================== Types ====================

interface Customer {
    id: number;
    first_name: string;
    last_name: string;
}

interface Product {
    id: number;
    name: string;
}

interface Variant {
    id: number;
    sku: string;
}

interface OrderItem {
    id: number;
    quantity: number;
    subtotal: number;
    product: Product;
    variant: Variant | null;
}

interface Order {
    id: number;
    status: string;
    total_amount: number;
    created_at: string;
    customer: Customer;
    items: OrderItem[];
}

// ==================== Props ====================

const props = defineProps<{
    orders: Order[];
}>();

// ==================== Helpers ====================

const formatStatus = (status: string): string => {
    return status.replace(/_/g, " ");
};

const formatDate = (date: string): string => {
    return new Date(date).toLocaleDateString();
};

const statusClasses = (status: string): string => {
    switch (status) {
        case "accepted":
            return "bg-blue-50 text-blue-700";

        case "preparing":
            return "bg-yellow-50 text-yellow-700";

        case "on_the_way":
            return "bg-purple-50 text-purple-700";

        case "at_the_door":
            return "bg-orange-50 text-orange-700";

        case "delivered":
            return "bg-green-50 text-green-700";

        case "rejected":
            return "bg-red-50 text-red-700";

        default:
            return "bg-gray-100 text-gray-700";
    }
};
</script>

<template>
    <Head title="Admin Orders" />

    <AdminLayout>
        <!-- ==================== Page Header ==================== -->

        <div class="mb-8">
            <p
                class="mb-2 text-xs font-bold uppercase tracking-[0.18em] text-brand-600"
            >
                {{ t("adminPanel") }}
            </p>

            <h1
                class="text-2xl font-bold tracking-tight text-heading sm:text-3xl"
            >
                {{ t("orders") }}
            </h1>

            <p class="mt-2 text-sm text-muted sm:text-base">
                {{ t("manageAndMonitor") }}
            </p>
        </div>

        <!-- ==================== Orders Container ==================== -->

        <div
            class="overflow-hidden rounded-2xl border border-border bg-surface shadow-sm"
        >
            <!-- ==================== Empty State ==================== -->

            <div
                v-if="props.orders.length === 0"
                class="px-6 py-14 text-center"
            >
                <div
                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"
                >
                    <ClipboardList class="h-6 w-6" />
                </div>

                <h2 class="mt-4 text-base font-semibold text-heading">
                    {{ t("noOrders") }}
                </h2>

                <p class="mt-1 text-sm text-muted">
                    {{ t("customerOrdersAppear") }}
                </p>
            </div>

            <!-- ==================== Desktop Table ==================== -->

            <div v-else class="hidden overflow-x-auto md:block">
                <table class="w-full text-left">
                    <!-- Table Header -->

                    <thead class="border-b border-border bg-background">
                        <tr>
                            <th
                                class="px-6 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("order") }}
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("customer") }}
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("products") }}
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("total") }}
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("status") }}
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("date") }}
                            </th>

                            <th
                                class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wide text-muted"
                            ></th>
                        </tr>
                    </thead>

                    <!-- Table Body -->

                    <tbody class="divide-y divide-border">
                        <tr
                            v-for="order in props.orders"
                            :key="order.id"
                            class="transition-colors hover:bg-background"
                        >
                            <!-- Order -->

                            <td class="whitespace-nowrap px-6 py-4">
                                <p class="font-semibold text-heading">
                                    #{{ order.id }}
                                </p>
                            </td>

                            <!-- Customer -->

                            <td class="px-6 py-4">
                                <p class="font-medium text-heading">
                                    {{ order.customer.first_name }}
                                    {{ order.customer.last_name }}
                                </p>
                            </td>

                            <!-- Items -->

                            <td class="px-6 py-4">
                                <span class="text-sm text-body">
                                    {{ order.items.length }}
                                    {{
                                        order.items.length === 1
                                            ? t("product")
                                            : t("products")
                                    }}
                                </span>
                            </td>

                            <!-- Total -->

                            <td class="whitespace-nowrap px-6 py-4">
                                <p class="font-semibold text-heading">
                                    {{ Number(order.total_amount).toFixed(2) }}
                                </p>
                            </td>

                            <!-- Status -->

                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex rounded-full border px-3 py-1 text-xs font-bold capitalize"
                                    :class="statusClasses(order.status)"
                                >
                                    {{ t(order.status) }}
                                </span>
                            </td>

                            <!-- Date -->

                            <td
                                class="whitespace-nowrap px-6 py-4 text-sm text-muted"
                            >
                                {{ formatDate(order.created_at) }}
                            </td>

                            <!-- Action -->

                            <td class="px-6 py-4 text-right">
                                <Link
                                    :href="`/admin/orders/${order.id}`"
                                    class="inline-flex items-center gap-2 rounded-xl border border-border px-3 py-2 text-sm font-semibold text-body transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700 focus-visible:outline-none"
                                >
                                    <Eye class="h-4 w-4" />
                                    {{ t("viewOrder") }}
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- ==================== Mobile Orders ==================== -->

            <div
                v-if="props.orders.length > 0"
                class="divide-y divide-border md:hidden"
            >
                <div
                    v-for="order in props.orders"
                    :key="order.id"
                    class="p-5 transition-colors hover:bg-background"
                >
                    <!-- Order Header -->

                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="font-semibold text-heading">
                                Order #{{ order.id }}
                            </p>

                            <p class="mt-1 truncate text-sm text-muted">
                                {{ order.customer.first_name }}
                                {{ order.customer.last_name }}
                            </p>
                        </div>

                        <span
                            class="shrink-0 rounded-full border px-3 py-1 text-xs font-bold capitalize"
                            :class="statusClasses(order.status)"
                        >
                            {{ t(order.status) }}
                        </span>
                    </div>

                    <!-- Order Information -->

                    <div
                        class="mt-5 grid grid-cols-2 gap-4 rounded-xl bg-background p-4"
                    >
                        <div>
                            <p
                                class="text-[11px] font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("products") }}
                            </p>

                            <p class="mt-1 text-sm font-semibold text-heading">
                                {{ order.items.length }}
                            </p>
                        </div>

                        <div>
                            <p
                                class="text-[11px] font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("total") }}
                            </p>

                            <p class="mt-1 text-sm font-semibold text-heading">
                                {{ Number(order.total_amount).toFixed(2) }}
                            </p>
                        </div>

                        <div>
                            <p
                                class="text-[11px] font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("date") }}
                            </p>

                            <p class="mt-1 text-sm font-medium text-heading">
                                {{ formatDate(order.created_at) }}
                            </p>
                        </div>
                    </div>

                    <!-- View Order -->

                    <Link
                        :href="`/admin/orders/${order.id}`"
                        class="mt-4 flex w-full items-center justify-center gap-2 rounded-xl border border-border px-4 py-3 text-sm font-semibold text-body transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700 focus-visible:outline-none"
                    >
                        <Eye class="h-4 w-4" />
                        {{ t("viewOrder") }}
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
