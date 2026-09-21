<script setup lang="ts">
// ==================== Imports ====================

import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";
import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import Button from "@/Components/UI/Button.vue";
import { MapPin, ShoppingBag } from "lucide-vue-next";

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

// ==================== State ====================

const deliveryAddress = ref("");

// ==================== Total ====================

const checkoutTotal = computed(() => {
    return props.cart.items.reduce((total, item) => {
        const price = Number(item.variant?.price ?? item.product.price);

        return total + price * item.quantity;
    }, 0);
});

// ==================== Place Order ====================

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
</script>

<template>
    <CustomerLayout>
        <!-- ==================== Checkout ==================== -->

        <main class="bg-background">
            <div
                class="mx-auto w-full max-w-7xl px-5 py-6 sm:px-8 sm:py-8 lg:py-10"
            >
                <!-- ==================== Page Header ==================== -->

                <div class="mb-7">
                    <h1
                        class="text-2xl font-extrabold tracking-tight text-heading sm:text-3xl"
                    >
                        Checkout
                    </h1>

                    <p class="mt-1.5 text-sm text-muted">
                        Review your order and delivery details.
                    </p>
                </div>

                <!-- ==================== Checkout Layout ==================== -->

                <div
                    class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px] lg:items-start"
                >
                    <!-- ==================== Order Details ==================== -->

                    <section
                        class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
                    >
                        <!-- ==================== Section Header ==================== -->

                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                            >
                                <ShoppingBag :size="20" :stroke-width="2" />
                            </div>

                            <div>
                                <h2
                                    class="text-base font-bold text-heading sm:text-lg"
                                >
                                    Your Order
                                </h2>

                                <p class="mt-0.5 text-xs text-muted">
                                    {{ props.cart.items.length }}
                                    {{
                                        props.cart.items.length === 1
                                            ? "item"
                                            : "items"
                                    }}
                                </p>
                            </div>
                        </div>

                        <!-- ==================== Order Items ==================== -->

                        <div class="mt-6 space-y-4">
                            <div
                                v-for="item in props.cart.items"
                                :key="item.id"
                                class="flex gap-4 border-b border-border pb-4 last:border-b-0 last:pb-0"
                            >
                                <!-- ==================== Product Image ==================== -->

                                <div
                                    class="h-20 w-20 shrink-0 overflow-hidden rounded-xl border border-border bg-background sm:h-24 sm:w-24"
                                >
                                    <img
                                        v-if="item.product.images.length"
                                        :src="`/storage/${item.product.images[0].image}`"
                                        :alt="item.product.name"
                                        class="h-full w-full object-cover"
                                    />

                                    <div
                                        v-else
                                        class="flex h-full flex-col items-center justify-center gap-1 text-muted"
                                    >
                                        <ShoppingBag
                                            :size="19"
                                            :stroke-width="1.5"
                                        />

                                        <span class="text-[10px] font-medium">
                                            No image
                                        </span>
                                    </div>
                                </div>

                                <!-- ==================== Product Information ==================== -->

                                <div class="min-w-0 flex-1">
                                    <h3
                                        class="truncate text-sm font-bold text-heading sm:text-base"
                                    >
                                        {{ item.product.name }}
                                    </h3>

                                    <!-- ==================== Variant ==================== -->

                                    <div
                                        v-if="item.variant"
                                        class="mt-2 flex flex-wrap gap-1.5"
                                    >
                                        <span
                                            v-for="value in item.variant
                                                .variant_values"
                                            :key="value.id"
                                            class="rounded-lg border border-border bg-background px-2 py-1 text-[11px] font-medium text-muted"
                                        >
                                            {{ value.option_value.value }}
                                        </span>
                                    </div>

                                    <p
                                        class="mt-2 text-xs font-medium text-muted"
                                    >
                                        Quantity:
                                        <span class="font-bold text-body">
                                            {{ item.quantity }}
                                        </span>
                                    </p>
                                </div>

                                <!-- ==================== Item Total ==================== -->

                                <div class="shrink-0 text-right">
                                    <p
                                        class="text-sm font-extrabold tracking-tight text-heading sm:text-base"
                                    >
                                        {{
                                            (
                                                Number(
                                                    item.variant?.price ??
                                                        item.product.price,
                                                ) * item.quantity
                                            ).toFixed(2)
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- ==================== Delivery & Summary ==================== -->

                    <aside class="space-y-5 lg:sticky lg:top-6">
                        <!-- ==================== Delivery Address ==================== -->

                        <section
                            class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
                        >
                            <!-- ==================== Section Header ==================== -->

                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                                >
                                    <MapPin :size="20" :stroke-width="2" />
                                </div>

                                <div>
                                    <h2
                                        class="text-base font-bold text-heading"
                                    >
                                        Delivery Address
                                    </h2>

                                    <p class="mt-0.5 text-xs text-muted">
                                        Where should we deliver your order?
                                    </p>
                                </div>
                            </div>

                            <!-- ==================== Address Input ==================== -->

                            <textarea
                                v-model="deliveryAddress"
                                rows="4"
                                placeholder="Enter your delivery address..."
                                class="mt-5 w-full resize-none rounded-xl border border-border bg-background px-4 py-3 text-sm leading-6 text-heading outline-none transition-all duration-200 placeholder:text-muted hover:border-slate-300 focus:border-brand-500 focus:bg-surface focus:ring-4 focus:ring-brand-500/10"
                            ></textarea>
                        </section>

                        <!-- ==================== Order Summary ==================== -->

                        <section
                            class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
                        >
                            <h2 class="text-base font-bold text-heading">
                                Order Summary
                            </h2>

                            <div class="mt-5 border-t border-border pt-4">
                                <div
                                    class="flex items-center justify-between gap-4"
                                >
                                    <span
                                        class="text-sm font-medium text-muted"
                                    >
                                        Total
                                    </span>

                                    <span
                                        class="text-xl font-extrabold tracking-tight text-heading"
                                    >
                                        {{ checkoutTotal.toFixed(2) }}
                                    </span>
                                </div>
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

                            <p
                                class="mt-3 text-center text-xs leading-5 text-muted"
                            >
                                Your order will be submitted immediately after
                                confirmation.
                            </p>
                        </section>
                    </aside>
                </div>
            </div>
        </main>
    </CustomerLayout>
</template>
