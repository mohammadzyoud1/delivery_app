<script setup lang="ts">
// ==================== Imports ====================

import { Link, usePage } from "@inertiajs/vue3";
import Button from "@/Components/UI/Button.vue";
import { ref } from "vue";
import {
    Bell,
    Boxes,
    ClipboardList,
    FolderTree,
    LayoutDashboard,
    LogOut,
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
    <div class="min-h-screen bg-gray-50">
        <!-- ==================== Mobile Overlay ==================== -->

        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-40 bg-black/40 lg:hidden"
            @click="closeSidebar"
        />

        <!-- ==================== Sidebar ==================== -->

        <aside
            class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-gray-200 bg-white transition-transform duration-200 lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <!-- ==================== Logo ==================== -->

            <div
                class="flex h-20 items-center justify-between border-b border-gray-100 px-6"
            >
                <Link
                    href="/admin"
                    class="text-2xl font-black tracking-tight text-gray-900"
                    @click="closeSidebar"
                >
                    DELIVER
                </Link>

                <Button
                    type="button"
                    class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 lg:hidden"
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
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition"
                    :class="
                        isActive(item.href)
                            ? 'bg-gray-900 text-white'
                            : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'
                    "
                    @click="closeSidebar"
                >
                    <component :is="item.icon" class="h-5 w-5 shrink-0" />

                    <span>{{ item.label }}</span>
                </Link>
            </nav>

            <!-- ==================== Sidebar Footer ==================== -->

            <div class="border-t border-gray-100 p-4">
                <Link
                    href="/"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900"
                >
                    <LogOut class="h-5 w-5" />

                    <span>Back to Store</span>
                </Link>
            </div>
        </aside>

        <!-- ==================== Main Area ==================== -->

        <div class="lg:pl-72">
            <!-- ==================== Header ==================== -->

            <header
                class="sticky top-0 z-30 flex h-20 items-center justify-between border-b border-gray-200 bg-white/95 px-4 backdrop-blur sm:px-6 lg:px-8"
            >
                <div class="flex items-center gap-3">
                    <Button
                        type="button"
                        class="rounded-xl p-2 text-gray-600 hover:bg-gray-100 lg:hidden"
                        @click="sidebarOpen = true"
                    >
                        <Menu class="h-6 w-6" />
                    </Button>

                    <div>
                        <p
                            class="text-xs font-medium uppercase tracking-wide text-gray-400"
                        >
                            Admin Panel
                        </p>

                        <h1 class="text-lg font-semibold text-gray-900">
                            Deliver
                        </h1>
                    </div>
                </div>

                <!-- ==================== Header Actions ==================== -->

                <div class="flex items-center gap-2">
                    <Link
                        href="/admin/notifications"
                        class="rounded-xl p-2.5 text-gray-600 hover:bg-gray-100"
                    >
                        <Bell class="h-5 w-5" />
                    </Link>

                    <div
                        class="hidden items-center gap-2 rounded-xl bg-gray-50 px-3 py-2 sm:flex"
                    >
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-900 text-xs font-semibold text-white"
                        >
                            A
                        </div>

                        <span class="text-sm font-medium text-gray-700">
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
