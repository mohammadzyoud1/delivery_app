<script setup lang="ts">
// ==================== Imports ====================
import { router } from "@inertiajs/vue3";
import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import Button from "@/Components/UI/Button.vue";
import { ref } from "vue";

// ==================== Types ====================

interface ProductImage {
    id: number;
    image: string;
}

interface OptionValue {
    id: number;
    value: string;
}

interface VariantValue {
    id: number;
    product_option_value_id: number;
    option_value: OptionValue;
}

interface ProductVariant {
    id: number;
    price: number;
    stock: number;
    variant_values: VariantValue[];
}

interface Product {
    id: number;
    name: string;
    price: number;
    unit: string | null;
    images: ProductImage[];
}

interface CartItem {
    id: number;
    quantity: number;
    product: Product;
    variant: ProductVariant | null;
}

interface Cart {
    id: number;
    items: CartItem[];
}

// ==================== Props ====================

const props = defineProps<{
    cart: Cart;
}>();

const deliveryAddress = ref("");
const placeOrder = () => {
    router.post(
        "/checkout",
        {
            delivery_address: deliveryAddress.value,
        },
        {
            preserveScroll: true,
        },
    );
};
// ==================== Total ====================

const checkoutTotal = () => {
    return props.cart.items.reduce((total, item) => {
        const price = Number(item.variant?.price ?? item.product.price);

        return total + price * item.quantity;
    }, 0);
};
</script>

<template>
    <CustomerLayout>
        <div class="mx-auto w-full max-w-6xl">
            <!-- ==================== Page Header ==================== -->

            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                    Checkout
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Review your order and delivery details.
                </p>
            </div>

            <!-- ==================== Checkout Content ==================== -->

            <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
                <!-- ==================== Order Details ==================== -->

                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900">
                        Your Order
                    </h2>

                    <div class="mt-5 space-y-4">
                        <div
                            v-for="item in props.cart.items"
                            :key="item.id"
                            class="flex gap-4 border-b border-gray-100 pb-4 last:border-b-0 last:pb-0"
                        >
                            <!-- ==================== Product Image ==================== -->

                            <div
                                class="h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-gray-100"
                            >
                                <img
                                    v-if="item.product.images.length"
                                    :src="`/storage/${item.product.images[0].image}`"
                                    :alt="item.product.name"
                                    class="h-full w-full object-cover"
                                />

                                <div
                                    v-else
                                    class="flex h-full items-center justify-center text-xs text-gray-400"
                                >
                                    No image
                                </div>
                            </div>

                            <!-- ==================== Product Information ==================== -->

                            <div class="min-w-0 flex-1">
                                <h3
                                    class="truncate font-semibold text-gray-900"
                                >
                                    {{ item.product.name }}
                                </h3>

                                <!-- ==================== Variant ==================== -->

                                <div
                                    v-if="item.variant"
                                    class="mt-1 flex flex-wrap gap-1"
                                >
                                    <span
                                        v-for="value in item.variant
                                            .variant_values"
                                        :key="value.id"
                                        class="rounded-md bg-gray-100 px-2 py-1 text-xs text-gray-600"
                                    >
                                        {{ value.option_value.value }}
                                    </span>
                                </div>

                                <p class="mt-2 text-sm text-gray-500">
                                    Quantity: {{ item.quantity }}
                                </p>
                            </div>

                            <!-- ==================== Item Total ==================== -->

                            <div
                                class="shrink-0 text-right font-semibold text-gray-900"
                            >
                                {{
                                    (
                                        Number(
                                            item.variant?.price ??
                                                item.product.price,
                                        ) * item.quantity
                                    ).toFixed(2)
                                }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== Delivery & Summary ==================== -->

                <div class="space-y-6">
                    <!-- ==================== Delivery Address ==================== -->

                    <div class="rounded-2xl bg-white p-5 shadow-sm">
                        <h2 class="text-lg font-semibold text-gray-900">
                            Delivery Address
                        </h2>

                        <textarea
                            v-model="deliveryAddress"
                            rows="4"
                            placeholder="Enter your delivery address"
                            class="mt-4 w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-gray-400 focus:ring-0"
                        ></textarea>
                    </div>

                    <!-- ==================== Order Summary ==================== -->

                    <div class="rounded-2xl bg-white p-5 shadow-sm">
                        <h2 class="text-lg font-semibold text-gray-900">
                            Order Summary
                        </h2>

                        <div
                            class="mt-4 flex items-center justify-between border-t border-gray-100 pt-4"
                        >
                            <span class="font-medium text-gray-600">
                                Total
                            </span>

                            <span class="text-xl font-bold text-gray-900">
                                {{ checkoutTotal().toFixed(2) }}
                            </span>
                        </div>

                        <!-- ==================== Place Order ==================== -->

                        <div class="mt-5">
                            <Button
                                type="button"
                                variant="primary"
                                size="lg"
                                fullWidth
                                :disabled="!deliveryAddress.trim()"
                                @click="placeOrder"
                            >
                                Place Order
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>
