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
import { t } from "@/Utils/i18n";

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
        return "customer";
    }

    return props.user.roles.map((role) => t(role.name)).join(", ");
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
        <!-- ==================== Back Navigation ==================== -->

        <Link
            href="/admin/users"
            class="mb-5 inline-flex items-center gap-2 text-sm font-semibold text-muted transition hover:text-brand-700"
        >
            <ArrowLeft class="h-4 w-4" />
            {{ t("backToUsers") }}
        </Link>

        <!-- ==================== Page Header ==================== -->

        <div
            class="mb-6 rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
        >
            <div
                class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between"
            >
                <div class="flex min-w-0 items-center gap-4">
                    <img
                        v-if="props.user.profile_image"
                        :src="props.user.profile_image"
                        :alt="fullName()"
                        class="h-16 w-16 shrink-0 rounded-full object-cover ring-4 ring-brand-50"
                    />

                    <div
                        v-else
                        class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600"
                    >
                        <UserRound class="h-7 w-7" />
                    </div>

                    <div class="min-w-0">
                        <p
                            class="mb-1 text-xs font-bold uppercase tracking-[0.16em] text-brand-600"
                        >
                            {{ t("userProfile") }}
                        </p>

                        <h1
                            class="truncate text-2xl font-bold tracking-tight text-heading"
                        >
                            {{ fullName() }}
                        </h1>

                        <p class="mt-1 text-sm text-muted">
                            {{ t("user") }} #{{ props.user.id }}
                        </p>
                    </div>
                </div>

                <!-- ==================== User Status ==================== -->

                <div class="flex flex-wrap gap-2">
                    <span
                        v-for="role in props.user.roles"
                        :key="role.id"
                        class="rounded-full border border-brand-100 bg-brand-50 px-3 py-1 text-xs font-bold capitalize text-brand-700"
                    >
                        {{ t(role.name) }}
                    </span>

                    <span
                        v-if="props.user.roles.length === 0"
                        class="rounded-full border border-brand-100 bg-brand-50 px-3 py-1 text-xs font-bold text-brand-700"
                    >
                        {{ t("customer") }}
                    </span>

                    <span
                        v-if="props.user.email_verified_at"
                        class="rounded-full border border-green-100 bg-green-50 px-3 py-1 text-xs font-bold text-green-700"
                    >
                        {{ t("verified") }}
                    </span>

                    <span
                        v-else
                        class="rounded-full border border-yellow-100 bg-yellow-50 px-3 py-1 text-xs font-bold text-yellow-700"
                    >
                        {{ t("unverified") }}
                    </span>
                </div>
            </div>
        </div>

        <!-- ==================== Statistics ==================== -->

        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <!-- ==================== Orders ==================== -->

            <div
                class="rounded-2xl border border-border bg-surface p-5 shadow-sm"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                    >
                        <ShoppingBag class="h-5 w-5" />
                    </div>

                    <div>
                        <p
                            class="text-xs font-bold uppercase tracking-wide text-muted"
                        >
                            {{ t("orders") }}
                        </p>

                        <p class="mt-0.5 text-xl font-bold text-heading">
                            {{ props.user.orders.length }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- ==================== Deliveries ==================== -->

            <div
                class="rounded-2xl border border-border bg-surface p-5 shadow-sm"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                    >
                        <Truck class="h-5 w-5" />
                    </div>

                    <div>
                        <p
                            class="text-xs font-bold uppercase tracking-wide text-muted"
                        >
                            {{ t("deliveries") }}
                        </p>

                        <p class="mt-0.5 text-xl font-bold text-heading">
                            {{ props.user.delivery_assignments.length }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- ==================== Order Value ==================== -->

            <div
                class="rounded-2xl border border-border bg-surface p-5 shadow-sm"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                    >
                        <ShoppingBag class="h-5 w-5" />
                    </div>

                    <div class="min-w-0">
                        <p
                            class="text-xs font-bold uppercase tracking-wide text-muted"
                        >
                            {{ t("ordersValue") }}
                        </p>

                        <p
                            class="mt-0.5 truncate text-xl font-bold text-heading"
                        >
                            {{ totalOrderValue().toFixed(2) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== Main Grid ==================== -->

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- ==================== User Information ==================== -->

            <section
                class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
            >
                <p
                    class="text-xs font-bold uppercase tracking-[0.16em] text-brand-600"
                >
                    {{ t("account") }}
                </p>

                <h2 class="mt-1 text-lg font-semibold text-heading">
                    {{ t("userInfo") }}
                </h2>

                <div class="mt-6 space-y-5">
                    <!-- ==================== Email ==================== -->

                    <div class="flex items-start gap-3">
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-background text-muted"
                        >
                            <Mail class="h-4 w-4" />
                        </div>

                        <div class="min-w-0">
                            <p
                                class="text-[11px] font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("email") }}
                            </p>

                            <p
                                class="mt-1 break-all text-sm font-semibold text-heading"
                            >
                                {{ props.user.email }}
                            </p>
                        </div>
                    </div>

                    <!-- ==================== Phone ==================== -->

                    <div v-if="props.user.phone" class="flex items-start gap-3">
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-background text-muted"
                        >
                            <Phone class="h-4 w-4" />
                        </div>

                        <div>
                            <p
                                class="text-[11px] font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("phone") }}
                            </p>

                            <p class="mt-1 text-sm font-semibold text-heading">
                                {{ props.user.phone }}
                            </p>
                        </div>
                    </div>

                    <!-- ==================== Roles ==================== -->

                    <div class="flex items-start gap-3">
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-background text-muted"
                        >
                            <UserRound class="h-4 w-4" />
                        </div>

                        <div class="min-w-0">
                            <p
                                class="text-[11px] font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("role") }}
                            </p>

                            <p
                                class="mt-1 text-sm font-semibold capitalize text-heading"
                            >
                                {{ roleNames() }}
                            </p>
                        </div>
                    </div>

                    <!-- ==================== Joined ==================== -->

                    <div class="flex items-start gap-3">
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-background text-muted"
                        >
                            <CalendarDays class="h-4 w-4" />
                        </div>

                        <div>
                            <p
                                class="text-[11px] font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("joinDate") }}
                            </p>

                            <p class="mt-1 text-sm font-semibold text-heading">
                                {{ formatDate(props.user.created_at) }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ==================== Orders ==================== -->

            <section
                class="rounded-2xl border border-border bg-surface shadow-sm lg:col-span-2"
            >
                <div
                    class="flex items-center justify-between gap-4 border-b border-border px-5 py-4 sm:px-6"
                >
                    <div>
                        <p
                            class="text-xs font-bold uppercase tracking-[0.16em] text-brand-600"
                        >
                            {{ t("activity") }}
                        </p>

                        <h2 class="mt-1 text-lg font-semibold text-heading">
                            {{ t("orders") }}
                        </h2>
                    </div>

                    <span
                        class="rounded-full bg-background px-3 py-1 text-xs font-bold text-muted"
                    >
                        {{ props.user.orders.length }} {{ t("total") }}
                    </span>
                </div>

                <div
                    v-if="props.user.orders.length"
                    class="divide-y divide-border"
                >
                    <div
                        v-for="order in props.user.orders.slice(0, 8)"
                        :key="order.id"
                        class="flex flex-col gap-4 p-5 transition-colors hover:bg-background sm:flex-row sm:items-center sm:justify-between sm:px-6"
                    >
                        <div class="min-w-0">
                            <p class="font-semibold text-heading">
                                {{ t("order") }} #{{ order.id }}
                            </p>

                            <p class="mt-1 text-xs text-muted">
                                {{ formatDateTime(order.created_at) }}
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <span
                                class="rounded-full border border-border bg-background px-3 py-1 text-xs font-bold capitalize text-body"
                            >
                                {{ t(order.status) }}
                            </span>

                            <span class="text-sm font-bold text-heading">
                                {{ Number(order.total_amount).toFixed(2) }}
                            </span>

                            <Link
                                :href="`/admin/orders/${order.id}`"
                                class="inline-flex items-center rounded-xl border border-border px-3 py-2 text-sm font-semibold text-body transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700 focus-visible:outline-none"
                            >
                                {{ t("view") }}
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- ==================== Empty Orders ==================== -->

                <div v-else class="px-6 py-14 text-center">
                    <div
                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"
                    >
                        <ShoppingBag class="h-6 w-6" />
                    </div>

                    <h3 class="mt-4 text-base font-semibold text-heading">
                        {{ t("noOrders") }}
                    </h3>

                    <p class="mt-1 text-sm text-muted">
                        {{ t("userNoOrders") }}
                    </p>
                </div>
            </section>
        </div>

        <!-- ==================== Delivery Assignments ==================== -->

        <section
            v-if="props.user.delivery_assignments.length"
            class="mt-6 rounded-2xl border border-border bg-surface shadow-sm"
        >
            <div
                class="flex items-center justify-between gap-4 border-b border-border px-5 py-4 sm:px-6"
            >
                <div>
                    <p
                        class="text-xs font-bold uppercase tracking-[0.16em] text-brand-600"
                    >
                        {{ t("delivery") }}
                    </p>

                    <h2 class="mt-1 text-lg font-semibold text-heading">
                        {{ t("deliveryAssignments") }}
                    </h2>
                </div>

                <span
                    class="rounded-full bg-background px-3 py-1 text-xs font-bold text-muted"
                >
                    {{ props.user.delivery_assignments.length }}
                    {{ t("total") }}
                </span>
            </div>

            <div class="divide-y divide-border">
                <div
                    v-for="assignment in props.user.delivery_assignments.slice(
                        0,
                        8,
                    )"
                    :key="assignment.id"
                    class="flex flex-col gap-4 p-5 transition-colors hover:bg-background sm:flex-row sm:items-center sm:justify-between sm:px-6"
                >
                    <div>
                        <p class="font-semibold text-heading">
                            {{ t("order") }} #{{ assignment.order_id }}
                        </p>

                        <p class="mt-1 text-xs capitalize text-muted">
                            {{ t(assignment.type) }} {{ t("assignment") }}
                        </p>

                        <p
                            v-if="assignment.created_at"
                            class="mt-1 text-xs text-muted"
                        >
                            {{ t("assignedDate") }}
                            {{ formatDateTime(assignment.created_at) }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <span
                            v-if="assignment.completed_at"
                            class="rounded-full border border-green-100 bg-green-50 px-3 py-1 text-xs font-bold text-green-700"
                        >
                            {{ t("completed") }}
                        </span>

                        <span
                            v-else-if="assignment.taken_at"
                            class="rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700"
                        >
                            {{ t("taken") }}
                        </span>

                        <span
                            v-else
                            class="rounded-full border border-yellow-100 bg-yellow-50 px-3 py-1 text-xs font-bold text-yellow-700"
                        >
                            {{ t("notTaken") }}
                        </span>

                        <Link
                            :href="`/admin/orders/${assignment.order_id}`"
                            class="inline-flex items-center rounded-xl border border-border px-3 py-2 text-sm font-semibold text-body transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700 focus-visible:outline-none"
                        >
                            {{ t("viewOrder") }}
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </AdminLayout>
</template>
