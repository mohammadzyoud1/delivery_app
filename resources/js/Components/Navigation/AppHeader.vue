<script setup lang="ts">
// ==================== Imports ====================

import { Link, usePage } from "@inertiajs/vue3";
import {
    Bike,
    ChevronDown,
    LogOut,
    Settings,
    ShieldCheck,
    Store,
    User,
    UserPlus,
} from "lucide-vue-next";
import { ref, computed, onMounted, onBeforeUnmount } from "vue";

// ==================== Props ====================

interface Props {
    title?: string;
}

withDefaults(defineProps<Props>(), {
    title: "Deliver",
});

// ==================== Types ====================

interface AuthUser {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    profile_image: string | null;
    roles: string[];
}

interface PageProps {
    [key: string]: unknown;
    auth: {
        user: AuthUser | null;
    };
}

// ==================== Page Data ====================

const page = usePage<PageProps>();

const user = computed(() => page.props.auth?.user);

// ==================== Menu State ====================

const isMenuOpen = ref(false);

const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
};

const closeMenu = () => {
    isMenuOpen.value = false;
};

// ==================== Role Checks ====================

const isSeller = computed(() => {
    return user.value?.roles.includes("seller") ?? false;
});

const isAdmin = computed(() => {
    return user.value?.roles.includes("admin") ?? false;
});

// ==================== Display Name ====================

const displayName = computed(() => {
    if (!user.value) {
        return "Account";
    }

    return `${user.value.first_name} ${user.value.last_name}`;
});

// ==================== Close Menu On Outside Click ====================

const handleOutsideClick = (event: MouseEvent) => {
    const target = event.target as HTMLElement;

    if (!target.closest("[data-profile-menu]")) {
        closeMenu();
    }
};

onMounted(() => {
    document.addEventListener("click", handleOutsideClick);
});

onBeforeUnmount(() => {
    document.removeEventListener("click", handleOutsideClick);
});
</script>

<template>
    <!-- ==================== Header ==================== -->

    <header class="border-b border-gray-100 bg-white">
        <div
            class="mx-auto flex h-16 w-full max-w-7xl items-center justify-between px-5 sm:px-8"
        >
            <!-- ==================== Brand Logo ==================== -->

            <Link
                href="/"
                class="group flex items-center gap-2"
                aria-label="Delivery4U home"
            >
                <!-- ==================== Bicycle Icon ==================== -->

                <div
                    class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm transition duration-200 group-hover:scale-105 group-hover:bg-blue-700"
                >
                    <Bike :size="21" :stroke-width="2.2" />
                </div>

                <!-- ==================== Brand Name ==================== -->

                <span
                    class="text-xl font-extrabold tracking-tight text-gray-900"
                >
                    delivery<span class="text-blue-600">4u</span>
                </span>
            </Link>

            <!-- ==================== Profile Menu ==================== -->

            <div class="relative" data-profile-menu>
                <!-- ==================== Profile Button ==================== -->

                <button
                    type="button"
                    aria-label="Open profile menu"
                    class="flex items-center gap-2 rounded-full p-1.5 transition hover:bg-gray-100"
                    @click.stop="toggleMenu"
                >
                    <div
                        class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full bg-gray-100 text-gray-700"
                    >
                        <img
                            v-if="user?.profile_image"
                            :src="`/storage/${user.profile_image}`"
                            :alt="displayName"
                            class="h-full w-full object-cover"
                        />

                        <User v-else :size="19" :stroke-width="2" />
                    </div>

                    <ChevronDown
                        :size="16"
                        :stroke-width="2"
                        class="hidden text-gray-500 sm:block"
                    />
                </button>

                <!-- ==================== Dropdown ==================== -->

                <div
                    v-if="isMenuOpen"
                    class="absolute right-0 z-50 mt-2 w-72 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xl shadow-gray-200/50"
                >
                    <!-- ==================== User Information ==================== -->

                    <div class="border-b border-gray-100 px-4 py-4">
                        <p class="truncate text-sm font-semibold text-gray-900">
                            {{ displayName }}
                        </p>

                        <p class="mt-0.5 truncate text-xs text-gray-500">
                            {{ user?.email }}
                        </p>
                    </div>

                    <!-- ==================== Account Links ==================== -->

                    <div class="p-2">
                        <!-- ==================== Seller Dashboard ==================== -->

                        <Link
                            v-if="isSeller"
                            href="/seller"
                            class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                            @click="closeMenu"
                        >
                            <Store :size="18" class="text-gray-500" />
                            Seller Dashboard
                        </Link>

                        <!-- ==================== Admin Dashboard ==================== -->

                        <Link
                            v-if="isAdmin"
                            href="/admin"
                            class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                            @click="closeMenu"
                        >
                            <ShieldCheck :size="18" class="text-gray-500" />
                            Admin Dashboard
                        </Link>

                        <!-- ==================== Apply As Seller ==================== -->

                        <Link
                            v-if="!isSeller"
                            href="/seller/apply"
                            class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                            @click="closeMenu"
                        >
                            <UserPlus :size="18" class="text-gray-500" />
                            Apply to Become a Seller
                        </Link>

                        <!-- ==================== Profile ==================== -->

                        <Link
                            href="/profile"
                            class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                            @click="closeMenu"
                        >
                            <User :size="18" class="text-gray-500" />
                            My Profile
                        </Link>

                        <!-- ==================== Settings ==================== -->

                        <Link
                            href="/settings"
                            class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                            @click="closeMenu"
                        >
                            <Settings :size="18" class="text-gray-500" />
                            Settings
                        </Link>
                    </div>

                    <!-- ==================== Logout ==================== -->

                    <div class="border-t border-gray-100 p-2">
                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-red-600 transition hover:bg-red-50"
                            @click="closeMenu"
                        >
                            <LogOut :size="18" />
                            Logout
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>
