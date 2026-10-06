<script setup lang="ts">
// ==================== Imports ====================

import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Button from "@/Components/UI/Button.vue";
import { MessageCircle, Truck } from "lucide-vue-next";
import { t } from "@/Utils/i18n";

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
    custom_request: string | null;
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
        <!-- ==================== Delivery Orders Page ==================== -->
        <main class="bg-background">
            <div
                class="mx-auto w-full max-w-7xl px-5 py-6 sm:px-8 sm:py-8 lg:py-10"
            >
                <!-- ==================== Page Header ==================== -->
                <div class="mb-7">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                        >
                            <Truck :size="21" :stroke-width="2" />
                        </div>
                        <div>
                            <h1
                                class="text-2xl font-extrabold tracking-tight text-heading sm:text-3xl"
                            >
                                {{ t("deliveryOrders") }}
                            </h1>
                            <p class="mt-1 text-sm text-muted">
                                {{ t("orderswaiting") }}
                            </p>
                        </div>
                    </div>
                </div>
                <!-- ==================== Empty State ==================== -->
                <div
                    v-if="props.orders.length === 0"
                    class="rounded-2xl border border-border bg-surface px-6 py-12 text-center shadow-sm sm:px-8"
                >
                    <div
                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"
                    >
                        <Package :size="26" :stroke-width="1.8" />
                    </div>
                    <h2 class="mt-5 text-base font-bold text-heading">
                        No delivery orders
                    </h2>
                    <p
                        class="mx-auto mt-1.5 max-w-sm text-sm leading-6 text-muted"
                    >
                        There are no orders available for delivery right now.
                    </p>
                </div>
                <!-- ==================== Orders ==================== -->
                <div v-else class="grid gap-5 lg:grid-cols-2">
                    <article
                        v-for="order in props.orders"
                        :key="order.id"
                        class="flex h-full flex-col overflow-hidden rounded-2xl border border-border bg-surface shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-lg hover:shadow-slate-900/5"
                    >
                        <!-- ==================== Order Header ==================== -->
                        <div class="border-b border-border p-5 sm:p-6">
                            <div
                                class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
                            >
                                <div class="min-w-0">
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wider text-muted"
                                    >
                                        {{ t("order") }}
                                    </p>
                                    <h2
                                        class="mt-1 text-lg font-extrabold tracking-tight text-heading"
                                    >
                                        #{{ order.id }}
                                    </h2>
                                    <div
                                        class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-muted"
                                    >
                                        <span class="flex items-center gap-1">
                                            <CalendarDays :size="13" />
                                            {{ formatDate(order.created_at) }}
                                        </span>
                                        <span>
                                            {{ order.customer.first_name }}
                                            {{ order.customer.last_name }}
                                        </span>
                                    </div>
                                </div>
                                <!-- ==================== Status ==================== -->
                                <span
                                    class="w-fit rounded-full border border-brand-100 bg-brand-50 px-3.5 py-1.5 text-xs font-bold capitalize text-brand-700"
                                >
                                    {{ t(order.status) }}
                                </span>
                            </div>
                        </div>
                        <!-- ==================== Order Content ==================== -->
                        <div class="flex flex-1 flex-col p-5 sm:p-6">
                            <!-- ==================== Delivery Address ==================== -->
                            <div
                                class="rounded-xl border border-border bg-background p-4"
                            >
                                <div class="flex items-start gap-3">
                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                                    >
                                        <MapPin :size="18" :stroke-width="2" />
                                    </div>
                                    <div class="min-w-0">
                                        <p
                                            class="text-xs font-semibold uppercase tracking-wider text-muted"
                                        >
                                            {{ t("deliveryAddress") }}
                                        </p>
                                        <p
                                            class="mt-1.5 text-sm leading-6 text-body"
                                        >
                                            {{ order.delivery_address }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!-- ==================== Order Description ==================== -->

                            <div
                                v-if="order.custom_request"
                                class="mt-5 rounded-xl border border-border bg-background p-4"
                            >
                                <p
                                    class="text-xs font-semibold uppercase tracking-wider text-muted"
                                >
                                    {{ t("description") }}
                                </p>

                                <p class="mt-1.5 text-sm leading-6 text-body">
                                    {{ order.custom_request }}
                                </p>
                            </div>
                            <!-- ==================== Order Items ==================== -->
                            <div class="mt-5">
                                <div
                                    class="mb-3 flex items-center justify-between"
                                >
                                    <h3 class="text-sm font-bold text-heading">
                                        {{ t("items") }}
                                    </h3>
                                    <span
                                        class="text-xs font-medium text-muted"
                                    >
                                        {{ order.items.length }}
                                        {{
                                            order.items.length === 1
                                                ? t("item")
                                                : t("items")
                                        }}
                                    </span>
                                </div>
                                <div class="space-y-3">
                                    <div
                                        v-for="item in order.items"
                                        :key="item.id"
                                        class="flex gap-3.5 rounded-xl border border-border bg-surface p-3"
                                    >
                                        <!-- ==================== Product Image ==================== -->
                                        <div
                                            class="h-16 w-16 shrink-0 overflow-hidden rounded-xl border border-border bg-background"
                                        >
                                            <img
                                                v-if="
                                                    item.product.images.length
                                                "
                                                :src="`/storage/${item.product.images[0].image}`"
                                                :alt="item.product.name"
                                                class="h-full w-full object-cover"
                                            />
                                            <div
                                                v-else
                                                class="flex h-full items-center justify-center text-muted"
                                            >
                                                <Package
                                                    :size="18"
                                                    :stroke-width="1.5"
                                                />
                                            </div>
                                        </div>
                                        <!-- ==================== Product Information ==================== -->
                                        <div class="min-w-0 flex-1">
                                            <p
                                                class="truncate text-sm font-bold text-heading"
                                            >
                                                {{ item.product.name }}
                                            </p>
                                            <p
                                                v-if="item.variant_name"
                                                class="mt-1 truncate text-xs text-muted"
                                            >
                                                {{ t(item.variant_name) }}
                                            </p>
                                            <p
                                                class="mt-1.5 text-xs font-medium text-muted"
                                            >
                                                {{ t("quantity") }}:
                                                <span
                                                    class="font-bold text-body"
                                                >
                                                    {{ item.quantity }}
                                                </span>
                                            </p>
                                        </div>
                                        <!-- ==================== Item Total ==================== -->
                                        <div class="shrink-0 self-center">
                                            <p
                                                class="text-sm font-bold text-heading"
                                            >
                                                {{
                                                    Number(
                                                        item.subtotal,
                                                    ).toFixed(2)
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- ==================== Order Total ==================== -->
                            <div
                                class="mt-5 flex items-center justify-between border-t border-border pt-4"
                            >
                                <span class="text-sm font-medium text-muted">
                                    {{ t("total") }}
                                </span>
                                <span
                                    class="text-xl font-extrabold tracking-tight text-heading"
                                >
                                    {{ Number(order.total_amount).toFixed(2) }}
                                </span>
                            </div>
                            <!-- ==================== Delivery Actions ==================== -->
                            <div class="mt-auto border-t border-border pt-5">
                                <!-- ==================== Assigned Delivery ==================== -->
                                <div
                                    v-if="
                                        order.delivery_assignment &&
                                        order.delivery_assignment
                                            .delivery_user_id ===
                                            props.deliveryUserId &&
                                        order.status !== 'accepted'
                                    "
                                >
                                    <Button
                                        v-if="order.status === 'preparing'"
                                        type="button"
                                        variant="primary"
                                        size="lg"
                                        fullWidth
                                        @click="
                                            updateStatus(order.id, 'on_the_way')
                                        "
                                    >
                                        {{ t("on_the_way") }}
                                    </Button>
                                    <Button
                                        v-else-if="
                                            order.status === 'on_the_way'
                                        "
                                        type="button"
                                        variant="primary"
                                        size="lg"
                                        fullWidth
                                        @click="
                                            updateStatus(
                                                order.id,
                                                'at_the_door',
                                            )
                                        "
                                    >
                                        {{ t("at_the_door") }}
                                    </Button>
                                    <Button
                                        v-else-if="
                                            order.status === 'at_the_door'
                                        "
                                        type="button"
                                        variant="primary"
                                        size="lg"
                                        fullWidth
                                        @click="
                                            updateStatus(order.id, 'delivered')
                                        "
                                    >
                                        {{ t("markDelivered") }}
                                    </Button>
                                    <div
                                        v-else-if="order.status === 'delivered'"
                                        class="flex items-center justify-center gap-2 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-bold text-green-700"
                                    >
                                        <CheckCircle2 :size="18" />
                                        {{ t("deliveryComplete") }}
                                    </div>
                                </div>
                                <!-- ==================== Take Order ==================== -->
                                <Button
                                    v-else-if="
                                        order.status === 'accepted' &&
                                        (!order.delivery_assignment ||
                                            order.delivery_assignment
                                                .delivery_user_id ===
                                                props.deliveryUserId)
                                    "
                                    type="button"
                                    variant="primary"
                                    size="lg"
                                    fullWidth
                                    @click="takeOrder(order.id)"
                                >
                                    <Truck :size="19" :stroke-width="2" />
                                    {{ t("takeOrder") }}
                                </Button>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </main>
    </CustomerLayout>
</template>
