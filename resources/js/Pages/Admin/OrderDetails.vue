```vue
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

interface DeliveryUser {
    id: number;
    first_name: string;
    last_name: string;
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
                class="mb-4 inline-flex items-center gap-2 text-sm font-medium text-gray-500 transition hover:text-gray-900"
            >
                <ArrowLeft class="h-4 w-4" />
                Back to Orders
            </Link>

            <div
                class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end"
            >
                <div>
                    <p class="text-sm text-gray-500">
                        Order placed {{ formatDate(props.order.created_at) }}
                    </p>

                    <h1
                        class="mt-1 text-2xl font-bold tracking-tight text-gray-900"
                    >
                        Order #{{ props.order.id }}
                    </h1>
                </div>

                <span
                    class="inline-flex w-fit rounded-full px-3 py-1.5 text-sm font-semibold capitalize"
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
                    class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6"
                >
                    <h2 class="font-semibold text-gray-900">Customer</h2>

                    <div class="mt-4">
                        <p class="font-medium text-gray-900">
                            {{ props.order.customer.first_name }}
                            {{ props.order.customer.last_name }}
                        </p>

                        <p class="mt-1 text-sm text-gray-500">
                            {{ props.order.customer.email }}
                        </p>

                        <p
                            v-if="props.order.customer.phone"
                            class="mt-1 text-sm text-gray-500"
                        >
                            {{ props.order.customer.phone }}
                        </p>
                    </div>
                </section>

                <!-- ==================== Order Items ==================== -->

                <section class="rounded-2xl border border-gray-200 bg-white">
                    <div class="border-b border-gray-100 px-5 py-4 sm:px-6">
                        <h2 class="font-semibold text-gray-900">Order Items</h2>
                    </div>

                    <div class="divide-y divide-gray-100">
                        <div
                            v-for="item in props.order.items"
                            :key="item.id"
                            class="flex gap-4 p-5 sm:p-6"
                        >
                            <!-- ==================== Product Image ==================== -->

                            <div
                                class="h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-gray-100"
                            >
                                <img
                                    v-if="item.product.images.length > 0"
                                    :src="`/storage/${item.product.images[0].image}`"
                                    :alt="item.product.name"
                                    class="h-full w-full object-cover"
                                />

                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center text-xs text-gray-400"
                                >
                                    No image
                                </div>
                            </div>

                            <!-- ==================== Product Information ==================== -->

                            <div class="min-w-0 flex-1">
                                <div
                                    class="flex flex-col justify-between gap-2 sm:flex-row"
                                >
                                    <div>
                                        <h3 class="font-medium text-gray-900">
                                            {{ item.product.name }}
                                        </h3>

                                        <p
                                            v-if="item.variant_name"
                                            class="mt-1 text-sm text-gray-500"
                                        >
                                            {{ item.variant_name }}
                                        </p>

                                        <p class="mt-1 text-xs text-gray-400">
                                            Seller:
                                            {{ item.seller.first_name }}
                                            {{ item.seller.last_name }}
                                        </p>
                                    </div>

                                    <p class="font-semibold text-gray-900">
                                        {{ Number(item.subtotal).toFixed(2) }}
                                    </p>
                                </div>

                                <p class="mt-2 text-sm text-gray-500">
                                    Quantity: {{ item.quantity }}
                                    ×
                                    {{ Number(item.unit_price).toFixed(2) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==================== Custom Request ==================== -->

                <section
                    v-if="props.order.custom_request"
                    class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6"
                >
                    <h2 class="font-semibold text-gray-900">Custom Request</h2>

                    <p
                        class="mt-3 whitespace-pre-line text-sm leading-6 text-gray-600"
                    >
                        {{ props.order.custom_request }}
                    </p>
                </section>

                <!-- ==================== Order Tracking ==================== -->

                <section
                    class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6"
                >
                    <h2 class="font-semibold text-gray-900">Order Tracking</h2>

                    <div class="mt-5 space-y-5">
                        <div
                            v-for="history in props.order.status_histories"
                            :key="history.id"
                            class="relative border-l-2 border-gray-200 pl-5"
                        >
                            <div
                                class="absolute -left-[5px] top-1 h-2 w-2 rounded-full bg-gray-900"
                            />

                            <p class="font-medium capitalize text-gray-900">
                                {{ formatStatus(history.status) }}
                            </p>

                            <p class="mt-1 text-xs text-gray-400">
                                {{ formatDate(history.created_at) }}
                            </p>

                            <p
                                v-if="history.changed_by"
                                class="mt-1 text-sm text-gray-500"
                            >
                                Changed by:
                                {{ history.changed_by.first_name }}
                                {{ history.changed_by.last_name }}
                            </p>

                            <p
                                v-if="history.note"
                                class="mt-1 text-sm text-gray-500"
                            >
                                {{ history.note }}
                            </p>
                        </div>
                    </div>
                </section>
            </div>

            <!-- ==================== Right Column ==================== -->

            <div class="space-y-6">
                <!-- ==================== Delivery Information ==================== -->

                <!-- ==================== Current Delivery Assignment ==================== -->

                <div
                    v-if="props.order.delivery_assignment"
                    class="rounded-2xl border border-gray-200 bg-white p-5"
                >
                    <div class="mb-5">
                        <h2 class="font-semibold text-gray-900">
                            Delivery Assignment
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Current delivery assignment for this order.
                        </p>
                    </div>

                    <!-- ==================== Assigned Delivery User ==================== -->

                    <div class="space-y-4">
                        <div>
                            <p
                                class="text-xs font-medium uppercase tracking-wide text-gray-400"
                            >
                                Assigned To
                            </p>

                            <p class="mt-1 text-sm font-semibold text-gray-900">
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

                        <!-- ==================== Assignment Type ==================== -->

                        <div>
                            <p
                                class="text-xs font-medium uppercase tracking-wide text-gray-400"
                            >
                                Assignment Type
                            </p>

                            <p
                                class="mt-1 text-sm font-medium capitalize text-gray-700"
                            >
                                {{ props.order.delivery_assignment.type }}
                            </p>
                        </div>

                        <!-- ==================== Order Lock ==================== -->

                        <div>
                            <p
                                class="text-xs font-medium uppercase tracking-wide text-gray-400"
                            >
                                Order Lock
                            </p>

                            <span
                                class="mt-1 inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700"
                            >
                                Locked
                            </span>
                        </div>

                        <!-- ==================== Admin Note ==================== -->

                        <div v-if="props.order.delivery_assignment.admin_note">
                            <p
                                class="text-xs font-medium uppercase tracking-wide text-gray-400"
                            >
                                Admin Note
                            </p>

                            <p class="mt-1 text-sm leading-6 text-gray-600">
                                {{ props.order.delivery_assignment.admin_note }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ==================== Rejection ==================== -->

                <section
                    v-if="props.order.rejection_reason"
                    class="rounded-2xl border border-red-100 bg-red-50 p-5 sm:p-6"
                >
                    <h2 class="font-semibold text-red-900">Rejection Reason</h2>

                    <p class="mt-2 text-sm leading-6 text-red-700">
                        {{ props.order.rejection_reason }}
                    </p>
                </section>

                <!-- ==================== Order Summary ==================== -->

                <section
                    class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6"
                >
                    <h2 class="font-semibold text-gray-900">Order Summary</h2>

                    <div
                        class="mt-5 flex items-center justify-between border-t border-gray-100 pt-4"
                    >
                        <span class="font-medium text-gray-700"> Total </span>

                        <span class="text-xl font-bold text-gray-900">
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
                    class="rounded-2xl border border-red-100 bg-white p-5 sm:p-6"
                >
                    <h2 class="font-semibold text-gray-900">Order Actions</h2>

                    <div class="mt-5">
                        <label
                            for="rejection_reason"
                            class="text-sm font-medium text-gray-700"
                        >
                            Rejection Reason
                        </label>

                        <textarea
                            id="rejection_reason"
                            v-model="rejectionReason"
                            rows="4"
                            class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-gray-400 focus:ring-1 focus:ring-gray-300"
                            placeholder="Explain why this order is being rejected..."
                        />

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
            <!-- ==================== Assign Delivery ==================== -->

            <div
                v-if="
                    !props.order.delivery_assignment &&
                    props.order.status !== 'rejected' &&
                    props.order.status !== 'delivered'
                "
                class="rounded-2xl border border-gray-200 bg-white p-5"
            >
                <div class="mb-5">
                    <h2 class="font-semibold text-gray-900">Assign Delivery</h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Assign this order to a delivery user.
                    </p>
                </div>

                <!-- ==================== Delivery User ==================== -->

                <div>
                    <label
                        for="delivery-user"
                        class="mb-2 block text-sm font-medium text-gray-700"
                    >
                        Delivery User
                    </label>

                    <select
                        id="delivery-user"
                        v-model="selectedDeliveryUser"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                    >
                        <option :value="null">Select delivery user</option>

                        <option
                            v-for="user in props.deliveryUsers"
                            :key="user.id"
                            :value="user.id"
                        >
                            {{ user.first_name }} {{ user.last_name }}
                        </option>
                    </select>
                </div>

                <!-- ==================== Admin Note ==================== -->

                <div class="mt-4">
                    <label
                        for="admin-note"
                        class="mb-2 block text-sm font-medium text-gray-700"
                    >
                        Admin Note
                    </label>

                    <textarea
                        id="admin-note"
                        v-model="adminNote"
                        rows="3"
                        placeholder="Optional note for the delivery user..."
                        class="w-full resize-none rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                    />
                </div>

                <!-- ==================== Assign Button ==================== -->

                <Button
                    type="button"
                    :disabled="!selectedDeliveryUser || assigningDelivery"
                    class="mt-4 w-full rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50"
                    @click="assignDelivery"
                >
                    {{ assigningDelivery ? "Assigning..." : "Assign Delivery" }}
                </Button>
            </div>
        </div>
    </AdminLayout>
</template>
```
