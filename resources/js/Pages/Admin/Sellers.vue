<script setup lang="ts">
// ==================== Imports ====================

import { Head, Link } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Eye, Package, Users } from "lucide-vue-next";

// ==================== Interfaces ====================

interface Role {
    id: number;
    name: string;
}

interface Product {
    id: number;
    name: string;
}

interface Seller {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone: string | null;
    profile_image: string | null;
    email_verified_at: string | null;
    created_at: string;
    roles: Role[];
    products: Product[];
}

// ==================== Props ====================

const props = defineProps<{
    sellers: Seller[];
}>();

// ==================== Helpers ====================

const fullName = (seller: Seller): string => {
    return `${seller.first_name} ${seller.last_name}`;
};

const formatDate = (date: string): string => {
    return new Date(date).toLocaleDateString();
};
</script>

<template>
    <Head title="Sellers" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- ==================== Header ==================== -->

            <div>
                <p
                    class="text-xs font-medium uppercase tracking-wide text-gray-400"
                >
                    Admin Panel
                </p>

                <h1 class="mt-1 text-2xl font-bold text-gray-900">Sellers</h1>

                <p class="mt-1 text-sm text-gray-500">
                    Manage users who have seller access.
                </p>
            </div>

            <!-- ==================== Seller Count ==================== -->

            <div
                class="flex items-center gap-3 rounded-2xl border border-gray-200 bg-white p-5"
            >
                <div
                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-gray-100"
                >
                    <Users class="h-5 w-5 text-gray-700" />
                </div>

                <div>
                    <p class="text-xs text-gray-400">Total Sellers</p>

                    <p class="text-xl font-bold text-gray-900">
                        {{ sellers.length }}
                    </p>
                </div>
            </div>

            <!-- ==================== Empty State ==================== -->

            <div
                v-if="sellers.length === 0"
                class="rounded-2xl border border-gray-200 bg-white px-6 py-14 text-center"
            >
                <Users class="mx-auto h-10 w-10 text-gray-300" />

                <h2 class="mt-4 text-lg font-semibold text-gray-900">
                    No sellers yet
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Approved seller applications will appear here.
                </p>
            </div>

            <!-- ==================== Desktop Table ==================== -->

            <div
                v-else
                class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white md:block"
            >
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="border-b border-gray-100 bg-gray-50">
                            <tr>
                                <th
                                    class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                                >
                                    Seller
                                </th>

                                <th
                                    class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                                >
                                    Contact
                                </th>

                                <th
                                    class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                                >
                                    Products
                                </th>

                                <th
                                    class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                                >
                                    Verification
                                </th>

                                <th
                                    class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                                >
                                    Joined
                                </th>

                                <th
                                    class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wide text-gray-500"
                                >
                                    Action
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            <tr
                                v-for="seller in sellers"
                                :key="seller.id"
                                class="transition hover:bg-gray-50"
                            >
                                <!-- Seller -->

                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-gray-100"
                                        >
                                            <img
                                                v-if="seller.profile_image"
                                                :src="seller.profile_image"
                                                :alt="fullName(seller)"
                                                class="h-full w-full object-cover"
                                            />

                                            <span
                                                v-else
                                                class="text-sm font-semibold text-gray-500"
                                            >
                                                {{
                                                    seller.first_name
                                                        .charAt(0)
                                                        .toUpperCase()
                                                }}
                                            </span>
                                        </div>

                                        <div>
                                            <p
                                                class="font-medium text-gray-900"
                                            >
                                                {{ fullName(seller) }}
                                            </p>

                                            <p class="text-xs text-gray-400">
                                                #{{ seller.id }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Contact -->

                                <td class="px-5 py-4">
                                    <p
                                        class="max-w-xs truncate text-sm text-gray-700"
                                    >
                                        {{ seller.email }}
                                    </p>

                                    <p class="mt-1 text-xs text-gray-400">
                                        {{ seller.phone || "No phone" }}
                                    </p>
                                </td>

                                <!-- Products -->

                                <td class="px-5 py-4">
                                    <div
                                        class="flex items-center gap-2 text-sm font-medium text-gray-700"
                                    >
                                        <Package
                                            class="h-4 w-4 text-gray-400"
                                        />

                                        {{ seller.products.length }}
                                    </div>
                                </td>

                                <!-- Verification -->

                                <td class="px-5 py-4">
                                    <span
                                        v-if="seller.email_verified_at"
                                        class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700"
                                    >
                                        Verified
                                    </span>

                                    <span
                                        v-else
                                        class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700"
                                    >
                                        Unverified
                                    </span>
                                </td>

                                <!-- Joined -->

                                <td
                                    class="whitespace-nowrap px-5 py-4 text-sm text-gray-600"
                                >
                                    {{ formatDate(seller.created_at) }}
                                </td>

                                <!-- Action -->

                                <td class="px-5 py-4 text-right">
                                    <Link
                                        :href="`/admin/users/${seller.id}`"
                                        class="inline-flex items-center gap-2 rounded-xl border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                                    >
                                        <Eye class="h-4 w-4" />
                                        View
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ==================== Mobile Cards ==================== -->

            <div v-if="sellers.length > 0" class="space-y-3 md:hidden">
                <div
                    v-for="seller in sellers"
                    :key="seller.id"
                    class="rounded-2xl border border-gray-200 bg-white p-4"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-full bg-gray-100"
                            >
                                <img
                                    v-if="seller.profile_image"
                                    :src="seller.profile_image"
                                    :alt="fullName(seller)"
                                    class="h-full w-full object-cover"
                                />

                                <span
                                    v-else
                                    class="text-sm font-semibold text-gray-500"
                                >
                                    {{
                                        seller.first_name
                                            .charAt(0)
                                            .toUpperCase()
                                    }}
                                </span>
                            </div>

                            <div>
                                <p class="font-semibold text-gray-900">
                                    {{ fullName(seller) }}
                                </p>

                                <p class="text-xs text-gray-400">
                                    #{{ seller.id }}
                                </p>
                            </div>
                        </div>

                        <span
                            class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700"
                        >
                            Seller
                        </span>
                    </div>

                    <div
                        class="mt-4 grid grid-cols-2 gap-3 border-t border-gray-100 pt-4"
                    >
                        <div>
                            <p class="text-xs text-gray-400">Products</p>

                            <p
                                class="mt-1 flex items-center gap-1.5 text-sm font-medium text-gray-800"
                            >
                                <Package class="h-4 w-4 text-gray-400" />

                                {{ seller.products.length }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-400">Joined</p>

                            <p class="mt-1 text-sm font-medium text-gray-800">
                                {{ formatDate(seller.created_at) }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <Link
                            :href="`/admin/users/${seller.id}`"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-gray-800"
                        >
                            <Eye class="h-4 w-4" />
                            View Seller
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
