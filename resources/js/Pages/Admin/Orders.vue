```vue
<script setup lang="ts">
// ==================== Imports ====================

import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { Eye } from "lucide-vue-next";

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
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                Orders
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Manage and monitor all customer orders.
            </p>
        </div>

        <!-- ==================== Orders Table ==================== -->

        <div
            class="overflow-hidden rounded-2xl border border-gray-200 bg-white"
        >
            <!-- ==================== Empty State ==================== -->

            <div v-if="props.orders.length === 0" class="p-10 text-center">
                <p class="text-sm text-gray-500">
                    No orders have been placed yet.
                </p>
            </div>

            <!-- ==================== Desktop Table ==================== -->

            <div v-else class="hidden overflow-x-auto md:block">
                <table class="w-full text-left">
                    <thead class="border-b border-gray-100 bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Order
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Customer
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Items
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Total
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Status
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Date
                            </th>

                            <th
                                class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Action
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        <tr
                            v-for="order in props.orders"
                            :key="order.id"
                            class="transition hover:bg-gray-50"
                        >
                            <!-- ==================== Order ==================== -->

                            <td class="whitespace-nowrap px-6 py-4">
                                <p class="font-semibold text-gray-900">
                                    #{{ order.id }}
                                </p>
                            </td>

                            <!-- ==================== Customer ==================== -->

                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">
                                    {{ order.customer.first_name }}
                                    {{ order.customer.last_name }}
                                </p>
                            </td>

                            <!-- ==================== Items ==================== -->

                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-700">
                                    {{ order.items.length }}
                                    {{
                                        order.items.length === 1
                                            ? "item"
                                            : "items"
                                    }}
                                </p>
                            </td>

                            <!-- ==================== Total ==================== -->

                            <td class="whitespace-nowrap px-6 py-4">
                                <p class="font-semibold text-gray-900">
                                    {{ Number(order.total_amount).toFixed(2) }}
                                </p>
                            </td>

                            <!-- ==================== Status ==================== -->

                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex rounded-full px-3 py-1 text-xs font-semibold capitalize"
                                    :class="statusClasses(order.status)"
                                >
                                    {{ formatStatus(order.status) }}
                                </span>
                            </td>

                            <!-- ==================== Date ==================== -->

                            <td
                                class="whitespace-nowrap px-6 py-4 text-sm text-gray-500"
                            >
                                {{ formatDate(order.created_at) }}
                            </td>

                            <!-- ==================== Action ==================== -->

                            <td class="px-6 py-4 text-right">
                                <Link
                                    :href="`/admin/orders/${order.id}`"
                                    class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100 hover:text-gray-900"
                                >
                                    <Eye class="h-4 w-4" />
                                    View
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- ==================== Mobile Orders ==================== -->

            <div
                v-if="props.orders.length > 0"
                class="divide-y divide-gray-100 md:hidden"
            >
                <div v-for="order in props.orders" :key="order.id" class="p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="font-semibold text-gray-900">
                                Order #{{ order.id }}
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                {{ order.customer.first_name }}
                                {{ order.customer.last_name }}
                            </p>
                        </div>

                        <span
                            class="inline-flex rounded-full px-3 py-1 text-xs font-semibold capitalize"
                            :class="statusClasses(order.status)"
                        >
                            {{ formatStatus(order.status) }}
                        </span>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-400">Items</p>

                            <p class="mt-1 text-sm font-medium text-gray-900">
                                {{ order.items.length }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-400">Total</p>

                            <p class="mt-1 text-sm font-semibold text-gray-900">
                                {{ Number(order.total_amount).toFixed(2) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-400">Date</p>

                            <p class="mt-1 text-sm font-medium text-gray-900">
                                {{ formatDate(order.created_at) }}
                            </p>
                        </div>
                    </div>

                    <Link
                        :href="`/admin/orders/${order.id}`"
                        class="mt-4 flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                    >
                        <Eye class="h-4 w-4" />
                        View Order
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
```
