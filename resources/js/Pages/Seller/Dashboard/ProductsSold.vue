<script setup lang="ts">
import { computed } from "vue";

import SellerLayout from "@/Layouts/SellerLayout.vue";
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

interface Variant {
    id: number;
    sku: string | null;
}

interface Order {
    id: number;
    status: string;
    created_at: string;
}

interface SoldItem {
    id: number;
    product_id: number;
    quantity: number;
    unit_price: number;
    subtotal: number;
    product: Product;
    variant: Variant | null;
    order: Order;
}

// ==================== Props ====================

const props = defineProps<{
    soldItems: SoldItem[];
}>();

// ==================== Group Products ====================

const soldProducts = computed(() => {
    const grouped = new Map<
        number,
        {
            product: Product;
            quantity: number;
            revenue: number;
        }
    >();

    props.soldItems.forEach((item) => {
        if (!grouped.has(item.product_id)) {
            grouped.set(item.product_id, {
                product: item.product,
                quantity: 0,
                revenue: 0,
            });
        }

        const currentProduct = grouped.get(item.product_id)!;

        currentProduct.quantity += Number(item.quantity);
        currentProduct.revenue += Number(item.subtotal);
    });

    return Array.from(grouped.values());
});

// ==================== Total Sold ====================

const totalSold = computed(() => {
    return props.soldItems.reduce((total, item) => {
        return total + Number(item.quantity);
    }, 0);
});

// ==================== Total Revenue ====================

const totalRevenue = computed(() => {
    return props.soldItems.reduce((total, item) => {
        return total + Number(item.subtotal);
    }, 0);
});

// ==================== Helpers ====================

const formatPrice = (price: number) => {
    return Number(price).toFixed(2);
};
</script>

<template>
    <SellerLayout>
        <div class="mx-auto w-full max-w-7xl">
            <!-- ==================== Page Header ==================== -->

            <section class="mb-8">
                <h1
                    class="mt-1 text-2xl font-extrabold tracking-tight text-heading sm:text-3xl"
                >
                    {{ t("productsSold") }}
                </h1>

                <p class="mt-2 text-sm leading-6 text-muted">
                    {{ t("productsSoldDescription") }}
                </p>
            </section>

            <!-- ==================== Summary ==================== -->

            <section class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- ==================== Total Products Sold ==================== -->

                <div
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p
                                class="text-xs font-bold uppercase tracking-wider text-muted"
                            >
                                {{ t("productsSold") }}
                            </p>

                            <p
                                class="mt-2 text-3xl font-extrabold tracking-tight text-heading"
                            >
                                {{ totalSold }}
                            </p>

                            <p class="mt-1 text-xs text-muted">
                                {{ t("successfullyDeliveredProducts") }}
                            </p>
                        </div>

                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="21"
                                height="21"
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
                    </div>
                </div>

                <!-- ==================== Total Revenue ==================== -->

                <div
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p
                                class="text-xs font-bold uppercase tracking-wider text-muted"
                            >
                                {{ t("revenue") }}
                            </p>

                            <p
                                class="mt-2 text-3xl font-extrabold tracking-tight text-heading"
                            >
                                {{ formatPrice(totalRevenue) }}
                            </p>

                            <p class="mt-1 text-xs text-muted">
                                {{ t("revenueFromDeliveredSales") }}
                            </p>
                        </div>

                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="21"
                                height="21"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <circle cx="12" cy="12" r="9" />
                                <path
                                    d="M16 8h-6a2 2 0 0 0 0 4h4a2 2 0 0 1 0 4H8"
                                />
                                <path d="M12 6v2" />
                                <path d="M12 16v2" />
                            </svg>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ==================== Empty State ==================== -->

            <section
                v-if="!soldProducts.length"
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
                    {{ t("noProductsSoldYet") }}
                </h2>

                <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-muted">
                    {{ t("soldProductsAppear") }}
                </p>
            </section>

            <!-- ==================== Sold Products ==================== -->

            <section
                v-else
                class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <article
                    v-for="item in soldProducts"
                    :key="item.product.id"
                    class="group overflow-hidden rounded-2xl border border-border bg-surface shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md"
                >
                    <!-- ==================== Product Image ==================== -->

                    <div
                        class="relative aspect-square overflow-hidden bg-background"
                    >
                        <img
                            v-if="item.product.images.length"
                            :src="`/storage/${item.product.images[0].image}`"
                            :alt="item.product.name"
                            class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                        />

                        <div
                            v-else
                            class="flex h-full flex-col items-center justify-center text-muted"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="28"
                                height="28"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <rect
                                    width="18"
                                    height="18"
                                    x="3"
                                    y="3"
                                    rx="2"
                                />
                                <circle cx="8.5" cy="8.5" r="1.5" />
                                <path d="m21 15-5-5L5 21" />
                            </svg>

                            <span class="mt-2 text-xs font-medium">
                                {{ t("noimg") }}
                            </span>
                        </div>

                        <!-- ==================== Sold Badge ==================== -->

                        <span
                            class="absolute left-3 top-3 rounded-full border border-green-200 bg-green-50 px-3 py-1 text-[11px] font-bold text-green-700 shadow-sm"
                        >
                            {{ t("sold") }}
                        </span>
                    </div>

                    <!-- ==================== Product Information ==================== -->

                    <div class="p-5">
                        <h2
                            class="truncate text-sm font-extrabold text-heading"
                            :title="item.product.name"
                        >
                            {{ item.product.name }}
                        </h2>

                        <div class="mt-5 space-y-3">
                            <!-- ==================== Quantity ==================== -->

                            <div
                                class="flex items-center justify-between gap-4"
                            >
                                <span class="text-sm text-muted">
                                    {{ t("quantitySold") }}
                                </span>

                                <span
                                    class="text-sm font-extrabold text-heading"
                                >
                                    {{ item.quantity }}
                                </span>
                            </div>

                            <!-- ==================== Revenue ==================== -->

                            <div
                                class="flex items-center justify-between gap-4 border-t border-border pt-3"
                            >
                                <span class="text-sm text-muted">
                                    {{ t("revenue") }}
                                </span>

                                <span
                                    class="text-base font-extrabold text-brand-700"
                                >
                                    {{ formatPrice(item.revenue) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </article>
            </section>
        </div>
    </SellerLayout>
</template>
