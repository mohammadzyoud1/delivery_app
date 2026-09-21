<script setup lang="ts">
// ==================== Imports ====================

import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head } from "@inertiajs/vue3";

// ==================== Types ====================

interface Customer {
    id: number;
    first_name: string;
    last_name: string;
}

interface DeliveryUser {
    id: number;
    first_name: string;
    last_name: string;
}

interface AssignedBy {
    id: number;
    first_name: string;
    last_name: string;
}

interface DeliveryAssignment {
    id: number;
    delivery_user_id: number;
    assigned_by: number | null;
    type: string;
    admin_note: string | null;
    taken_at: string | null;
    completed_at: string | null;
    delivery_user?: DeliveryUser;
    assigned_by_user?: AssignedBy;
}

interface Order {
    id: number;
    customer_id: number;
    status: string;
    delivery_locked: boolean;
    created_at: string;
    customer: Customer;
    delivery_assignment?: DeliveryAssignment | null;
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
    return new Date(date).toLocaleString();
};

const statusClasses = (status: string): string => {
    switch (status) {
        case "accepted":
            return "bg-blue-100 text-blue-700";

        case "preparing":
            return "bg-yellow-100 text-yellow-700";

        case "on_the_way":
            return "bg-purple-100 text-purple-700";

        case "at_the_door":
            return "bg-orange-100 text-orange-700";

        case "delivered":
            return "bg-green-100 text-green-700";

        default:
            return "bg-gray-100 text-gray-700";
    }
};
</script>

<template>
    <Head title="Admin Delivery" />

    <AdminLayout>
        <!-- ==================== Page Header ==================== -->

        <div class="mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                Delivery
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Manage delivery assignments and track delivery progress.
            </p>
        </div>

        <!-- ==================== Empty State ==================== -->

        <div
            v-if="props.orders.length === 0"
            class="rounded-2xl border border-gray-200 bg-white p-10 text-center"
        >
            <h2 class="font-semibold text-gray-900">No delivery orders</h2>

            <p class="mt-1 text-sm text-gray-500">
                There are currently no orders in the delivery flow.
            </p>
        </div>

        <!-- ==================== Desktop Table ==================== -->

        <div
            v-else
            class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white md:block"
        >
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="border-b border-gray-200 bg-gray-50">
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
                                Delivery User
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Type
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Status
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Lock
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Taken
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Date
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        <tr
                            v-for="order in props.orders"
                            :key="order.id"
                            class="hover:bg-gray-50"
                        >
                            <td
                                class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-gray-900"
                            >
                                #{{ order.id }}
                            </td>

                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ order.customer.first_name }}
                                    {{ order.customer.last_name }}
                                </p>
                            </td>

                            <td class="px-6 py-4">
                                <span
                                    v-if="
                                        order.delivery_assignment?.delivery_user
                                    "
                                    class="text-sm text-gray-700"
                                >
                                    {{
                                        order.delivery_assignment.delivery_user
                                            .first_name
                                    }}
                                    {{
                                        order.delivery_assignment.delivery_user
                                            .last_name
                                    }}
                                </span>

                                <span v-else class="text-sm text-gray-400">
                                    Unassigned
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <span
                                    v-if="order.delivery_assignment"
                                    class="text-sm font-medium capitalize text-gray-700"
                                >
                                    {{ order.delivery_assignment.type }}
                                </span>

                                <span v-else class="text-sm text-gray-400">
                                    —
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex rounded-full px-3 py-1 text-xs font-semibold capitalize"
                                    :class="statusClasses(order.status)"
                                >
                                    {{ formatStatus(order.status) }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <span
                                    v-if="order.delivery_locked"
                                    class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700"
                                >
                                    Locked
                                </span>

                                <span v-else class="text-sm text-gray-400">
                                    No
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <span
                                    v-if="order.delivery_assignment?.taken_at"
                                    class="text-sm text-gray-700"
                                >
                                    Yes
                                </span>

                                <span v-else class="text-sm text-gray-400">
                                    No
                                </span>
                            </td>

                            <td
                                class="whitespace-nowrap px-6 py-4 text-sm text-gray-500"
                            >
                                {{ formatDate(order.created_at) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ==================== Mobile Cards ==================== -->

        <div v-if="props.orders.length > 0" class="space-y-4 md:hidden">
            <div
                v-for="order in props.orders"
                :key="order.id"
                class="rounded-2xl border border-gray-200 bg-white p-5"
            >
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

                <div class="mt-5 space-y-3 border-t border-gray-100 pt-4">
                    <div class="flex justify-between gap-4">
                        <span class="text-sm text-gray-500">
                            Delivery User
                        </span>

                        <span
                            v-if="order.delivery_assignment?.delivery_user"
                            class="text-right text-sm font-medium text-gray-900"
                        >
                            {{
                                order.delivery_assignment.delivery_user
                                    .first_name
                            }}
                            {{
                                order.delivery_assignment.delivery_user
                                    .last_name
                            }}
                        </span>

                        <span v-else class="text-sm text-gray-400">
                            Unassigned
                        </span>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span class="text-sm text-gray-500"> Type </span>

                        <span
                            class="text-sm font-medium capitalize text-gray-900"
                        >
                            {{ order.delivery_assignment?.type ?? "—" }}
                        </span>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span class="text-sm text-gray-500"> Lock </span>

                        <span
                            class="text-sm font-medium"
                            :class="
                                order.delivery_locked
                                    ? 'text-amber-600'
                                    : 'text-gray-400'
                            "
                        >
                            {{
                                order.delivery_locked ? "Locked" : "Not locked"
                            }}
                        </span>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span class="text-sm text-gray-500"> Taken </span>

                        <span class="text-sm font-medium text-gray-900">
                            {{
                                order.delivery_assignment?.taken_at
                                    ? "Yes"
                                    : "No"
                            }}
                        </span>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span class="text-sm text-gray-500"> Date </span>

                        <span class="text-right text-sm text-gray-700">
                            {{ formatDate(order.created_at) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
