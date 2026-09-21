<script setup lang="ts">
// ==================== Imports ====================

import { Head, Link } from "@inertiajs/vue3";
import {
    ArrowLeft,
    CalendarDays,
    Mail,
    Phone,
    ShoppingBag,
    Truck,
    UserRound,
} from "lucide-vue-next";
import AdminLayout from "@/Layouts/AdminLayout.vue";

// ==================== Interfaces ====================

interface Role {
    id: number;
    name: string;
}

interface Order {
    id: number;
    status: string;
    total_amount: string | number;
    created_at: string;
}

interface DeliveryAssignment {
    id: number;
    order_id: number;
    type: string;
    taken_at: string | null;
    completed_at: string | null;
    created_at: string;
}

interface User {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone: string | null;
    profile_image: string | null;
    email_verified_at: string | null;
    created_at: string;
    roles: Role[];
    orders: Order[];
    delivery_assignments: DeliveryAssignment[];
}

// ==================== Props ====================

const props = defineProps<{
    user: User;
}>();

// ==================== Helpers ====================

const fullName = (): string => {
    return `${props.user.first_name} ${props.user.last_name}`.trim();
};

const formatDate = (date: string): string => {
    return new Date(date).toLocaleDateString();
};

const formatDateTime = (date: string): string => {
    return new Date(date).toLocaleString();
};

const formatStatus = (status: string): string => {
    return status.replace(/_/g, " ");
};

const roleNames = (): string => {
    if (props.user.roles.length === 0) {
        return "Customer";
    }

    return props.user.roles.map((role) => role.name).join(", ");
};

const totalOrderValue = (): number => {
    return props.user.orders.reduce(
        (total, order) => total + Number(order.total_amount),
        0,
    );
};
</script>

<template>
    <Head :title="`User - ${fullName()}`" />

    <AdminLayout>
        <!-- ==================== Back ==================== -->

        <Link
            href="/admin/users"
            class="mb-6 inline-flex items-center gap-2 text-sm font-medium text-gray-600 transition hover:text-gray-900"
        >
            <ArrowLeft class="h-4 w-4" />
            Back to Users
        </Link>

        <!-- ==================== Page Header ==================== -->

        <div
            class="mb-6 flex flex-col gap-5 rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 lg:flex-row lg:items-center lg:justify-between"
        >
            <div class="flex items-center gap-4">
                <img
                    v-if="props.user.profile_image"
                    :src="props.user.profile_image"
                    :alt="fullName()"
                    class="h-16 w-16 rounded-full object-cover"
                />

                <div
                    v-else
                    class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-gray-100"
                >
                    <UserRound class="h-7 w-7 text-gray-500" />
                </div>

                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ fullName() }}
                    </h1>

                    <p class="mt-1 text-sm text-gray-500">
                        User #{{ props.user.id }}
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <span
                    v-for="role in props.user.roles"
                    :key="role.id"
                    class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium capitalize text-gray-700"
                >
                    {{ role.name }}
                </span>

                <span
                    v-if="props.user.roles.length === 0"
                    class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700"
                >
                    Customer
                </span>

                <span
                    v-if="props.user.email_verified_at"
                    class="rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700"
                >
                    Email Verified
                </span>

                <span
                    v-else
                    class="rounded-full bg-yellow-50 px-3 py-1 text-xs font-medium text-yellow-700"
                >
                    Email Unverified
                </span>
            </div>
        </div>

        <!-- ==================== Statistics ==================== -->

        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-gray-200 bg-white p-5">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100"
                    >
                        <ShoppingBag class="h-5 w-5 text-gray-600" />
                    </div>

                    <div>
                        <p
                            class="text-xs font-medium uppercase tracking-wide text-gray-400"
                        >
                            Orders
                        </p>

                        <p class="text-xl font-bold text-gray-900">
                            {{ props.user.orders.length }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100"
                    >
                        <Truck class="h-5 w-5 text-gray-600" />
                    </div>

                    <div>
                        <p
                            class="text-xs font-medium uppercase tracking-wide text-gray-400"
                        >
                            Deliveries
                        </p>

                        <p class="text-xl font-bold text-gray-900">
                            {{ props.user.delivery_assignments.length }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100"
                    >
                        <ShoppingBag class="h-5 w-5 text-gray-600" />
                    </div>

                    <div>
                        <p
                            class="text-xs font-medium uppercase tracking-wide text-gray-400"
                        >
                            Order Value
                        </p>

                        <p class="text-xl font-bold text-gray-900">
                            {{ totalOrderValue().toFixed(2) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== Main Grid ==================== -->

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- ==================== User Information ==================== -->

            <div class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6">
                <h2 class="text-base font-semibold text-gray-900">
                    User Information
                </h2>

                <div class="mt-5 space-y-4">
                    <div class="flex items-start gap-3">
                        <Mail class="mt-0.5 h-5 w-5 text-gray-400" />

                        <div class="min-w-0">
                            <p class="text-xs text-gray-400">Email</p>

                            <p
                                class="break-all text-sm font-medium text-gray-900"
                            >
                                {{ props.user.email }}
                            </p>
                        </div>
                    </div>

                    <div v-if="props.user.phone" class="flex items-start gap-3">
                        <Phone class="mt-0.5 h-5 w-5 text-gray-400" />

                        <div>
                            <p class="text-xs text-gray-400">Phone</p>

                            <p class="text-sm font-medium text-gray-900">
                                {{ props.user.phone }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <UserRound class="mt-0.5 h-5 w-5 text-gray-400" />

                        <div>
                            <p class="text-xs text-gray-400">Roles</p>

                            <p
                                class="text-sm font-medium capitalize text-gray-900"
                            >
                                {{ roleNames() }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <CalendarDays class="mt-0.5 h-5 w-5 text-gray-400" />

                        <div>
                            <p class="text-xs text-gray-400">Joined</p>

                            <p class="text-sm font-medium text-gray-900">
                                {{ formatDate(props.user.created_at) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==================== Recent Orders ==================== -->

            <div
                class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 lg:col-span-2"
            >
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900">
                        Orders
                    </h2>

                    <span class="text-xs text-gray-400">
                        {{ props.user.orders.length }} total
                    </span>
                </div>

                <div
                    v-if="props.user.orders.length"
                    class="mt-4 divide-y divide-gray-100"
                >
                    <div
                        v-for="order in props.user.orders.slice(0, 8)"
                        :key="order.id"
                        class="flex flex-col gap-3 py-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <p class="text-sm font-semibold text-gray-900">
                                Order #{{ order.id }}
                            </p>

                            <p class="mt-1 text-xs text-gray-400">
                                {{ formatDateTime(order.created_at) }}
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            <span
                                class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium capitalize text-gray-700"
                            >
                                {{ formatStatus(order.status) }}
                            </span>

                            <span class="text-sm font-semibold text-gray-900">
                                {{ Number(order.total_amount).toFixed(2) }}
                            </span>

                            <Link
                                :href="`/admin/orders/${order.id}`"
                                class="text-sm font-semibold text-gray-900 hover:underline"
                            >
                                View
                            </Link>
                        </div>
                    </div>
                </div>

                <div v-else class="py-10 text-center">
                    <ShoppingBag class="mx-auto h-9 w-9 text-gray-300" />

                    <p class="mt-3 text-sm font-medium text-gray-900">
                        No orders yet.
                    </p>
                </div>
            </div>
        </div>

        <!-- ==================== Delivery Assignments ==================== -->

        <div
            v-if="props.user.delivery_assignments.length"
            class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 sm:p-6"
        >
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-900">
                    Delivery Assignments
                </h2>

                <span class="text-xs text-gray-400">
                    {{ props.user.delivery_assignments.length }} total
                </span>
            </div>

            <div class="mt-4 divide-y divide-gray-100">
                <div
                    v-for="assignment in props.user.delivery_assignments.slice(
                        0,
                        8,
                    )"
                    :key="assignment.id"
                    class="flex flex-col gap-3 py-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <p class="text-sm font-semibold text-gray-900">
                            Order #{{ assignment.order_id }}
                        </p>

                        <p class="mt-1 text-xs capitalize text-gray-400">
                            {{ assignment.type }} assignment
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <span
                            v-if="assignment.completed_at"
                            class="rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700"
                        >
                            Completed
                        </span>

                        <span
                            v-else-if="assignment.taken_at"
                            class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700"
                        >
                            Taken
                        </span>

                        <span
                            v-else
                            class="rounded-full bg-yellow-50 px-3 py-1 text-xs font-medium text-yellow-700"
                        >
                            Not Taken
                        </span>

                        <Link
                            :href="`/admin/orders/${assignment.order_id}`"
                            class="text-sm font-semibold text-gray-900 hover:underline"
                        >
                            View Order
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
