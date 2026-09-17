<script setup lang="ts">
// ==================== Imports ====================

import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import { router, usePage } from "@inertiajs/vue3";
import { Minus, Plus, Trash2 } from "lucide-vue-next";
import Button from "@/Components/UI/Button.vue";
import { computed } from "vue";
// ==================== Types ====================
const page = usePage<{
    flash: {
        success?: string;
        error?: string;
    };
}>();
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
    stock: number;
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

// ==================== Cart Actions ====================

const updateQuantity = (itemId: number, quantity: number) => {
    if (quantity < 1) {
        return;
    }

    router.patch(
        `/cart/items/${itemId}`,
        {
            quantity,
        },
        {
            preserveScroll: true,
        },
    );
};

const removeItem = (itemId: number) => {
    router.delete(`/cart/items/${itemId}`, {
        preserveScroll: true,
    });
};

// ==================== Cart Total ====================

const cartTotal = computed(() => {
    if (!props.cart) {
        return 0;
    }

    return props.cart.items.reduce((total, item) => {
        const price = Number(item.variant?.price ?? item.product.price);

        return total + price * item.quantity;
    }, 0);
});
// ==================== Stock Helpers ====================

const getAvailableStock = (item: CartItem): number => {
    return item.variant ? item.variant.stock : item.product.stock;
};

const isOutOfStock = (item: CartItem): boolean => {
    return getAvailableStock(item) <= 0;
};

const hasInsufficientStock = (item: CartItem): boolean => {
    const stock = getAvailableStock(item);

    return stock > 0 && item.quantity > stock;
};
// ==================== Props ====================

const props = defineProps<{
    cart: Cart | null;
}>();
</script>

<template>
    <CustomerLayout>
        <!-- ==================== Cart ==================== -->

        <div class="mx-auto max-w-6xl">
            <h1 class="text-2xl font-bold text-gray-900">Your Cart</h1>

            <!-- ==================== Empty Cart ==================== -->

            <div
                v-if="!props.cart || !props.cart.items.length"
                class="mt-6 rounded-2xl bg-white p-8 text-center"
            >
                <p class="text-gray-500">Your cart is empty.</p>
            </div>
            <!-- ==================== Cart Items ==================== -->

            <div v-else class="mt-6 space-y-4">
                <div
                    v-for="item in props.cart.items"
                    :key="item.id"
                    class="rounded-2xl bg-white p-4 shadow-sm"
                >
                    <div class="flex gap-4">
                        <!-- ==================== Product Image ==================== -->

                        <div
                            class="h-24 w-24 shrink-0 overflow-hidden rounded-xl bg-gray-100 sm:h-28 sm:w-28"
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
                            <h2 class="truncate font-semibold text-gray-900">
                                {{ item.product.name }}
                            </h2>

                            <!-- ==================== Variant ==================== -->

                            <div
                                v-if="item.variant"
                                class="mt-2 flex flex-wrap gap-1"
                            >
                                <span
                                    v-for="value in item.variant.variant_values"
                                    :key="value.id"
                                    class="rounded-lg bg-gray-100 px-2 py-1 text-xs text-gray-600"
                                >
                                    {{ value.option_value.value }}
                                </span>
                            </div>

                            <!-- ==================== Price ==================== -->

                            <p class="mt-2 font-semibold text-gray-900">
                                {{
                                    Number(
                                        item.variant?.price ??
                                            item.product.price,
                                    ).toFixed(2)
                                }}
                            </p>

                            <p
                                v-if="item.product.unit"
                                class="mt-1 text-xs text-gray-500"
                            >
                                Per {{ item.product.unit }}
                            </p>
                            <!-- ==================== Stock Warning ==================== -->

                            <div
                                v-if="isOutOfStock(item)"
                                class="mt-3 rounded-xl bg-red-50 px-3 py-2 text-sm font-medium text-red-600"
                            >
                                Out of stock
                            </div>

                            <div
                                v-else-if="hasInsufficientStock(item)"
                                class="mt-3 rounded-xl bg-amber-50 px-3 py-2 text-sm font-medium text-amber-700"
                            >
                                Only {{ getAvailableStock(item) }} available.
                                Please reduce your quantity.
                            </div>
                        </div>
                    </div>

                    <!-- ==================== Quantity ==================== -->

                    <div class="mt-4 flex items-center justify-between gap-4">
                        <div
                            class="inline-flex items-center rounded-xl border border-gray-200"
                        >
                            <button
                                type="button"
                                class="flex h-10 w-10 items-center justify-center text-gray-600 transition hover:text-gray-900 disabled:cursor-not-allowed disabled:opacity-40"
                                :disabled="item.quantity <= 1"
                                @click="
                                    updateQuantity(item.id, item.quantity - 1)
                                "
                            >
                                <Minus :size="18" />
                            </button>

                            <span
                                class="min-w-10 text-center text-sm font-semibold"
                            >
                                {{ item.quantity }}
                            </span>

                            <button
                                type="button"
                                class="flex h-10 w-10 items-center justify-center text-gray-600 transition hover:text-gray-900 disabled:cursor-not-allowed disabled:opacity-40"
                                :disabled="
                                    isOutOfStock(item) ||
                                    item.quantity >= getAvailableStock(item)
                                "
                                @click="
                                    updateQuantity(item.id, item.quantity + 1)
                                "
                            >
                                <Plus :size="18" />
                            </button>
                        </div>

                        <!-- ==================== Subtotal ==================== -->

                        <span class="font-semibold text-gray-900">
                            {{
                                (
                                    Number(
                                        item.variant?.price ??
                                            item.product.price,
                                    ) * item.quantity
                                ).toFixed(2)
                            }}
                        </span>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <Button
                            type="button"
                            class="inline-flex items-center gap-2 text-sm font-medium text-red-500 transition hover:text-red-600"
                            @click="removeItem(item.id)"
                        >
                            <Trash2 :size="16" />
                            Remove
                        </Button>
                    </div>
                </div>
            </div>
            <!-- ==================== Cart Summary ==================== -->

            <div
                v-if="props.cart && props.cart.items.length"
                class="mt-6 rounded-2xl bg-white p-5 shadow-sm"
            >
                <div class="flex items-center justify-between">
                    <span class="text-base font-medium text-gray-600">
                        Total
                    </span>

                    <span class="text-xl font-bold text-gray-900">
                        {{ cartTotal.toFixed(2) }}
                    </span>
                </div>

                <!-- ==================== Checkout ==================== -->

                <div class="mt-5">
                    <Button
                        type="button"
                        variant="primary"
                        size="lg"
                        fullWidth
                        :disabled="
                            props.cart.items.some(
                                (item) =>
                                    isOutOfStock(item) ||
                                    hasInsufficientStock(item),
                            )
                        "
                        @click="router.visit('/checkout')"
                    >
                        Checkout
                    </Button>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>
