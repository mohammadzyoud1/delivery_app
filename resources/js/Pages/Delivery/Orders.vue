<script setup lang="ts">
// ==================== Imports ====================

import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Button from "@/Components/UI/Button.vue";
// ==================== Types ====================

interface ProductImage {
    id: number;
    image: string;
}

interface Product {
    id: number;
    name: string;
    images: ProductImage[];
}

interface ProductVariant {
    id: number;
    price: number;
}

interface OrderItem {
    id: number;
    quantity: number;
    unit_price: number;
    subtotal: number;
    variant_name: string | null;
    product: Product;
    variant: ProductVariant | null;
}

interface Customer {
    id: number;
    first_name: string;
    last_name: string;
}

interface DeliveryAssignment {
    id: number;
    delivery_user_id: number;
    type: string;
    admin_note: string | null;
    taken_at: string | null;
    completed_at: string | null;
}

interface Order {
    id: number;
    customer_id: number;
    delivery_address: string;
    status: string;
    total_amount: number;
    created_at: string;
    customer: Customer;
    items: OrderItem[];
    delivery_assignment: DeliveryAssignment | null;
}

// ==================== Props ====================

const props = defineProps<{
    orders: Order[];
    deliveryUserId: number;
}>();

// ==================== Helpers ====================

const formatStatus = (status: string): string => {
    return status.replace(/_/g, " ");
};

const formatDate = (date: string): string => {
    return new Date(date).toLocaleDateString();
};

// ==================== Take Order ====================

const takeOrder = (orderId: number) => {
    router.post(
        `/delivery/orders/${orderId}/take`,
        {},
        {
            preserveScroll: true,
        },
    );
};
// ==================== Update Delivery Status ====================
const updateStatus = (orderId: number, status: string) => {
    router.patch(
        `/delivery/orders/${orderId}/status`,
        { status },
        {
            preserveScroll: true,
        },
    );
};
</script>

<template>
    <Head title="Delivery Orders" />

    <CustomerLayout>
        <!-- ==================== Page Header ==================== -->

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Delivery Orders</h1>

            <p class="mt-1 text-sm text-gray-500">
                Orders currently waiting for delivery.
            </p>
        </div>

        <!-- ==================== Empty State ==================== -->

        <div
            v-if="props.orders.length === 0"
            class="rounded-2xl bg-white p-8 text-center shadow-sm"
        >
            <p class="text-gray-500">
                There are no orders available for delivery right now.
            </p>
        </div>

        <!-- ==================== Orders ==================== -->

        <div v-else class="space-y-4">
            <div
                v-for="order in props.orders"
                :key="order.id"
                class="rounded-2xl bg-white p-5 shadow-sm"
            >
                <!-- ==================== Order Header ==================== -->

                <div
                    class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <h2 class="font-semibold text-gray-900">
                            Order #{{ order.id }}
                        </h2>

                        <p class="text-sm text-gray-500">
                            {{ formatDate(order.created_at) }}
                        </p>
                    </div>

                    <span
                        class="w-fit rounded-full bg-gray-100 px-3 py-1 text-sm font-medium capitalize text-gray-700"
                    >
                        {{ formatStatus(order.status) }}
                    </span>
                </div>

                <!-- ==================== Delivery Address ==================== -->

                <div class="mt-4">
                    <p class="text-xs font-medium uppercase text-gray-400">
                        Delivery Address
                    </p>

                    <p class="mt-1 text-sm text-gray-700">
                        {{ order.delivery_address }}
                    </p>
                </div>

                <!-- ==================== Order Items ==================== -->

                <div class="mt-4 space-y-3">
                    <div
                        v-for="item in order.items"
                        :key="item.id"
                        class="flex items-center gap-3"
                    >
                        <div
                            class="h-14 w-14 shrink-0 overflow-hidden rounded-xl bg-gray-100"
                        >
                            <img
                                v-if="item.product.images.length"
                                :src="`/storage/${item.product.images[0].image}`"
                                :alt="item.product.name"
                                class="h-full w-full object-cover"
                            />
                        </div>

                        <div class="min-w-0 flex-1">
                            <p
                                class="truncate text-sm font-medium text-gray-900"
                            >
                                {{ item.product.name }}
                            </p>

                            <p
                                v-if="item.variant_name"
                                class="text-xs text-gray-500"
                            >
                                {{ item.variant_name }}
                            </p>

                            <p class="text-xs text-gray-500">
                                Qty: {{ item.quantity }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ==================== Order Total ==================== -->

                <div
                    class="mt-4 flex items-center justify-between border-t border-gray-100 pt-4"
                >
                    <span class="text-sm text-gray-500"> Total </span>

                    <span class="font-semibold text-gray-900">
                        {{ Number(order.total_amount).toFixed(2) }}
                    </span>
                </div>

                <!-- ==================== Delivery Action ==================== -->

                <div
                    v-if="
                        order.delivery_assignment &&
                        order.delivery_assignment.delivery_user_id ===
                            props.deliveryUserId
                    "
                    class="mt-4 border-t border-gray-100 pt-4"
                >
                    <Button
                        v-if="order.status === 'preparing'"
                        type="button"
                        class="w-full rounded-xl bg-black px-4 py-3 text-sm font-semibold text-white transition hover:bg-gray-800"
                        @click="updateStatus(order.id, 'on_the_way')"
                    >
                        On the Way
                    </Button>

                    <Button
                        v-else-if="order.status === 'on_the_way'"
                        type="button"
                        class="w-full rounded-xl bg-black px-4 py-3 text-sm font-semibold text-white transition hover:bg-gray-800"
                        @click="updateStatus(order.id, 'at_the_door')"
                    >
                        At the Door
                    </Button>

                    <Button
                        v-else-if="order.status === 'at_the_door'"
                        type="button"
                        class="w-full rounded-xl bg-black px-4 py-3 text-sm font-semibold text-white transition hover:bg-gray-800"
                        @click="updateStatus(order.id, 'delivered')"
                    >
                        Mark as Delivered
                    </Button>

                    <p
                        v-else-if="order.status === 'delivered'"
                        class="text-center text-sm font-medium text-green-600"
                    >
                        Delivery completed.
                    </p>
                </div>

                <div
                    v-else-if="
                        order.status === 'accepted' &&
                        !order.delivery_assignment
                    "
                    class="mt-4 border-t border-gray-100 pt-4"
                >
                    <Button
                        type="button"
                        class="w-full rounded-xl bg-black px-4 py-3 text-sm font-semibold text-white transition hover:bg-gray-800"
                        @click="takeOrder(order.id)"
                    >
                        Take Order
                    </Button>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>
