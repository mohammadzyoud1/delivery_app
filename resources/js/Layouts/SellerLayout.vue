<script setup lang="ts">
// ==================== Imports ====================

import { Link } from "@inertiajs/vue3";
import { LayoutDashboard, Package, Plus, User } from "lucide-vue-next";
</script>

<template>
    <!-- ==================== Seller Layout ==================== -->

    <div class="min-h-screen bg-gray-50">
        <!-- ==================== Header ==================== -->

        <header class="border-b border-gray-100 bg-white">
            <div
                class="mx-auto flex h-16 w-full max-w-7xl items-center justify-between px-5 sm:px-8"
            >
                <h1 class="text-xl font-bold tracking-tight text-gray-900">
                    Seller Dashboard
                </h1>

                <Link
                    href="/profile"
                    aria-label="Open profile"
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-700 transition hover:bg-gray-200"
                >
                    <User :size="20" />
                </Link>
            </div>
        </header>

        <!-- ==================== Page Content ==================== -->

        <main class="pb-20">
            <div class="mx-auto w-full max-w-7xl px-5 py-6 sm:px-8">
                <slot />
            </div>
        </main>

        <!-- ==================== Seller Navigation ==================== -->

        <nav
            class="fixed inset-x-0 bottom-0 z-50 border-t border-gray-100 bg-white"
        >
            <div
                class="mx-auto flex h-16 max-w-7xl items-center justify-around px-2 sm:px-8"
            >
                <Link
                    href="/seller"
                    class="flex flex-col items-center gap-1 text-gray-500 hover:text-gray-900"
                >
                    <LayoutDashboard :size="20" />
                    <span class="text-[11px] font-medium"> Dashboard </span>
                </Link>

                <Link
                    href="/seller/products"
                    class="flex flex-col items-center gap-1 text-gray-500 hover:text-gray-900"
                >
                    <Package :size="20" />
                    <span class="text-[11px] font-medium"> Products </span>
                </Link>

                <Link
                    href="/seller/products/create"
                    class="flex flex-col items-center gap-1 text-gray-500 hover:text-gray-900"
                >
                    <Plus :size="20" />
                    <span class="text-[11px] font-medium"> Add Product </span>
                </Link>
            </div>
        </nav>
    </div>
</template>
