<script setup lang="ts">
// ==================== Imports ====================

import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head } from "@inertiajs/vue3";
import { t } from "@/Utils/i18n";

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
        <div class="space-y-6">
            <!-- ==================== Page Header ==================== -->

            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
            >
                <div>
                    <p
                        class="text-xs font-bold uppercase tracking-[0.16em] text-brand-600"
                    >
                        {{ t("adminPanel") }}
                    </p>

                    <h1
                        class="mt-1 text-2xl font-bold tracking-tight text-heading"
                    >
                        {{ t("Delivery") }}
                    </h1>

                    <p class="mt-1 max-w-2xl text-sm text-muted">
                        {{ t("manageDeliveryDescription") }}
                    </p>
                </div>

                <div
                    class="inline-flex w-fit items-center rounded-full border border-border bg-surface px-4 py-2 text-sm font-semibold text-body shadow-sm"
                >
                    {{ props.orders.length }} orders
                </div>
            </div>

            <!-- ==================== Summary ==================== -->

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm"
                >
                    <p
                        class="text-xs font-bold uppercase tracking-wide text-muted"
                    >
                        {{ t("deliveryOrders") }}
                    </p>

                    <p class="mt-1 text-2xl font-bold text-heading">
                        {{ props.orders.length }}
                    </p>
                </div>

                <div
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm"
                >
                    <p
                        class="text-xs font-bold uppercase tracking-wide text-muted"
                    >
                        {{ t("assigned") }}
                    </p>

                    <p class="mt-1 text-2xl font-bold text-heading">
                        {{
                            props.orders.filter(
                                (order) => order.delivery_assignment,
                            ).length
                        }}
                    </p>
                </div>

                <div
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm"
                >
                    <p
                        class="text-xs font-bold uppercase tracking-wide text-muted"
                    >
                        {{ t("locked") }}
                    </p>

                    <p class="mt-1 text-2xl font-bold text-heading">
                        {{
                            props.orders.filter(
                                (order) => order.delivery_locked,
                            ).length
                        }}
                    </p>
                </div>
            </div>

            <!-- ==================== Empty State ==================== -->

            <div
                v-if="props.orders.length === 0"
                class="rounded-2xl border border-border bg-surface px-6 py-14 text-center shadow-sm"
            >
                <div
                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"
                >
                    <Package class="h-6 w-6" />
                </div>

                <h2 class="mt-4 text-base font-semibold text-heading">
                    {{ t("noDeliveryOrders") }}
                </h2>

                <p class="mt-1 text-sm text-muted">
                    {{ t("noOrdersInDeliveryFlow") }}
                </p>
            </div>

            <!-- ==================== Desktop Table ==================== -->

            <div
                v-else
                class="hidden overflow-hidden rounded-2xl border border-border bg-surface shadow-sm md:block"
            >
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1050px] text-left">
                        <thead class="border-b border-border bg-background">
                            <tr>
                                <th
                                    class="px-5 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    {{ t("order") }}
                                </th>

                                <th
                                    class="px-5 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    {{ t("customer") }}
                                </th>

                                <th
                                    class="px-5 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    {{ t("delivery") }}
                                </th>

                                <th
                                    class="px-5 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    {{ t("type") }}
                                </th>

                                <th
                                    class="px-5 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    {{ t("status") }}
                                </th>

                                <th
                                    class="px-5 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    {{ t("lock") }}
                                </th>

                                <th
                                    class="px-5 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    {{ t("taken") }}
                                </th>

                                <th
                                    class="px-5 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    {{ t("date") }}
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-border">
                            <tr
                                v-for="order in props.orders"
                                :key="order.id"
                                class="transition-colors hover:bg-background"
                            >
                                <!-- ==================== Order ==================== -->

                                <td class="px-5 py-4">
                                    <p class="font-semibold text-heading">
                                        #{{ order.id }}
                                    </p>

                                    <p class="mt-0.5 text-xs text-muted">
                                        {{ t("customer") }} #{{
                                            order.customer_id
                                        }}
                                    </p>
                                </td>

                                <!-- ==================== Customer ==================== -->

                                <td class="px-5 py-4">
                                    <p
                                        class="text-sm font-semibold text-heading"
                                    >
                                        {{ order.customer.first_name }}
                                        {{ order.customer.last_name }}
                                    </p>
                                </td>

                                <!-- ==================== Delivery User ==================== -->

                                <td class="px-5 py-4">
                                    <template
                                        v-if="
                                            order.delivery_assignment
                                                ?.delivery_user
                                        "
                                    >
                                        <p
                                            class="text-sm font-medium text-body"
                                        >
                                            {{
                                                order.delivery_assignment
                                                    .delivery_user.first_name
                                            }}
                                            {{
                                                order.delivery_assignment
                                                    .delivery_user.last_name
                                            }}
                                        </p>

                                        <p
                                            v-if="
                                                order.delivery_assignment
                                                    .assigned_by_user
                                            "
                                            class="mt-0.5 text-xs text-muted"
                                        >
                                            {{ t("assignedBy") }}
                                            {{
                                                order.delivery_assignment
                                                    .assigned_by_user.first_name
                                            }}
                                            {{
                                                order.delivery_assignment
                                                    .assigned_by_user.last_name
                                            }}
                                        </p>
                                    </template>

                                    <span v-else class="text-sm text-muted">
                                        {{ t("unassigned") }}
                                    </span>
                                </td>

                                <!-- ==================== Type ==================== -->

                                <td class="px-5 py-4">
                                    <span
                                        v-if="order.delivery_assignment"
                                        class="inline-flex rounded-lg bg-background px-2.5 py-1 text-xs font-semibold capitalize text-body"
                                    >
                                        {{ t(order.delivery_assignment.type) }}
                                    </span>

                                    <span v-else class="text-sm text-muted">
                                        —
                                    </span>
                                </td>

                                <!-- ==================== Status ==================== -->

                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-bold capitalize"
                                        :class="statusClasses(order.status)"
                                    >
                                        {{ t(order.status) }}
                                    </span>
                                </td>

                                <!-- ==================== Lock ==================== -->

                                <td class="px-5 py-4">
                                    <span
                                        v-if="order.delivery_locked"
                                        class="inline-flex rounded-full border border-amber-100 bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700"
                                    >
                                        {{ t("locked") }}
                                    </span>

                                    <span v-else class="text-sm text-muted">
                                        {{ t("notLocked") }}
                                    </span>
                                </td>

                                <!-- ==================== Taken ==================== -->

                                <td class="px-5 py-4">
                                    <span
                                        v-if="
                                            order.delivery_assignment?.taken_at
                                        "
                                        class="inline-flex rounded-full border border-green-100 bg-green-50 px-3 py-1 text-xs font-bold text-green-700"
                                    >
                                        {{ t("taken") }}
                                    </span>

                                    <span v-else class="text-sm text-muted">
                                        {{ t("notTaken") }}
                                    </span>
                                </td>

                                <!-- ==================== Date ==================== -->

                                <td
                                    class="whitespace-nowrap px-5 py-4 text-sm text-body"
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
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm"
                >
                    <!-- ==================== Card Header ==================== -->

                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-heading">
                                {{ t("order") }} #{{ order.id }}
                            </p>

                            <p class="mt-1 text-sm text-muted">
                                {{ order.customer.first_name }}
                                {{ order.customer.last_name }}
                            </p>
                        </div>

                        <span
                            class="shrink-0 rounded-full px-3 py-1 text-xs font-bold capitalize"
                            :class="statusClasses(order.status)"
                        >
                            {{ t(order.status) }}
                        </span>
                    </div>

                    <!-- ==================== Delivery Information ==================== -->

                    <div class="mt-5 space-y-4 border-t border-border pt-4">
                        <div class="flex justify-between gap-4">
                            <span class="text-sm text-muted">
                                {{ t("delivery") }}
                            </span>

                            <span
                                v-if="order.delivery_assignment?.delivery_user"
                                class="text-right text-sm font-semibold text-heading"
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

                            <span v-else class="text-sm text-muted">
                                {{ t("unassigned") }}
                            </span>
                        </div>

                        <div
                            v-if="order.delivery_assignment?.assigned_by_user"
                            class="flex justify-between gap-4"
                        >
                            <span class="text-sm text-muted">
                                {{ t("assignedBy") }}
                            </span>

                            <span
                                class="text-right text-sm font-medium text-body"
                            >
                                {{
                                    order.delivery_assignment.assigned_by_user
                                        .first_name
                                }}
                                {{
                                    order.delivery_assignment.assigned_by_user
                                        .last_name
                                }}
                            </span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-sm text-muted">
                                {{ t("type") }}
                            </span>

                            <span
                                class="text-sm font-semibold capitalize text-heading"
                            >
                                {{ order.delivery_assignment?.type ?? "—" }}
                            </span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-sm text-muted">
                                {{ t("lock") }}
                            </span>

                            <span
                                class="text-sm font-semibold"
                                :class="
                                    order.delivery_locked
                                        ? 'text-amber-600'
                                        : 'text-muted'
                                "
                            >
                                {{
                                    order.delivery_locked
                                        ? t("tocked")
                                        : t("notLocked")
                                }}
                            </span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-sm text-muted">
                                {{ t("taken") }}
                            </span>

                            <span
                                class="text-sm font-semibold"
                                :class="
                                    order.delivery_assignment?.taken_at
                                        ? 'text-green-600'
                                        : 'text-muted'
                                "
                            >
                                {{
                                    order.delivery_assignment?.taken_at
                                        ? t("taken")
                                        : t("notTaken")
                                }}
                            </span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-sm text-muted">
                                {{ t("date") }}
                            </span>

                            <span
                                class="text-right text-sm font-medium text-body"
                            >
                                {{ formatDate(order.created_at) }}
                            </span>
                        </div>
                    </div>

                    <!-- ==================== Admin Note ==================== -->

                    <div
                        v-if="order.delivery_assignment?.admin_note"
                        class="mt-4 rounded-xl border border-brand-100 bg-brand-50 p-3"
                    >
                        <p
                            class="text-[11px] font-bold uppercase tracking-wide text-brand-700"
                        >
                            {{ t("adminNote") }}
                        </p>

                        <p class="mt-1 text-sm leading-5 text-brand-700">
                            {{ order.delivery_assignment.admin_note }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
