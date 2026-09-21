<script setup lang="ts">
// ==================== Imports ====================

import { computed, ref } from "vue";
import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import Button from "@/Components/UI/Button.vue";
import { router } from "@inertiajs/vue3";
import {
    ChevronLeft,
    ChevronRight,
    Heart,
    Minus,
    Plus,
    ShoppingCart,
} from "lucide-vue-next";

// ==================== Types ====================

interface ProductImage {
    id: number;
    image: string;
}

interface OptionValue {
    id: number;
    value: string;
}

interface ProductOption {
    id: number;
    name: string;
    values: OptionValue[];
}

interface VariantValue {
    id: number;
    product_option_id: number;
    product_option_value_id: number;
    option_value: OptionValue;
}

interface ProductVariant {
    id: number;
    sku: string | null;
    price: number;
    stock: number;
    variant_values: VariantValue[];
}

interface Seller {
    id: number;
    first_name: string;
    last_name: string;
}

interface Product {
    id: number;
    name: string;
    description: string | null;
    price: number;
    stock: number;
    unit: string | null;
    images: ProductImage[];
    options: ProductOption[];
    variants: ProductVariant[];
    seller: Seller;
}

// ==================== Props ====================

const props = defineProps<{
    product: Product;
}>();

// ==================== State ====================

const quantity = ref(1);
const selectedValues = ref<Record<number, number>>({});

// ==================== Image Slider ====================

const currentImageIndex = ref(0);

const nextImage = () => {
    if (currentImageIndex.value < props.product.images.length - 1) {
        currentImageIndex.value++;
    }
};

const previousImage = () => {
    if (currentImageIndex.value > 0) {
        currentImageIndex.value--;
    }
};

// ==================== Image Swipe ====================

const touchStartX = ref(0);
const touchCurrentX = ref(0);
const isDragging = ref(false);

const handleTouchStart = (event: TouchEvent) => {
    touchStartX.value = event.touches[0].clientX;
    touchCurrentX.value = touchStartX.value;
    isDragging.value = true;
};

const handleTouchMove = (event: TouchEvent) => {
    if (!isDragging.value) {
        return;
    }

    touchCurrentX.value = event.touches[0].clientX;
};

const handleTouchEnd = () => {
    if (!isDragging.value) {
        return;
    }

    const difference = touchStartX.value - touchCurrentX.value;

    isDragging.value = false;

    // ==================== Minimum Swipe Distance ====================

    if (Math.abs(difference) < 50) {
        return;
    }

    if (difference > 0) {
        nextImage();
    } else {
        previousImage();
    }
};

// ==================== Image Translation ====================

const imageTranslation = computed(() => {
    const baseTranslation = -currentImageIndex.value * 100;

    if (!isDragging.value) {
        return `${baseTranslation}%`;
    }

    const difference = touchCurrentX.value - touchStartX.value;

    return `calc(${baseTranslation}% + ${difference}px)`;
});
// ==================== Selected Variant ====================

const selectedVariant = computed(() => {
    if (!props.product.variants.length) {
        return null;
    }

    const selectedIds = Object.values(selectedValues.value);

    if (selectedIds.length !== props.product.options.length) {
        return null;
    }

    return (
        props.product.variants.find((variant) => {
            const variantIds = variant.variant_values.map(
                (value) => value.product_option_value_id,
            );

            return (
                variantIds.length === selectedIds.length &&
                variantIds.every((id) => selectedIds.includes(id))
            );
        }) ?? null
    );
});

// ==================== Variant Availability ====================

const isValueAvailable = (optionId: number, valueId: number): boolean => {
    if (!props.product.variants.length) {
        return true;
    }

    return props.product.variants.some((variant) => {
        if (variant.stock <= 0) {
            return false;
        }

        const hasSelectedValue = variant.variant_values.some(
            (variantValue) =>
                variantValue.product_option_id === optionId &&
                variantValue.product_option_value_id === valueId,
        );

        if (!hasSelectedValue) {
            return false;
        }

        return Object.entries(selectedValues.value).every(
            ([selectedOptionId, selectedValueId]) => {
                if (Number(selectedOptionId) === optionId) {
                    return true;
                }

                return variant.variant_values.some(
                    (variantValue) =>
                        variantValue.product_option_id ===
                            Number(selectedOptionId) &&
                        variantValue.product_option_value_id ===
                            selectedValueId,
                );
            },
        );
    });
};

// ==================== Current Price ====================

const currentPrice = computed(() => {
    return selectedVariant.value
        ? Number(selectedVariant.value.price)
        : Number(props.product.price);
});

// ==================== Current Stock ====================

const currentStock = computed(() => {
    return selectedVariant.value
        ? selectedVariant.value.stock
        : props.product.stock;
});

// ==================== Quantity ====================

const increaseQuantity = () => {
    if (quantity.value < currentStock.value) {
        quantity.value++;
    }
};

const decreaseQuantity = () => {
    if (quantity.value > 1) {
        quantity.value--;
    }
};

// ==================== Add To Cart ====================

const addToCart = () => {
    router.post("/cart/items", {
        product_id: props.product.id,
        variant_id: selectedVariant.value?.id ?? null,
        quantity: quantity.value,
    });
};
</script>

<template>
    <CustomerLayout>
        <!-- ==================== Product Details ==================== -->

        <main class="bg-background">
            <div
                class="mx-auto w-full max-w-7xl px-5 py-6 sm:px-8 sm:py-8 lg:py-10"
            >
                <!-- ==================== Product Layout ==================== -->

                <div
                    class="grid gap-8 lg:grid-cols-[minmax(0,1.05fr)_minmax(0,0.95fr)] lg:gap-12"
                >
                    <!-- ==================== Product Gallery ==================== -->

                    <section>
                        <div
                            class="relative overflow-hidden rounded-3xl border border-border bg-surface shadow-sm"
                        >
                            <!-- ==================== Image Slider ==================== -->

                            <div
                                class="relative aspect-square overflow-hidden bg-background"
                                @touchstart="handleTouchStart"
                                @touchmove="handleTouchMove"
                                @touchend="handleTouchEnd"
                            >
                                <!-- ==================== Images ==================== -->

                                <div
                                    v-if="product.images.length"
                                    class="flex h-full w-full"
                                    :class="{
                                        'transition-transform duration-300 ease-out':
                                            !isDragging,
                                    }"
                                    :style="{
                                        transform: `translateX(${imageTranslation})`,
                                    }"
                                >
                                    <div
                                        v-for="image in product.images"
                                        :key="image.id"
                                        class="h-full w-full min-w-full max-w-full shrink-0 grow-0 basis-full overflow-hidden"
                                    >
                                        <img
                                            :src="`/storage/${image.image}`"
                                            :alt="product.name"
                                            class="h-full w-full object-contain"
                                        />
                                    </div>
                                </div>

                                <!-- ==================== Empty Image State ==================== -->

                                <div
                                    v-else
                                    class="flex h-full flex-col items-center justify-center gap-2 text-muted"
                                >
                                    <ShoppingCart
                                        :size="30"
                                        :stroke-width="1.5"
                                    />

                                    <span class="text-sm font-medium">
                                        No image available
                                    </span>
                                </div>

                                <!-- ==================== Previous Image ==================== -->

                                <button
                                    v-if="product.images.length > 1"
                                    type="button"
                                    aria-label="Previous image"
                                    :disabled="currentImageIndex === 0"
                                    class="absolute left-3 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-white/60 bg-white/90 text-body shadow-md backdrop-blur-sm transition-all duration-200 hover:scale-105 hover:bg-white disabled:cursor-not-allowed disabled:opacity-30"
                                    @click="previousImage"
                                >
                                    <ChevronLeft :size="20" :stroke-width="2" />
                                </button>

                                <!-- ==================== Next Image ==================== -->

                                <button
                                    v-if="product.images.length > 1"
                                    type="button"
                                    aria-label="Next image"
                                    :disabled="
                                        currentImageIndex ===
                                        product.images.length - 1
                                    "
                                    class="absolute right-3 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-white/60 bg-white/90 text-body shadow-md backdrop-blur-sm transition-all duration-200 hover:scale-105 hover:bg-white disabled:cursor-not-allowed disabled:opacity-30"
                                    @click="nextImage"
                                >
                                    <ChevronRight
                                        :size="20"
                                        :stroke-width="2"
                                    />
                                </button>

                                <!-- ==================== Image Counter ==================== -->

                                <div
                                    v-if="product.images.length > 1"
                                    class="absolute bottom-4 right-4 rounded-full border border-white/20 bg-slate-950/65 px-3 py-1.5 text-xs font-semibold text-white shadow-sm backdrop-blur-sm"
                                >
                                    {{ currentImageIndex + 1 }} /
                                    {{ product.images.length }}
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- ==================== Product Information ==================== -->

                    <section class="flex flex-col">
                        <!-- ==================== Product Header ==================== -->

                        <div
                            class="flex items-start justify-between gap-4 border-b border-border pb-5"
                        >
                            <div class="min-w-0">
                                <h1
                                    class="text-2xl font-extrabold tracking-tight text-heading sm:text-3xl"
                                >
                                    {{ product.name }}
                                </h1>

                                <p class="mt-2 text-sm text-muted">
                                    Sold by
                                    <span class="font-semibold text-body">
                                        {{ product.seller.first_name }}
                                        {{ product.seller.last_name }}
                                    </span>
                                </p>
                            </div>

                            <!-- ==================== Favorite ==================== -->

                            <button
                                type="button"
                                aria-label="Add to favorites"
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-border bg-surface text-muted shadow-sm transition-all duration-200 hover:border-red-100 hover:bg-red-50 hover:text-red-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 sm:h-11 sm:w-11"
                            >
                                <Heart :size="20" :stroke-width="2" />
                            </button>
                        </div>

                        <!-- ==================== Price ==================== -->

                        <div class="mt-6">
                            <div
                                class="text-3xl font-extrabold tracking-tight text-heading sm:text-4xl"
                            >
                                {{ currentPrice.toFixed(2) }}
                            </div>

                            <p
                                v-if="product.unit"
                                class="mt-1 text-sm font-medium text-muted"
                            >
                                Per {{ product.unit }}
                            </p>
                        </div>

                        <!-- ==================== Description ==================== -->

                        <div
                            v-if="product.description"
                            class="mt-6 rounded-2xl border border-border bg-surface p-4 sm:p-5"
                        >
                            <h2 class="text-sm font-bold text-heading">
                                Description
                            </h2>

                            <p class="mt-2 text-sm leading-6 text-body">
                                {{ product.description }}
                            </p>
                        </div>

                        <!-- ==================== Product Options ==================== -->

                        <div
                            v-for="option in product.options"
                            :key="option.id"
                            class="mt-6"
                        >
                            <div
                                class="flex items-center justify-between gap-3"
                            >
                                <h2 class="text-sm font-bold text-heading">
                                    {{ option.name }}
                                </h2>

                                <span
                                    v-if="selectedValues[option.id]"
                                    class="text-xs font-medium text-muted"
                                >
                                    Selected
                                </span>
                            </div>

                            <div class="mt-3 flex flex-wrap gap-2">
                                <button
                                    v-for="value in option.values"
                                    :key="value.id"
                                    type="button"
                                    :disabled="
                                        !isValueAvailable(option.id, value.id)
                                    "
                                    class="rounded-xl border px-4 py-2.5 text-sm font-semibold transition-all duration-200 disabled:cursor-not-allowed disabled:border-border disabled:bg-background disabled:text-muted disabled:opacity-50"
                                    :class="
                                        selectedValues[option.id] === value.id
                                            ? 'border-brand-600 bg-brand-600 text-white shadow-sm shadow-brand-600/20'
                                            : 'border-border bg-surface text-body hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700'
                                    "
                                    @click="
                                        selectedValues[option.id] = value.id;
                                        quantity = 1;
                                    "
                                >
                                    {{ value.value }}
                                </button>
                            </div>
                        </div>

                        <!-- ==================== Stock ==================== -->

                        <div class="mt-6">
                            <div class="flex items-center gap-2 text-sm">
                                <span
                                    class="h-2 w-2 rounded-full"
                                    :class="
                                        currentStock > 0
                                            ? 'bg-emerald-500'
                                            : 'bg-red-500'
                                    "
                                ></span>

                                <span
                                    class="font-semibold"
                                    :class="
                                        currentStock > 0
                                            ? 'text-emerald-700'
                                            : 'text-red-600'
                                    "
                                >
                                    {{
                                        currentStock > 0
                                            ? `${currentStock} available`
                                            : "Out of stock"
                                    }}
                                </span>
                            </div>
                        </div>

                        <!-- ==================== Quantity ==================== -->

                        <div class="mt-6">
                            <h2 class="text-sm font-bold text-heading">
                                Quantity
                            </h2>

                            <div
                                class="mt-3 inline-flex items-center overflow-hidden rounded-xl border border-border bg-surface shadow-sm"
                            >
                                <button
                                    type="button"
                                    aria-label="Decrease quantity"
                                    class="flex h-11 w-11 items-center justify-center text-muted transition-colors duration-150 hover:bg-background hover:text-heading disabled:cursor-not-allowed disabled:opacity-40"
                                    :disabled="quantity <= 1"
                                    @click="decreaseQuantity"
                                >
                                    <Minus :size="17" :stroke-width="2" />
                                </button>

                                <span
                                    class="flex h-11 min-w-12 items-center justify-center border-x border-border px-3 text-sm font-bold text-heading"
                                >
                                    {{ quantity }}
                                </span>

                                <button
                                    type="button"
                                    aria-label="Increase quantity"
                                    class="flex h-11 w-11 items-center justify-center text-muted transition-colors duration-150 hover:bg-background hover:text-heading disabled:cursor-not-allowed disabled:opacity-40"
                                    :disabled="quantity >= currentStock"
                                    @click="increaseQuantity"
                                >
                                    <Plus :size="17" :stroke-width="2" />
                                </button>
                            </div>
                        </div>

                        <!-- ==================== Add To Cart ==================== -->

                        <div class="mt-8 border-t border-border pt-6">
                            <Button
                                type="button"
                                variant="primary"
                                size="lg"
                                fullWidth
                                :disabled="
                                    currentStock <= 0 ||
                                    (product.variants.length > 0 &&
                                        !selectedVariant)
                                "
                                @click="addToCart"
                            >
                                <ShoppingCart :size="20" :stroke-width="2" />

                                {{
                                    currentStock <= 0
                                        ? "Out of Stock"
                                        : product.variants.length > 0 &&
                                            !selectedVariant
                                          ? "Select Options"
                                          : "Add to Cart"
                                }}
                            </Button>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </CustomerLayout>
</template>
