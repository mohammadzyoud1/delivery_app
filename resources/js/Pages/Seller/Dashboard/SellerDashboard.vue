<script setup lang="ts">
import SellerLayout from "@/Layouts/SellerLayout.vue";
import { Link } from "@inertiajs/vue3";
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

            <div class="mb-6">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                    Seller Dashboard
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Manage your store and products.
                </p>
            </div>

            <!-- ==================== Dashboard Cards ==================== -->

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                <Link
                    href="/seller/products"
                    class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <p class="text-sm text-gray-500">My Products</p>

                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        {{ allProductCount }}
                    </p>
                </Link>

                <Link
                    href="/seller/products?status=active"
                    class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <p class="text-sm text-gray-500">Active Products</p>

                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        {{ activeProductCount }}
                    </p>
                </Link>

                <Link
                    href="/seller/orders"
                    class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <p class="text-sm text-gray-500">Orders</p>

                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        {{ orderCount }}
                    </p>
                </Link>

                <Link
                    href="/seller/products-sold"
                    class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <p class="text-sm text-gray-500">Sold Products</p>

                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        {{ productsSold }}
                    </p>
                </Link>

                <Link
                    href="/seller/revenue"
                    class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <p class="text-sm text-gray-500">Revenue</p>

                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        {{ revenue }}
                    </p>
                </Link>
            </div>
        </div>
    </SellerLayout>
</template>
