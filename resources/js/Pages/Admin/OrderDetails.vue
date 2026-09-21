<script setup lang="ts">
// ==================== Imports ====================

import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ArrowLeft, Truck } from "lucide-vue-next";
import { ref } from "vue";
import Button from "@/Components/UI/Button.vue";
// ==================== Types ====================

interface Customer {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone: string | null;
}

interface ProductImage {
    id: number;
    image: string;
}

interface Product {
    id: number;
    name: string;
    images: ProductImage[];
}

interface Variant {
    id: number;
    sku: string;
}

interface Seller {
    id: number;
    first_name: string;
    last_name: string;
}

interface OrderItem {
    id: number;
    quantity: number;
    unit_price: number;
    subtotal: number;
    variant_name: string | null;
    product: Product;
    variant: Variant | null;
    seller: Seller;
}

interface StatusHistory {
    id: number;
    status: string;
    note: string | null;
    created_at: string;
    changed_by: {
        id: number;
        first_name: string;
        last_name: string;
    } | null;
}

interface DeliveryUser {
    id: number;
    first_name: string;
    last_name: string;
}

interface DeliveryAssignment {
    id: number;
    type: string;
    admin_note: string | null;
    taken_at: string | null;
    completed_at: string | null;
    delivery_user?: DeliveryUser;
    assigned_by: DeliveryUser | null;
}

interface Order {
    id: number;
    status: string;
    delivery_address: string;
    custom_request: string | null;
    rejection_reason: string | null;
    total_amount: number;
    created_at: string;
    customer: Customer;
    items: OrderItem[];
    status_histories: StatusHistory[];
    delivery_assignment: DeliveryAssignment | null;
}

// ==================== Props ====================

const props = defineProps<{
    order: Order;
    deliveryUsers: DeliveryUser[];
}>();

// ==================== Reject Order ====================
const rejectionReason = ref("");
const rejecting = ref(false);
const rejectOrder = () => {
    if (!rejectionReason.value.trim()) {
        return;
    }

    rejecting.value = true;

    router.patch(
        `/admin/orders/${props.order.id}/reject`,
        {
            rejection_reason: rejectionReason.value,
        },
        {
            preserveScroll: true,
            onFinish: () => {
                rejecting.value = false;
            },
        },
    );
};

// ==================== Delivery Assignment State ====================

const selectedDeliveryUser = ref<number | null>(null);
const adminNote = ref("");
const assigningDelivery = ref(false);

// ==================== Reassignment State ====================

const reassigningDelivery = ref(false);
const reassignNote = ref("");

// ==================== Assign Delivery ====================

const assignDelivery = () => {
    if (!selectedDeliveryUser.value) {
        return;
    }

    assigningDelivery.value = true;

    router.post(
        `/admin/orders/${props.order.id}/delivery`,
        {
            delivery_user_id: selectedDeliveryUser.value,
            admin_note: adminNote.value || null,
        },
        {
            preserveScroll: true,
            onFinish: () => {
                assigningDelivery.value = false;
            },
        },
    );
};

// ==================== Reassign Delivery ====================

const reassignDelivery = () => {
    if (!selectedDeliveryUser.value) {
        return;
    }

    reassigningDelivery.value = true;

    router.patch(
        `/admin/orders/${props.order.id}/delivery`,
        {
            delivery_user_id: selectedDeliveryUser.value,
            admin_note: reassignNote.value || null,
        },
        {
            preserveScroll: true,
            onFinish: () => {
                reassigningDelivery.value = false;
            },
        },
    );
};
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
    <Head :title="`Order #${props.order.id}`" />

    <AdminLayout>
        <!-- ==================== Page Header ==================== -->

        <div class="mb-8">
            <Link
                href="/admin/orders"
                class="mb-5 inline-flex items-center gap-2 text-sm font-semibold text-muted transition hover:text-brand-700"
            >
                <ArrowLeft class="h-4 w-4" />
                Back to Orders
            </Link>

            <div
                class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end"
            >
                <div>
                    <p
                        class="mb-2 text-xs font-bold uppercase tracking-[0.18em] text-brand-600"
                    >
                        Admin Panel
                    </p>

                    <h1
                        class="text-2xl font-bold tracking-tight text-heading sm:text-3xl"
                    >
                        Order #{{ props.order.id }}
                    </h1>

                    <p class="mt-2 text-sm text-muted">
                        Placed {{ formatDate(props.order.created_at) }}
                    </p>
                </div>

                <span
                    class="inline-flex w-fit rounded-full border px-3 py-1.5 text-sm font-bold capitalize"
                    :class="statusClasses(props.order.status)"
                >
                    {{ formatStatus(props.order.status) }}
                </span>
            </div>
        </div>

        <!-- ==================== Main Grid ==================== -->

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- ==================== Left Column ==================== -->

            <div class="space-y-6 lg:col-span-2">
                <!-- ==================== Customer ==================== -->

                <section
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
                >
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p
                                class="text-xs font-bold uppercase tracking-[0.16em] text-brand-600"
                            >
                                Customer
                            </p>

                            <h2 class="mt-1 text-lg font-semibold text-heading">
                                Customer Information
                            </h2>
                        </div>
                    </div>

                    <div
                        class="mt-5 rounded-xl border border-border bg-background p-4"
                    >
                        <p class="font-semibold text-heading">
                            {{ props.order.customer.first_name }}
                            {{ props.order.customer.last_name }}
                        </p>

                        <p class="mt-1 text-sm text-muted">
                            {{ props.order.customer.email }}
                        </p>

                        <p
                            v-if="props.order.customer.phone"
                            class="mt-1 text-sm text-muted"
                        >
                            {{ props.order.customer.phone }}
                        </p>
                    </div>
                </section>

                <!-- ==================== Delivery Address ==================== -->

                <section
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
                >
                    <p
                        class="text-xs font-bold uppercase tracking-[0.16em] text-brand-600"
                    >
                        Delivery
                    </p>

                    <h2 class="mt-1 text-lg font-semibold text-heading">
                        Delivery Address
                    </h2>

                    <div
                        class="mt-5 rounded-xl border border-border bg-background p-4"
                    >
                        <p
                            class="whitespace-pre-line text-sm leading-6 text-body"
                        >
                            {{ props.order.delivery_address }}
                        </p>
                    </div>
                </section>

                <!-- ==================== Order Items ==================== -->

                <section
                    class="overflow-hidden rounded-2xl border border-border bg-surface shadow-sm"
                >
                    <div class="border-b border-border px-5 py-4 sm:px-6">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p
                                    class="text-xs font-bold uppercase tracking-[0.16em] text-brand-600"
                                >
                                    Order
                                </p>

                                <h2
                                    class="mt-1 text-lg font-semibold text-heading"
                                >
                                    Order Items
                                </h2>
                            </div>

                            <span
                                class="rounded-full bg-brand-50 px-3 py-1 text-xs font-bold text-brand-700"
                            >
                                {{ props.order.items.length }}
                                {{
                                    props.order.items.length === 1
                                        ? "item"
                                        : "items"
                                }}
                            </span>
                        </div>
                    </div>

                    <div class="divide-y divide-border">
                        <div
                            v-for="item in props.order.items"
                            :key="item.id"
                            class="p-5 sm:p-6"
                        >
                            <div class="flex gap-4">
                                <!-- ==================== Product Image ==================== -->

                                <div
                                    class="h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-background"
                                >
                                    <img
                                        v-if="item.product.images.length > 0"
                                        :src="`/storage/${item.product.images[0].image}`"
                                        :alt="item.product.name"
                                        class="h-full w-full object-cover"
                                    />

                                    <div
                                        v-else
                                        class="flex h-full w-full items-center justify-center text-xs font-medium text-muted"
                                    >
                                        No image
                                    </div>
                                </div>

                                <!-- ==================== Product Information ==================== -->

                                <div class="min-w-0 flex-1">
                                    <div
                                        class="flex flex-col justify-between gap-2 sm:flex-row"
                                    >
                                        <div class="min-w-0">
                                            <h3
                                                class="truncate font-semibold text-heading"
                                            >
                                                {{ item.product.name }}
                                            </h3>

                                            <p
                                                v-if="item.variant_name"
                                                class="mt-1 text-sm text-muted"
                                            >
                                                {{ item.variant_name }}
                                            </p>

                                            <p
                                                v-if="item.variant?.sku"
                                                class="mt-1 text-xs text-muted"
                                            >
                                                SKU: {{ item.variant.sku }}
                                            </p>

                                            <p
                                                class="mt-2 text-xs font-medium text-muted"
                                            >
                                                Seller:
                                                {{ item.seller.first_name }}
                                                {{ item.seller.last_name }}
                                            </p>
                                        </div>

                                        <p
                                            class="shrink-0 font-bold text-heading"
                                        >
                                            {{
                                                Number(item.subtotal).toFixed(2)
                                            }}
                                        </p>
                                    </div>

                                    <div
                                        class="mt-4 flex flex-wrap gap-2 text-xs"
                                    >
                                        <span
                                            class="rounded-lg bg-background px-3 py-1.5 font-medium text-body"
                                        >
                                            Quantity: {{ item.quantity }}
                                        </span>

                                        <span
                                            class="rounded-lg bg-background px-3 py-1.5 font-medium text-body"
                                        >
                                            Unit:
                                            {{
                                                Number(item.unit_price).toFixed(
                                                    2,
                                                )
                                            }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==================== Custom Request ==================== -->

                <section
                    v-if="props.order.custom_request"
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
                >
                    <p
                        class="text-xs font-bold uppercase tracking-[0.16em] text-brand-600"
                    >
                        Customer Note
                    </p>

                    <h2 class="mt-1 text-lg font-semibold text-heading">
                        Custom Request
                    </h2>

                    <div
                        class="mt-4 rounded-xl border border-border bg-background p-4"
                    >
                        <p
                            class="whitespace-pre-line text-sm leading-6 text-body"
                        >
                            {{ props.order.custom_request }}
                        </p>
                    </div>
                </section>

                <!-- ==================== Order Tracking ==================== -->

                <section
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
                >
                    <p
                        class="text-xs font-bold uppercase tracking-[0.16em] text-brand-600"
                    >
                        Timeline
                    </p>

                    <h2 class="mt-1 text-lg font-semibold text-heading">
                        Order Tracking
                    </h2>

                    <div class="mt-6 space-y-6">
                        <div
                            v-for="(history, index) in props.order
                                .status_histories"
                            :key="history.id"
                            class="relative pl-7"
                        >
                            <!-- Timeline Line -->

                            <div
                                v-if="
                                    index <
                                    props.order.status_histories.length - 1
                                "
                                class="absolute left-[5px] top-3 h-full w-px bg-border"
                            ></div>

                            <!-- Timeline Dot -->

                            <div
                                class="absolute left-0 top-1.5 h-3 w-3 rounded-full border-2 border-surface bg-brand-600 ring-4 ring-brand-50"
                            ></div>

                            <div>
                                <div
                                    class="flex flex-col justify-between gap-1 sm:flex-row sm:items-center"
                                >
                                    <p
                                        class="font-semibold capitalize text-heading"
                                    >
                                        {{ formatStatus(history.status) }}
                                    </p>

                                    <p class="text-xs text-muted">
                                        {{ formatDate(history.created_at) }}
                                    </p>
                                </div>

                                <p
                                    v-if="history.changed_by"
                                    class="mt-1 text-sm text-muted"
                                >
                                    Changed by
                                    <span class="font-medium text-body">
                                        {{ history.changed_by.first_name }}
                                        {{ history.changed_by.last_name }}
                                    </span>
                                </p>

                                <p
                                    v-if="history.note"
                                    class="mt-2 text-sm leading-6 text-body"
                                >
                                    {{ history.note }}
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- ==================== Right Column ==================== -->

            <div class="space-y-6">
                <!-- ==================== Delivery Assignment ==================== -->

                <section
                    v-if="props.order.delivery_assignment"
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
                >
                    <p
                        class="text-xs font-bold uppercase tracking-[0.16em] text-brand-600"
                    >
                        Delivery
                    </p>

                    <h2 class="mt-1 text-lg font-semibold text-heading">
                        Delivery Assignment
                    </h2>

                    <p class="mt-2 text-sm text-muted">
                        Current delivery assignment for this order.
                    </p>

                    <div class="mt-5 space-y-4">
                        <!-- Assigned User -->

                        <div
                            class="rounded-xl border border-border bg-background p-4"
                        >
                            <p
                                class="text-[11px] font-bold uppercase tracking-wide text-muted"
                            >
                                Assigned To
                            </p>

                            <p class="mt-1 font-semibold text-heading">
                                {{
                                    props.order.delivery_assignment
                                        .delivery_user?.first_name
                                }}
                                {{
                                    props.order.delivery_assignment
                                        .delivery_user?.last_name
                                }}
                            </p>
                        </div>

                        <!-- Assignment Details -->

                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-xl border border-border p-4">
                                <p
                                    class="text-[11px] font-bold uppercase tracking-wide text-muted"
                                >
                                    Type
                                </p>

                                <p
                                    class="mt-1 text-sm font-semibold capitalize text-heading"
                                >
                                    {{ props.order.delivery_assignment.type }}
                                </p>
                            </div>

                            <div class="rounded-xl border border-border p-4">
                                <p
                                    class="text-[11px] font-bold uppercase tracking-wide text-muted"
                                >
                                    Lock
                                </p>

                                <span
                                    class="mt-1 inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700"
                                >
                                    Locked
                                </span>
                            </div>
                        </div>

                        <!-- Admin Note -->

                        <div v-if="props.order.delivery_assignment.admin_note">
                            <p
                                class="text-[11px] font-bold uppercase tracking-wide text-muted"
                            >
                                Admin Note
                            </p>

                            <p class="mt-2 text-sm leading-6 text-body">
                                {{ props.order.delivery_assignment.admin_note }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- ==================== Assign Delivery ==================== -->

                <section
                    v-if="
                        !props.order.delivery_assignment &&
                        props.order.status !== 'rejected' &&
                        props.order.status !== 'delivered'
                    "
                    class="rounded-2xl border border-brand-100 bg-surface p-5 shadow-sm sm:p-6"
                >
                    <div class="mb-5 flex items-start gap-3">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                        >
                            <Truck class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="text-lg font-semibold text-heading">
                                Assign Delivery
                            </h2>

                            <p class="mt-1 text-sm text-muted">
                                Assign this order to a delivery user.
                            </p>
                        </div>
                    </div>

                    <div>
                        <label
                            for="delivery-user"
                            class="mb-2 block text-sm font-semibold text-body"
                        >
                            Delivery User
                        </label>

                        <select
                            id="delivery-user"
                            v-model="selectedDeliveryUser"
                            class="w-full rounded-xl border border-border bg-surface px-4 py-3 text-sm text-heading outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-100"
                        >
                            <option :value="null">Select delivery user</option>

                            <option
                                v-for="user in props.deliveryUsers"
                                :key="user.id"
                                :value="user.id"
                            >
                                {{ user.first_name }}
                                {{ user.last_name }}
                            </option>
                        </select>
                    </div>

                    <div class="mt-4">
                        <label
                            for="admin-note"
                            class="mb-2 block text-sm font-semibold text-body"
                        >
                            Admin Note
                        </label>

                        <textarea
                            id="admin-note"
                            v-model="adminNote"
                            rows="3"
                            placeholder="Optional note for the delivery user..."
                            class="w-full resize-none rounded-xl border border-border bg-surface px-4 py-3 text-sm text-heading outline-none transition placeholder:text-muted focus:border-brand-500 focus:ring-2 focus:ring-brand-100"
                        ></textarea>
                    </div>

                    <Button
                        type="button"
                        :disabled="!selectedDeliveryUser || assigningDelivery"
                        class="mt-4 w-full rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-50"
                        @click="assignDelivery"
                    >
                        {{
                            assigningDelivery
                                ? "Assigning..."
                                : "Assign Delivery"
                        }}
                    </Button>
                </section>

                <!-- ==================== Reassign Delivery ==================== -->

                <section
                    v-if="
                        props.order.delivery_assignment &&
                        props.order.status !== 'delivered' &&
                        props.order.status !== 'rejected'
                    "
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
                >
                    <p
                        class="text-xs font-bold uppercase tracking-[0.16em] text-brand-600"
                    >
                        Management
                    </p>

                    <h2 class="mt-1 text-lg font-semibold text-heading">
                        Reassign Delivery
                    </h2>

                    <p class="mt-2 text-sm text-muted">
                        Assign this order to another delivery user.
                    </p>

                    <div class="mt-5">
                        <label
                            for="reassign-delivery-user"
                            class="mb-2 block text-sm font-semibold text-body"
                        >
                            Delivery User
                        </label>

                        <select
                            id="reassign-delivery-user"
                            v-model="selectedDeliveryUser"
                            class="w-full rounded-xl border border-border bg-surface px-4 py-3 text-sm text-heading outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-100"
                        >
                            <option :value="null">Select delivery user</option>

                            <option
                                v-for="deliveryUser in props.deliveryUsers"
                                :key="deliveryUser.id"
                                :value="deliveryUser.id"
                            >
                                {{ deliveryUser.first_name }}
                                {{ deliveryUser.last_name }}
                            </option>
                        </select>
                    </div>

                    <div class="mt-4">
                        <label
                            for="reassign-note"
                            class="mb-2 block text-sm font-semibold text-body"
                        >
                            Admin Note
                        </label>

                        <textarea
                            id="reassign-note"
                            v-model="reassignNote"
                            rows="3"
                            placeholder="Optional note..."
                            class="w-full resize-none rounded-xl border border-border bg-surface px-4 py-3 text-sm text-heading outline-none transition placeholder:text-muted focus:border-brand-500 focus:ring-2 focus:ring-brand-100"
                        ></textarea>
                    </div>

                    <Button
                        type="button"
                        :disabled="
                            !selectedDeliveryUser ||
                            selectedDeliveryUser ===
                                props.order.delivery_assignment.delivery_user
                                    ?.id ||
                            reassigningDelivery
                        "
                        class="mt-4 w-full rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-50"
                        @click="reassignDelivery"
                    >
                        {{
                            reassigningDelivery
                                ? "Reassigning..."
                                : "Reassign Delivery"
                        }}
                    </Button>
                </section>

                <!-- ==================== Rejection ==================== -->

                <section
                    v-if="props.order.rejection_reason"
                    class="rounded-2xl border border-red-100 bg-red-50 p-5 sm:p-6"
                >
                    <p
                        class="text-xs font-bold uppercase tracking-[0.16em] text-red-600"
                    >
                        Order Rejected
                    </p>

                    <h2 class="mt-1 text-lg font-semibold text-red-900">
                        Rejection Reason
                    </h2>

                    <p class="mt-3 text-sm leading-6 text-red-700">
                        {{ props.order.rejection_reason }}
                    </p>
                </section>

                <!-- ==================== Order Summary ==================== -->

                <section
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
                >
                    <p
                        class="text-xs font-bold uppercase tracking-[0.16em] text-brand-600"
                    >
                        Summary
                    </p>

                    <h2 class="mt-1 text-lg font-semibold text-heading">
                        Order Summary
                    </h2>

                    <div
                        class="mt-5 flex items-end justify-between gap-4 border-t border-border pt-5"
                    >
                        <span class="text-sm font-medium text-muted">
                            Total
                        </span>

                        <span
                            class="text-2xl font-bold tracking-tight text-heading"
                        >
                            {{ Number(props.order.total_amount).toFixed(2) }}
                        </span>
                    </div>
                </section>

                <!-- ==================== Admin Actions ==================== -->

                <section
                    v-if="
                        props.order.status !== 'rejected' &&
                        props.order.status !== 'delivered'
                    "
                    class="rounded-2xl border border-red-100 bg-surface p-5 shadow-sm sm:p-6"
                >
                    <p
                        class="text-xs font-bold uppercase tracking-[0.16em] text-red-600"
                    >
                        Admin Action
                    </p>

                    <h2 class="mt-1 text-lg font-semibold text-heading">
                        Reject Order
                    </h2>

                    <p class="mt-2 text-sm text-muted">
                        Provide a reason before rejecting this order.
                    </p>

                    <div class="mt-5">
                        <label
                            for="rejection_reason"
                            class="mb-2 block text-sm font-semibold text-body"
                        >
                            Rejection Reason
                        </label>

                        <textarea
                            id="rejection_reason"
                            v-model="rejectionReason"
                            rows="4"
                            class="w-full resize-none rounded-xl border border-border bg-surface px-4 py-3 text-sm text-heading outline-none transition placeholder:text-muted focus:border-red-400 focus:ring-2 focus:ring-red-100"
                            placeholder="Explain why this order is being rejected..."
                        ></textarea>

                        <Button
                            type="button"
                            :disabled="!rejectionReason.trim() || rejecting"
                            class="mt-3 w-full rounded-xl bg-red-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50"
                            @click="rejectOrder"
                        >
                            {{ rejecting ? "Rejecting..." : "Reject Order" }}
                        </Button>
                    </div>
                </section>
            </div>
        </div>
    </AdminLayout>
</template>
