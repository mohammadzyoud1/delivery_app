<script setup lang="ts">
import SellerLayout from "@/Layouts/SellerLayout.vue";
import { computed } from "vue";
import { t } from "@/Utils/i18n";

// ==================== Types ====================

interface Product {
    id: number;
    name: string;
}

interface Variant {
    id: number;
    sku: string | null;
}

interface Order {
    id: number;
    status: string;
    created_at: string;
}

interface OrderItem {
    id: number;
    order_id: number;
    quantity: number;
    unit_price: number;
    subtotal: number;
    product: Product;
    variant: Variant | null;
    order: Order;
}

const props = defineProps<{
    orderItems: OrderItem[];
}>();

// ==================== Group Orders ====================

const orders = computed(() => {
    const grouped = new Map<
        number,
        {
            order: Order;
            items: OrderItem[];
            total: number;
        }
    >();

    props.orderItems.forEach((item) => {
        if (!grouped.has(item.order_id)) {
            grouped.set(item.order_id, {
                order: item.order,
                items: [],
                total: 0,
            });
        }

        const currentOrder = grouped.get(item.order_id)!;

        currentOrder.items.push(item);
        currentOrder.total += Number(item.subtotal);
    });

    return Array.from(grouped.values());
});

// ==================== Helpers ====================

const formatPrice = (price: number) => {
    return Number(price).toFixed(2);
};

//const formatStatus = (status: string) => {
//    return status.replaceAll("_", " ");
//};
</script>

<template>
    <SellerLayout>
        <div class="mx-auto w-full max-w-7xl">
            <!-- ==================== Page Header ==================== -->

            <section class="mb-8">
                <div
                    class="mt-1 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between"
                >
                    <div>
                        <h1
                            class="text-2xl font-extrabold tracking-tight text-heading sm:text-3xl"
                        >
                            {{ t("orders") }}
                        </h1>

                        <p class="mt-2 text-sm leading-6 text-muted">
                            {{ t("ordersContainingStoreProducts") }}
                        </p>
                    </div>

                    <div
                        v-if="orders.length"
                        class="w-fit rounded-full border border-border bg-surface px-3.5 py-2 text-xs font-bold text-body"
                    >
                        {{ orders.length }}
                        {{ orders.length === 1 ? "order" : "orders" }}
                    </div>
                </div>
            </section>

            <!-- ==================== Empty State ==================== -->

            <section
                v-if="!orders.length"
                class="rounded-2xl border border-border bg-surface p-8 text-center shadow-sm sm:p-12"
            >
                <div
                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="26"
                        height="26"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path
                            d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"
                        />
                        <path d="M3 6h18" />
                        <path d="M16 10a4 4 0 0 1-8 0" />
                    </svg>
                </div>

                <h2 class="mt-5 text-base font-extrabold text-heading">
                    {{ t("noOrders") }}
                </h2>

                <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-muted">
                    {{ t("ordersContainingProductsAppear") }}
                </p>
            </section>

            <!-- ==================== Orders ==================== -->

            <div v-else class="space-y-5">
                <article
                    v-for="sellerOrder in orders"
                    :key="sellerOrder.order.id"
                    class="overflow-hidden rounded-2xl border border-border bg-surface shadow-sm"
                >
                    <!-- ==================== Order Header ==================== -->

                    <div
                        class="border-b border-border bg-background/70 px-5 py-5 sm:px-6"
                    >
                        <div
                            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2
                                        class="text-base font-extrabold text-heading"
                                    >
                                        {{ t("order") }} #{{
                                            sellerOrder.order.id
                                        }}
                                    </h2>

                                    <!-- ==================== Status ==================== -->

                                    <span
                                        class="w-fit rounded-full border px-3 py-1 text-[11px] font-bold capitalize"
                                        :class="
                                            sellerOrder.order.status ===
                                            'delivered'
                                                ? 'border-green-200 bg-green-50 text-green-700'
                                                : sellerOrder.order.status ===
                                                    'rejected'
                                                  ? 'border-red-200 bg-red-50 text-red-700'
                                                  : 'border-brand-100 bg-brand-50 text-brand-700'
                                        "
                                    >
                                        {{ t(sellerOrder.order.status) }}
                                    </span>
                                </div>

                                <p class="mt-1.5 text-xs text-muted">
                                    {{ sellerOrder.items.length }}
                                    {{
                                        sellerOrder.items.length === 1
                                            ? t("product")
                                            : t("products")
                                    }}
                                    {{ t("fromYourStore") }}
                                </p>
                            </div>

                            <div class="sm:text-right">
                                <p class="text-xs font-medium text-muted">
                                    {{ t("ordered") }}
                                </p>

                                <p class="mt-1 text-sm font-semibold text-body">
                                    {{ sellerOrder.order.created_at }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== Seller Products ==================== -->

                    <div class="divide-y divide-border">
                        <div
                            v-for="item in sellerOrder.items"
                            :key="item.id"
                            class="px-5 py-5 sm:px-6"
                        >
                            <div
                                class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
                            >
                                <!-- ==================== Product ==================== -->

                                <div class="min-w-0">
                                    <h3
                                        class="truncate text-sm font-extrabold text-heading"
                                    >
                                        {{ item.product.name }}
                                    </h3>

                                    <div
                                        v-if="item.variant"
                                        class="mt-1.5 flex flex-wrap items-center gap-2"
                                    >
                                        <span
                                            class="rounded-md bg-background px-2 py-1 text-xs font-medium text-muted"
                                        >
                                            SKU
                                        </span>

                                        <span
                                            class="text-xs font-medium text-body"
                                        >
                                            {{ item.variant.sku || "No SKU" }}
                                        </span>
                                    </div>
                                </div>

                                <!-- ==================== Product Summary ==================== -->

                                <div
                                    class="grid grid-cols-3 gap-3 sm:max-w-xl sm:flex-1 lg:max-w-2xl"
                                >
                                    <div
                                        class="rounded-xl bg-background px-3 py-3"
                                    >
                                        <p
                                            class="text-[11px] font-bold uppercase tracking-wide text-muted"
                                        >
                                            {{ t("quantity") }}
                                        </p>

                                        <p
                                            class="mt-1 text-sm font-extrabold text-heading"
                                        >
                                            {{ item.quantity }}
                                        </p>
                                    </div>

                                    <div
                                        class="rounded-xl bg-background px-3 py-3"
                                    >
                                        <p
                                            class="text-[11px] font-bold uppercase tracking-wide text-muted"
                                        >
                                            {{ t("price") }}
                                        </p>

                                        <p
                                            class="mt-1 text-sm font-extrabold text-heading"
                                        >
                                            {{ formatPrice(item.unit_price) }}
                                        </p>
                                    </div>

                                    <div
                                        class="rounded-xl bg-brand-50 px-3 py-3"
                                    >
                                        <p
                                            class="text-[11px] font-bold uppercase tracking-wide text-brand-600"
                                        >
                                            {{ t("subtotal") }}
                                        </p>

                                        <p
                                            class="mt-1 text-sm font-extrabold text-brand-700"
                                        >
                                            {{ formatPrice(item.subtotal) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== Seller Order Total ==================== -->

                    <div
                        class="flex items-center justify-between border-t border-border bg-background/50 px-5 py-4 sm:px-6"
                    >
                        <div>
                            <p class="text-xs font-medium text-muted">
                                {{ t("orderTotal") }}
                            </p>

                            <p class="mt-0.5 text-xs text-muted">
                                {{ t("productsFromYourStore") }}
                            </p>
                        </div>

                        <span
                            class="text-xl font-extrabold tracking-tight text-heading"
                        >
                            {{ formatPrice(sellerOrder.total) }}
                        </span>
                    </div>
                </article>
            </div>
        </div>
    </SellerLayout>
</template>
