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
    Truck,
    LogIn,
} from "lucide-vue-next";
import { ref, computed, onMounted, onBeforeUnmount } from "vue";

// ==================== Props ====================

interface Props {
    title?: string;
}

const props = withDefaults(defineProps<Props>(), {
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

    <header
        class="relative z-50 border-b border-border/80 bg-surface/95 backdrop-blur-sm"
    >
        <div
            class="mx-auto flex h-[68px] w-full max-w-7xl items-center justify-between px-5 sm:px-8"
        >
            <!-- ==================== Brand Logo ==================== -->

            <Link
                href="/"
                class="group flex items-center gap-2.5"
                aria-label="Delivery4U home"
            >
                <!-- ==================== Bicycle Icon ==================== -->

                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-600 text-white shadow-sm shadow-brand-600/20 transition-all duration-200 group-hover:scale-105 group-hover:bg-brand-700"
                >
                    <Bike :size="21" :stroke-width="2.2" />
                </div>

                <!-- ==================== Brand Name ==================== -->

                <span
                    class="text-[21px] font-extrabold tracking-[-0.03em] text-heading"
                >
                    delivery<span class="text-brand-600">4u</span>
                </span>
            </Link>

            <!-- ==================== Profile Menu ==================== -->

            <div class="relative" data-profile-menu>
                <!-- ==================== Profile Button ==================== -->

                <button
                    type="button"
                    aria-label="Open profile menu"
                    class="group flex items-center gap-2 rounded-full p-1.5 transition-colors duration-200 hover:bg-brand-50 focus-visible:outline-none"
                    @click.stop="toggleMenu"
                >
                    <!-- ==================== Profile Avatar ==================== -->

                    <div
                        class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full bg-brand-50 text-brand-600 ring-1 ring-border transition-all duration-200 group-hover:ring-brand-100"
                    >
                        <img
                            v-if="user?.profile_image"
                            :src="`/storage/${user.profile_image}`"
                            :alt="displayName"
                            class="h-full w-full object-cover"
                        />

                        <User v-else :size="18" :stroke-width="2" />
                    </div>

                    <!-- ==================== Dropdown Indicator ==================== -->

                    <ChevronDown
                        :size="16"
                        :stroke-width="2"
                        class="hidden text-muted transition-transform duration-200 sm:block"
                        :class="{ 'rotate-180': isMenuOpen }"
                    />
                </button>

                <!-- ==================== Dropdown ==================== -->

                <div
                    v-if="isMenuOpen"
                    class="absolute right-0 z-[60] mt-3 w-72 overflow-hidden rounded-2xl border border-border bg-surface shadow-2xl shadow-slate-900/10"
                >
                    <!-- ==================== User Information ==================== -->

                    <div
                        class="border-b border-border bg-background/60 px-4 py-4"
                    >
                        <p class="truncate text-sm font-semibold text-heading">
                            {{ displayName }}
                        </p>

                        <p class="mt-1 truncate text-xs text-muted">
                            {{ user?.email }}
                        </p>
                    </div>

                    <!-- ==================== Account Links ==================== -->

                    <div class="p-2">
                        <!-- ==================== Seller Dashboard ==================== -->

                        <Link
                            v-if="isSeller || isAdmin"
                            href="/seller"
                            class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-body transition-colors duration-150 hover:bg-brand-50 hover:text-brand-700"
                            @click="closeMenu"
                        >
                            <Store
                                :size="18"
                                class="text-muted transition-colors group-hover:text-brand-600"
                            />
                            Seller Dashboard
                        </Link>

                        <!-- ==================== Admin Dashboard ==================== -->

                        <Link
                            v-if="isAdmin"
                            href="/admin"
                            class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-body transition-colors duration-150 hover:bg-brand-50 hover:text-brand-700"
                            @click="closeMenu"
                        >
                            <ShieldCheck
                                :size="18"
                                class="text-muted transition-colors group-hover:text-brand-600"
                            />
                            Admin Dashboard
                        </Link>

                        <!-- ==================== Apply As Seller ==================== -->

                        <Link
                            v-if="
                                !isSeller && !isAdmin && page.props.auth?.user
                            "
                            href="/seller/apply"
                            class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-body transition-colors duration-150 hover:bg-brand-50 hover:text-brand-700"
                            @click="closeMenu"
                        >
                            <UserPlus
                                :size="18"
                                class="text-muted transition-colors group-hover:text-brand-600"
                            />
                            Apply to Become a Seller
                        </Link>
                        <!-- ==================== Delivery ==================== -->

                        <Link
                            v-if="user?.roles.includes('delivery')"
                            href="/delivery/orders"
                            class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-body transition-colors duration-150 hover:bg-brand-50 hover:text-brand-700"
                            @click="closeMenu"
                        >
                            <Truck
                                :size="18"
                                class="text-muted transition-colors group-hover:text-brand-600"
                            />
                            Delivery Orders
                        </Link>

                        <!-- ==================== Profile ==================== -->

                        <Link
                            v-if="page.props.auth?.user"
                            href="/profile"
                            class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-body transition-colors duration-150 hover:bg-brand-50 hover:text-brand-700"
                            @click="closeMenu"
                        >
                            <User
                                :size="18"
                                class="text-muted transition-colors group-hover:text-brand-600"
                            />
                            My Profile
                        </Link>

                        <!-- ==================== Settings ==================== -->

                        <Link
                            v-if="page.props.auth?.user"
                            href="/settings"
                            class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-body transition-colors duration-150 hover:bg-brand-50 hover:text-brand-700"
                            @click="closeMenu"
                        >
                            <Settings
                                :size="18"
                                class="text-muted transition-colors group-hover:text-brand-600"
                            />
                            Settings
                        </Link>
                    </div>

                    <!-- ==================== Logout ==================== -->

                    <div class="border-t border-border p-2">
                        <Link
                            v-if="page.props.auth?.user"
                            href="/logout"
                            method="post"
                            as="button"
                            class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-red-600 transition-colors duration-150 hover:bg-red-50"
                            @click="closeMenu"
                        >
                            <LogOut :size="18" />
                            Logout
                        </Link>
                        <Link
                            v-else
                            href="/login"
                            method="get"
                            as="button"
                            class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-blue-600 transition-colors duration-150 hover:bg-red-50"
                            @click="closeMenu"
                        >
                            <LogIn :size="18" />
                            Login
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>
