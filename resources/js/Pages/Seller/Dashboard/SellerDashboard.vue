<script setup lang="ts">
// ==================== Imports ====================

import SellerLayout from "@/Layouts/SellerLayout.vue";
import { Link } from "@inertiajs/vue3";
import {
    Boxes,
    CircleDollarSign,
    PackageCheck,
    ShoppingBag,
    Store,
} from "lucide-vue-next";

// ==================== Props ====================

defineProps<{
    allProductCount: number;
    activeProductCount: number;
    orderCount: number;
    productsSold: number;
    revenue: number;
}>();
</script>

<template>
    <SellerLayout>
        <div class="mx-auto w-full max-w-7xl">
            <!-- ==================== Page Header ==================== -->

            <section class="mb-8">
                <div
                    class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
                >
                    <div>
                        <p
                            class="text-sm font-bold uppercase tracking-wider text-brand-600"
                        >
                            Seller Center
                        </p>

                        <h1
                            class="mt-1 text-2xl font-extrabold tracking-tight text-heading sm:text-3xl"
                        >
                            Seller Dashboard
                        </h1>

                        <p class="mt-2 max-w-xl text-sm leading-6 text-muted">
                            Manage your products, orders, sales, and store
                            performance from one place.
                        </p>
                    </div>

                    <!-- ==================== Store Link ==================== -->

                    <Link
                        href="/seller/products"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-border bg-surface px-4 py-2.5 text-sm font-bold text-heading shadow-sm transition-all duration-200 hover:border-slate-300 hover:bg-background hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 sm:w-auto"
                    >
                        <Store :size="17" :stroke-width="2" />
                        Manage Products
                    </Link>
                </div>
            </section>

            <!-- ==================== Dashboard Cards ==================== -->

            <section>
                <div
                    class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <!-- ==================== My Products ==================== -->

                    <Link
                        href="/seller/products"
                        class="group rounded-2xl border border-border bg-surface p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-lg hover:shadow-slate-900/5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold text-muted">
                                    My Products
                                </p>

                                <p
                                    class="mt-2 text-3xl font-extrabold tracking-tight text-heading"
                                >
                                    {{ allProductCount }}
                                </p>

                                <p class="mt-1 text-xs font-medium text-muted">
                                    Total products
                                </p>
                            </div>

                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600 transition-colors group-hover:bg-brand-100"
                            >
                                <Boxes :size="21" :stroke-width="2" />
                            </div>
                        </div>
                    </Link>

                    <!-- ==================== Active Products ==================== -->

                    <Link
                        href="/seller/products?status=active"
                        class="group rounded-2xl border border-border bg-surface p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-lg hover:shadow-slate-900/5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold text-muted">
                                    Active Products
                                </p>

                                <p
                                    class="mt-2 text-3xl font-extrabold tracking-tight text-heading"
                                >
                                    {{ activeProductCount }}
                                </p>

                                <p class="mt-1 text-xs font-medium text-muted">
                                    Currently available
                                </p>
                            </div>

                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-green-50 text-green-600 transition-colors group-hover:bg-green-100"
                            >
                                <PackageCheck :size="21" :stroke-width="2" />
                            </div>
                        </div>
                    </Link>

                    <!-- ==================== Orders ==================== -->

                    <Link
                        href="/seller/orders"
                        class="group rounded-2xl border border-border bg-surface p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-lg hover:shadow-slate-900/5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold text-muted">
                                    Orders
                                </p>

                                <p
                                    class="mt-2 text-3xl font-extrabold tracking-tight text-heading"
                                >
                                    {{ orderCount }}
                                </p>

                                <p class="mt-1 text-xs font-medium text-muted">
                                    Orders containing your products
                                </p>
                            </div>

                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600 transition-colors group-hover:bg-violet-100"
                            >
                                <ShoppingBag :size="21" :stroke-width="2" />
                            </div>
                        </div>
                    </Link>

                    <!-- ==================== Products Sold ==================== -->

                    <Link
                        href="/seller/products-sold"
                        class="group rounded-2xl border border-border bg-surface p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-lg hover:shadow-slate-900/5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold text-muted">
                                    Sold Products
                                </p>

                                <p
                                    class="mt-2 text-3xl font-extrabold tracking-tight text-heading"
                                >
                                    {{ productsSold }}
                                </p>

                                <p class="mt-1 text-xs font-medium text-muted">
                                    Total units sold
                                </p>
                            </div>

                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-orange-50 text-orange-600 transition-colors group-hover:bg-orange-100"
                            >
                                <PackageCheck :size="21" :stroke-width="2" />
                            </div>
                        </div>
                    </Link>

                    <!-- ==================== Revenue ==================== -->

                    <Link
                        href="/seller/revenue"
                        class="group rounded-2xl border border-border bg-surface p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-lg hover:shadow-slate-900/5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 lg:col-span-2"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold text-muted">
                                    Revenue
                                </p>

                                <p
                                    class="mt-2 text-3xl font-extrabold tracking-tight text-heading"
                                >
                                    {{ revenue }}
                                </p>

                                <p class="mt-1 text-xs font-medium text-muted">
                                    Total seller revenue
                                </p>
                            </div>

                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 transition-colors group-hover:bg-emerald-100"
                            >
                                <CircleDollarSign
                                    :size="21"
                                    :stroke-width="2"
                                />
                            </div>
                        </div>
                    </Link>
                </div>
            </section>
        </div>
    </SellerLayout>
</template>
