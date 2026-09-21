<script setup lang="ts">
// ==================== Imports ====================
import AppHeader from "@/Components/Navigation/AppHeader.vue";

import { Link, usePage } from "@inertiajs/vue3";

import {
    ArrowLeft,
    LayoutDashboard,
    Package,
    Plus,
    User,
} from "lucide-vue-next";

// ==================== Page ====================

const page = usePage<{
    flash: {
        success?: string;
        error?: string;
    };
}>();

// ==================== Navigation Helper ====================

const isActive = (path: string) => {
    return page.url === path || page.url.startsWith(`${path}/`);
};
</script>

<template>
    <!-- ==================== Seller Layout ==================== -->

    <div class="min-h-screen bg-background">
        <!-- ==================== Header ==================== -->

        <AppHeader />

        <!-- ==================== Flash Messages ==================== -->

        <div class="mx-auto w-full max-w-7xl px-5 pt-4 sm:px-8">
            <div
                v-if="page.props.flash?.success"
                class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700"
            >
                {{ page.props.flash.success }}
            </div>

            <div
                v-if="page.props.flash?.error"
                class="mt-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700"
            >
                {{ page.props.flash.error }}
            </div>
        </div>

        <!-- ==================== Page Content ==================== -->

        <main class="pb-24">
            <div class="mx-auto w-full max-w-7xl px-5 py-6 sm:px-8 sm:py-8">
                <slot />
            </div>
        </main>

        <!-- ==================== Seller Navigation ==================== -->

        <nav
            class="fixed inset-x-0 bottom-0 z-50 border-t border-border bg-surface/95 shadow-[0_-4px_20px_rgba(15,23,42,0.06)] backdrop-blur"
        >
            <div
                class="mx-auto flex h-[72px] max-w-7xl items-center justify-around px-2 sm:px-8"
            >
                <!-- Dashboard -->

                <Link
                    href="/seller"
                    class="flex min-w-[72px] flex-col items-center justify-center gap-1.5 rounded-xl px-3 py-2 transition focus-visible:outline-none"
                    :class="
                        isActive('/seller') && page.url === '/seller'
                            ? 'bg-brand-50 text-brand-700'
                            : 'text-muted hover:bg-background hover:text-heading'
                    "
                >
                    <LayoutDashboard :size="20" />

                    <span class="text-[11px] font-semibold"> Dashboard </span>
                </Link>

                <!-- Products -->

                <Link
                    href="/seller/products"
                    class="flex min-w-[72px] flex-col items-center justify-center gap-1.5 rounded-xl px-3 py-2 transition focus-visible:outline-none"
                    :class="
                        isActive('/seller/products')
                            ? 'bg-brand-50 text-brand-700'
                            : 'text-muted hover:bg-background hover:text-heading'
                    "
                >
                    <Package :size="20" />

                    <span class="text-[11px] font-semibold"> Products </span>
                </Link>

                <!-- Add Product -->

                <Link
                    href="/seller/products/create"
                    class="flex min-w-[72px] flex-col items-center justify-center gap-1.5 rounded-xl px-3 py-2 transition focus-visible:outline-none"
                    :class="
                        page.url === '/seller/products/create'
                            ? 'bg-brand-50 text-brand-700'
                            : 'text-muted hover:bg-background hover:text-heading'
                    "
                >
                    <Plus :size="20" />

                    <span class="text-[11px] font-semibold"> Add Product </span>
                </Link>
            </div>
        </nav>
    </div>
</template>
