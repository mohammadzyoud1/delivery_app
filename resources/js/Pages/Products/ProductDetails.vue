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
    if (!isDragging.value) {
        return `-${currentImageIndex.value * 100}%`;
    }

    const difference = touchCurrentX.value - touchStartX.value;

    return `calc(-${currentImageIndex.value * 100}% + ${difference}px)`;
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
    // No variants means every option value is available.
    if (!props.product.variants.length) {
        return true;
    }

    return props.product.variants.some((variant) => {
        // Ignore variants that are completely out of stock.
        if (variant.stock <= 0) {
            return false;
        }

        // Check whether this variant contains the clicked value.
        const hasSelectedValue = variant.variant_values.some(
            (variantValue) =>
                variantValue.product_option_id === optionId &&
                variantValue.product_option_value_id === valueId,
        );

        if (!hasSelectedValue) {
            return false;
        }

        // Check the other already-selected options.
        return Object.entries(selectedValues.value).every(
            ([selectedOptionId, selectedValueId]) => {
                // Ignore the option currently being displayed.
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

        <div class="mx-auto max-w-6xl">
            <!-- ==================== Product Content ==================== -->

            <div class="grid gap-8 lg:grid-cols-2">
                <!-- ==================== Product Image Slider ==================== -->

                <div class="relative">
                    <div
                        class="relative aspect-square overflow-hidden rounded-2xl bg-gray-100"
                        @touchstart="handleTouchStart"
                        @touchmove="handleTouchMove"
                        @touchend="handleTouchEnd"
                    >
                        <!-- ==================== Images ==================== -->

                        <div
                            v-if="product.images.length"
                            class="flex h-full"
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
                                class="h-full min-w-full shrink-0"
                            >
                                <img
                                    :src="`/storage/${image.image}`"
                                    :alt="product.name"
                                    class="h-full w-full object-cover"
                                />
                            </div>
                        </div>

                        <div
                            v-else
                            class="flex h-full items-center justify-center text-gray-400"
                        >
                            No image
                        </div>

                        <!-- ==================== Previous Image ==================== -->

                        <button
                            v-if="product.images.length > 1"
                            type="button"
                            aria-label="Previous image"
                            :disabled="currentImageIndex === 0"
                            class="absolute left-3 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-gray-800 shadow-sm transition hover:bg-white disabled:cursor-not-allowed disabled:opacity-40"
                            @click="previousImage"
                        >
                            <ChevronLeft :size="20" />
                        </button>

                        <!-- ==================== Next Image ==================== -->

                        <button
                            v-if="product.images.length > 1"
                            type="button"
                            aria-label="Next image"
                            :disabled="
                                currentImageIndex === product.images.length - 1
                            "
                            class="absolute right-3 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-gray-800 shadow-sm transition hover:bg-white disabled:cursor-not-allowed disabled:opacity-40"
                            @click="nextImage"
                        >
                            <ChevronRight :size="20" />
                        </button>

                        <!-- ==================== Image Counter ==================== -->

                        <div
                            v-if="product.images.length > 1"
                            class="absolute bottom-3 right-3 rounded-full bg-black/60 px-3 py-1 text-xs font-medium text-white backdrop-blur"
                        >
                            {{ currentImageIndex + 1 }}/{{
                                product.images.length
                            }}
                        </div>
                    </div>
                </div>

                <!-- ==================== Product Information ==================== -->

                <div class="flex flex-col">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h1
                                class="text-2xl font-bold text-gray-900 sm:text-3xl"
                            >
                                {{ product.name }}
                            </h1>

                            <p class="mt-2 text-sm text-gray-500">
                                Sold by
                                {{ product.seller.first_name }}
                                {{ product.seller.last_name }}
                            </p>
                        </div>

                        <!-- ==================== Favorite ==================== -->

                        <button
                            type="button"
                            aria-label="Add to favorites"
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-gray-100 text-gray-700 transition hover:bg-gray-200 hover:text-red-500"
                        >
                            <Heart :size="21" />
                        </button>
                    </div>

                    <!-- ==================== Price ==================== -->

                    <p class="mt-6 text-2xl font-bold text-gray-900">
                        {{ currentPrice.toFixed(2) }}
                    </p>

                    <p v-if="product.unit" class="mt-1 text-sm text-gray-500">
                        Per {{ product.unit }}
                    </p>

                    <!-- ==================== Description ==================== -->

                    <p
                        v-if="product.description"
                        class="mt-6 text-sm leading-6 text-gray-600"
                    >
                        {{ product.description }}
                    </p>

                    <!-- ==================== Product Options ==================== -->

                    <div
                        v-for="option in product.options"
                        :key="option.id"
                        class="mt-6"
                    >
                        <h2 class="text-sm font-semibold text-gray-900">
                            {{ option.name }}
                        </h2>

                        <div class="mt-3 flex flex-wrap gap-2">
                            <button
                                v-for="value in option.values"
                                :key="value.id"
                                type="button"
                                :disabled="
                                    !isValueAvailable(option.id, value.id)
                                "
                                class="rounded-xl border px-4 py-2 text-sm font-medium transition disabled:cursor-not-allowed disabled:opacity-40"
                                :class="
                                    selectedValues[option.id] === value.id
                                        ? 'border-gray-900 bg-gray-900 text-white'
                                        : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'
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

                    <!-- ==================== Quantity ==================== -->

                    <div class="mt-6">
                        <h2 class="text-sm font-semibold text-gray-900">
                            Quantity
                        </h2>

                        <div
                            class="mt-3 inline-flex items-center rounded-xl border border-gray-200"
                        >
                            <button
                                type="button"
                                class="p-3 text-gray-600 hover:text-gray-900"
                                :disabled="quantity <= 1"
                                @click="decreaseQuantity"
                            >
                                <Minus :size="18" />
                            </button>

                            <span
                                class="min-w-10 text-center text-sm font-semibold"
                            >
                                {{ quantity }}
                            </span>

                            <button
                                type="button"
                                class="p-3 text-gray-600 hover:text-gray-900"
                                :disabled="quantity >= currentStock"
                                @click="increaseQuantity"
                            >
                                <Plus :size="18" />
                            </button>
                        </div>
                    </div>

                    <!-- ==================== Add To Cart ==================== -->

                    <div class="mt-8">
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
                            <ShoppingCart :size="20" />

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
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>
