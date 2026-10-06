<script setup lang="ts">
// ==================== Imports ====================
import { t } from "@/Utils/i18n";

import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import { router, usePage } from "@inertiajs/vue3";
import { Minus, Plus, Trash2, ShoppingCart } from "lucide-vue-next";
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
        <main class="bg-background">
            <div
                class="mx-auto w-full max-w-7xl px-5 py-6 sm:px-8 sm:py-8 lg:py-10"
            >
                <!-- ==================== Page Header ==================== -->
                <div class="mb-7">
                    <h1
                        class="text-2xl font-extrabold tracking-tight text-heading sm:text-3xl"
                    >
                        {{ t("myCart") }}
                    </h1>
                    <p class="mt-1.5 text-sm text-muted">
                        {{ t("reviewItems") }}
                    </p>
                </div>
                <!-- ==================== Empty Cart ==================== -->
                <div
                    v-if="!props.cart || !props.cart.items.length"
                    class="rounded-3xl border border-border bg-surface px-6 py-14 text-center shadow-sm"
                >
                    <div
                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"
                    >
                        <ShoppingCart :size="26" :stroke-width="1.8" />
                    </div>
                    <h2 class="mt-5 text-base font-bold text-heading">
                        {{ t("emptyCart") }}
                    </h2>
                    <p
                        class="mx-auto mt-1.5 max-w-sm text-sm leading-6 text-muted"
                    >
                        {{ t("cartEmptyMessage") }}
                    </p>
                    <div class="mx-auto mt-6 max-w-xs">
                        <Button
                            type="button"
                            variant="primary"
                            size="md"
                            fullWidth
                            @click="router.visit('/')"
                        >
                            {{ t("continueShopping") }}
                        </Button>
                    </div>
                </div>
                <!-- ==================== Cart Content ==================== -->
                <div
                    v-else
                    class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px] lg:items-start"
                >
                    <!-- ==================== Cart Items ==================== -->
                    <section class="space-y-4">
                        <div
                            v-for="item in props.cart.items"
                            :key="item.id"
                            class="rounded-2xl border border-border bg-surface p-4 shadow-sm transition-shadow duration-200 hover:shadow-md sm:p-5"
                        >
                            <!-- ==================== Product ==================== -->
                            <div class="flex gap-4">
                                <!-- ==================== Product Image ==================== -->
                                <div
                                    class="h-24 w-24 shrink-0 overflow-hidden rounded-2xl border border-border bg-background sm:h-28 sm:w-28"
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
                                        <ShoppingCart
                                            :size="20"
                                            :stroke-width="1.5"
                                        />
                                        <span class="text-[11px] font-medium">
                                            {{ t("noimg") }}
                                        </span>
                                    </div>
                                </div>
                                <!-- ==================== Product Information ==================== -->
                                <div class="min-w-0 flex-1">
                                    <div
                                        class="flex items-start justify-between gap-3"
                                    >
                                        <div class="min-w-0">
                                            <h2
                                                class="truncate text-sm font-bold text-heading sm:text-base"
                                            >
                                                {{ item.product.name }}
                                            </h2>
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
                                                    {{
                                                        t(
                                                            value.option_value
                                                                .value,
                                                        )
                                                    }}
                                                </span>
                                            </div>
                                            <!-- ==================== Price ==================== -->
                                            <p
                                                class="mt-2 text-sm font-bold text-heading"
                                            >
                                                {{
                                                    Number(
                                                        item.variant?.price ??
                                                            item.product.price,
                                                    ).toFixed(2)
                                                }}
                                            </p>
                                            <p
                                                v-if="item.product.unit"
                                                class="mt-0.5 text-xs font-medium text-muted"
                                            >
                                                {{ t("per") }}
                                                {{ item.product.unit }}
                                            </p>
                                        </div>
                                    </div>
                                    <!-- ==================== Stock Warning ==================== -->
                                    <div
                                        v-if="isOutOfStock(item)"
                                        class="mt-3 rounded-xl border border-red-100 bg-red-50 px-3 py-2 text-xs font-semibold text-red-600"
                                    >
                                        {{ t("noStuck") }}
                                    </div>
                                    <div
                                        v-else-if="hasInsufficientStock(item)"
                                        class="mt-3 rounded-xl border border-amber-100 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700"
                                    >
                                        {{ t("only") }}
                                        {{ getAvailableStock(item) }}
                                        {{ t("available") }}.
                                        {{ t("reduceQuantity") }}
                                    </div>
                                </div>
                            </div>
                            <!-- ==================== Item Controls ==================== -->
                            <div
                                class="mt-5 flex flex-wrap items-center justify-between gap-4 border-t border-border pt-4"
                            >
                                <!-- ==================== Quantity ==================== -->
                                <div
                                    class="inline-flex items-center overflow-hidden rounded-xl border border-border bg-surface shadow-sm"
                                >
                                    <button
                                        type="button"
                                        aria-label="Decrease quantity"
                                        class="flex h-10 w-10 items-center justify-center text-muted transition-colors duration-150 hover:bg-background hover:text-heading disabled:cursor-not-allowed disabled:opacity-40"
                                        :disabled="item.quantity <= 1"
                                        @click="
                                            updateQuantity(
                                                item.id,
                                                item.quantity - 1,
                                            )
                                        "
                                    >
                                        <Minus :size="17" :stroke-width="2" />
                                    </button>
                                    <span
                                        class="flex h-10 min-w-10 items-center justify-center border-x border-border px-2 text-sm font-bold text-heading"
                                    >
                                        {{ item.quantity }}
                                    </span>
                                    <button
                                        type="button"
                                        aria-label="Increase quantity"
                                        class="flex h-10 w-10 items-center justify-center text-muted transition-colors duration-150 hover:bg-background hover:text-heading disabled:cursor-not-allowed disabled:opacity-40"
                                        :disabled="
                                            isOutOfStock(item) ||
                                            item.quantity >=
                                                getAvailableStock(item)
                                        "
                                        @click="
                                            updateQuantity(
                                                item.id,
                                                item.quantity + 1,
                                            )
                                        "
                                    >
                                        <Plus :size="17" :stroke-width="2" />
                                    </button>
                                </div>
                                <!-- ==================== Subtotal ==================== -->
                                <div class="text-right">
                                    <p
                                        class="text-[11px] font-medium text-muted"
                                    >
                                        {{ t("subtotal") }}
                                    </p>
                                    <p
                                        class="mt-0.5 text-base font-extrabold tracking-tight text-heading sm:text-lg"
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
                            <!-- ==================== Remove ==================== -->
                            <div class="mt-3 flex justify-end">
                                <Button
                                    type="button"
                                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-500 transition-colors duration-150 hover:text-red-600"
                                    @click="removeItem(item.id)"
                                >
                                    <Trash2 :size="15" /> {{ t("remove") }}
                                </Button>
                            </div>
                        </div>
                    </section>
                    <!-- ==================== Cart Summary ==================== -->
                    <aside
                        class="rounded-2xl border border-border bg-surface p-5 shadow-sm lg:sticky lg:top-6"
                    >
                        <h2 class="text-base font-bold text-heading">
                            {{ t("orderSummary") }}
                        </h2>
                        <div class="mt-5 border-t border-border pt-4">
                            <div
                                class="flex items-center justify-between gap-4"
                            >
                                <span class="text-sm font-medium text-muted">
                                    {{ t("total") }}
                                </span>
                                <span
                                    class="text-xl font-extrabold tracking-tight text-heading"
                                >
                                    {{ cartTotal.toFixed(2) }}
                                </span>
                            </div>
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
                                {{ t("checkout") }}
                            </Button>
                        </div>
                        <p
                            class="mt-3 text-center text-xs leading-5 text-muted"
                        >
                            {{ t("reviewBeforeContinuing") }}
                        </p>
                    </aside>
                </div>
            </div>
        </main>
    </CustomerLayout>
</template>
