<script setup lang="ts">
import { Link, usePage } from "@inertiajs/vue3";
import {
    Home,
    ShoppingCart,
    Package,
    MessageCircle,
    User,
} from "lucide-vue-next";

// ==================== Current Page ====================

const page = usePage();

// ==================== Navigation Items ====================

const navigationItems = [
    {
        label: "Home",
        href: "/",
        icon: Home,
    },
    {
        label: "Cart",
        href: "/cart",
        icon: ShoppingCart,
    },
    {
        label: "Orders",
        href: "/orders",
        icon: Package,
    },
    {
        label: "Messages",
        href: "/messages",
        icon: MessageCircle,
    },
    {
        label: "Profile",
        href: "/profile",
        icon: User,
    },
];

// ==================== Active State ====================

const isActive = (href: string): boolean => {
    return page.url === href;
};
</script>
<template>
    <!-- ==================== Bottom Navigation ==================== -->

    <nav
        class="fixed inset-x-0 bottom-0 z-50 border-t border-gray-100 bg-white"
    >
        <div
            class="mx-auto flex h-16 w-full max-w-7xl items-center justify-around px-2 sm:px-8"
        >
            <Link
                v-for="item in navigationItems"
                :key="item.label"
                :href="item.href"
                :class="[
                    'flex min-w-0 flex-1 flex-col items-center justify-center gap-1 transition',
                    isActive(item.href)
                        ? 'text-gray-900'
                        : 'text-gray-400 hover:text-gray-700',
                ]"
            >
                <component
                    :is="item.icon"
                    :size="20"
                    :stroke-width="isActive(item.href) ? 2.5 : 2"
                />

                <span class="text-[11px] font-medium sm:text-xs">
                    {{ item.label }}
                </span>
            </Link>
        </div>
    </nav>
</template>
