<script setup lang="ts">
// ==================== Imports ====================

import { Link, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

import Button from "@/Components/UI/Button.vue";

import {
    ArrowLeft,
    Bell,
    ClipboardList,
    FolderTree,
    LayoutDashboard,
    Menu,
    MessageSquare,
    Package,
    Settings,
    ShoppingBag,
    Truck,
    UserRound,
    Users,
    X,
} from "lucide-vue-next";

// ==================== Page Data ====================

const page = usePage<{
    flash: {
        success?: string;
        error?: string;
    };
}>();

// ==================== Sidebar State ====================

const sidebarOpen = ref(false);

// ==================== Navigation ====================

const navigation = [
    {
        label: "Dashboard",
        href: "/admin",
        icon: LayoutDashboard,
    },
    {
        label: "Orders",
        href: "/admin/orders",
        icon: ClipboardList,
    },
    {
        label: "Users",
        href: "/admin/users",
        icon: Users,
    },
    {
        label: "Seller Applications",
        href: "/admin/seller-applications",
        icon: ShoppingBag,
    },
    {
        label: "Sellers",
        href: "/admin/sellers",
        icon: UserRound,
    },
    {
        label: "Delivery",
        href: "/admin/delivery",
        icon: Truck,
    },
    {
        label: "Products",
        href: "/admin/products",
        icon: Package,
    },
    {
        label: "Categories",
        href: "/admin/categories",
        icon: FolderTree,
    },
    {
        label: "Messages",
        href: "/admin/messages",
        icon: MessageSquare,
    },
    {
        label: "Notifications",
        href: "/admin/notifications",
        icon: Bell,
    },
    {
        label: "Settings",
        href: "/admin/settings",
        icon: Settings,
    },
];

// ==================== Active Navigation ====================

const isActive = (href: string): boolean => {
    if (href === "/admin") {
        return page.url === "/admin";
    }

    return page.url.startsWith(href);
};

// ==================== Close Mobile Sidebar ====================

const closeSidebar = () => {
    sidebarOpen.value = false;
};
</script>

<template>
    <!-- ==================== Admin Layout ==================== -->

    <div class="min-h-screen bg-background">
        <!-- ==================== Mobile Overlay ==================== -->

        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-40 bg-slate-950/40 backdrop-blur-[2px] lg:hidden"
            @click="closeSidebar"
        ></div>

        <!-- ==================== Sidebar ==================== -->

        <aside
            class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-border bg-surface transition-transform duration-200 lg:translate-x-0"
            :class="
                sidebarOpen
                    ? 'translate-x-0'
                    : '-translate-x-full lg:translate-x-0'
            "
        >
            <!-- ==================== Sidebar Header ==================== -->

            <div
                class="flex h-20 items-center justify-between border-b border-border px-6"
            >
                <Link
                    href="/admin"
                    class="flex items-center gap-2 text-xl font-black tracking-tight text-heading"
                    @click="closeSidebar"
                >
                    <span
                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-600 text-sm font-black text-white"
                    >
                        D
                    </span>

                    <span>ELIVER</span>
                </Link>

                <Button
                    type="button"
                    class="rounded-xl p-2 text-muted hover:bg-background hover:text-heading lg:hidden"
                    @click="closeSidebar"
                >
                    <X class="h-5 w-5" />
                </Button>
            </div>

            <!-- ==================== Navigation ==================== -->

            <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-5">
                <Link
                    v-for="item in navigation"
                    :key="item.href"
                    :href="item.href"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition focus-visible:outline-none"
                    :class="
                        isActive(item.href)
                            ? 'bg-brand-600 text-white shadow-sm'
                            : 'text-muted hover:bg-background hover:text-heading'
                    "
                    @click="closeSidebar"
                >
                    <component :is="item.icon" class="h-5 w-5 shrink-0" />

                    <span>{{ item.label }}</span>
                </Link>
            </nav>

            <!-- ==================== Sidebar Footer ==================== -->

            <div class="border-t border-border p-4">
                <Link
                    href="/"
                    class="group flex items-center gap-3 rounded-xl border border-border px-4 py-3 text-sm font-semibold text-body transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700"
                    @click="closeSidebar"
                >
                    <ArrowLeft
                        class="h-5 w-5 transition-transform group-hover:-translate-x-0.5"
                    />

                    <span>Back to Store</span>
                </Link>
            </div>
        </aside>

        <!-- ==================== Main Area ==================== -->

        <div class="lg:pl-72">
            <!-- ==================== Header ==================== -->

            <header
                class="sticky top-0 z-30 flex h-20 items-center justify-between border-b border-border bg-surface/95 px-4 backdrop-blur sm:px-6 lg:px-8"
            >
                <!-- Header Title -->

                <div class="flex items-center gap-3">
                    <Button
                        type="button"
                        class="rounded-xl p-2 text-muted hover:bg-background hover:text-heading lg:hidden"
                        @click="sidebarOpen = true"
                    >
                        <Menu class="h-6 w-6" />
                    </Button>

                    <div>
                        <p
                            class="text-[10px] font-bold uppercase tracking-[0.18em] text-brand-600 sm:text-xs"
                        >
                            Admin Panel
                        </p>

                        <h1
                            class="text-lg font-bold tracking-tight text-heading"
                        >
                            Deliver
                        </h1>
                    </div>
                </div>

                <!-- ==================== Header Actions ==================== -->

                <div class="flex items-center gap-2">
                    <Link
                        href="/admin/notifications"
                        aria-label="Open notifications"
                        class="rounded-xl p-2.5 text-muted transition hover:bg-background hover:text-heading focus-visible:outline-none"
                    >
                        <Bell class="h-5 w-5" />
                    </Link>

                    <div
                        class="hidden items-center gap-2 rounded-xl bg-background px-3 py-2 sm:flex"
                    >
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-600 text-xs font-bold text-white"
                        >
                            A
                        </div>

                        <span class="text-sm font-semibold text-body">
                            Admin
                        </span>
                    </div>
                </div>
            </header>

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

            <main class="p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
