<script setup lang="ts">
import SellerLayout from "@/Layouts/SellerLayout.vue";
import { computed } from "vue";

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

            <div class="mb-6">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                    Orders
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Orders containing your products.
                </p>
            </div>

            <!-- ==================== Empty State ==================== -->

            <div
                v-if="!orders.length"
                class="rounded-2xl border border-gray-100 bg-white p-8 text-center shadow-sm"
            >
                <h3 class="text-base font-semibold text-gray-900">
                    No orders yet
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Orders containing your products will appear here.
                </p>
            </div>

            <div v-else class="space-y-4">
                <div
                    v-for="sellerOrder in orders"
                    :key="sellerOrder.order.id"
                    class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm"
                >
                    <!-- ==================== Order Header ==================== -->

                    <div
                        class="flex flex-col gap-2 border-b border-gray-100 pb-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <p class="font-semibold text-gray-900">
                                Order #{{ sellerOrder.order.id }}
                            </p>

                            <p class="mt-1 text-xs capitalize text-gray-500">
                                {{ sellerOrder.order.status }}
                            </p>
                        </div>

                        <p class="text-xs text-gray-500">
                            {{ sellerOrder.order.created_at }}
                        </p>
                    </div>

                    <!-- ==================== Seller's Products ==================== -->

                    <div class="divide-y divide-gray-100">
                        <div
                            v-for="item in sellerOrder.items"
                            :key="item.id"
                            class="py-4"
                        >
                            <p class="font-medium text-gray-900">
                                {{ item.product.name }}
                            </p>

                            <p
                                v-if="item.variant"
                                class="mt-1 text-sm text-gray-500"
                            >
                                SKU: {{ item.variant.sku || "No SKU" }}
                            </p>

                            <div
                                class="mt-3 grid grid-cols-2 gap-3 text-sm sm:grid-cols-3"
                            >
                                <div>
                                    <p class="text-gray-500">Quantity</p>
                                    <p class="mt-1 font-medium text-gray-900">
                                        {{ item.quantity }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-gray-500">Unit Price</p>
                                    <p class="mt-1 font-medium text-gray-900">
                                        {{ formatPrice(item.unit_price) }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-gray-500">Subtotal</p>
                                    <p class="mt-1 font-medium text-gray-900">
                                        {{ formatPrice(item.subtotal) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== Seller Order Total ==================== -->

                    <div
                        class="flex items-center justify-between border-t border-gray-100 pt-4"
                    >
                        <span class="text-sm font-medium text-gray-500">
                            Your total
                        </span>

                        <span class="text-lg font-bold text-gray-900">
                            {{ formatPrice(sellerOrder.total) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </SellerLayout>
</template>
