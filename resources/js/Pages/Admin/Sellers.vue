<script setup lang="ts">
// ==================== Imports ====================

import { Head, Link } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Eye, Package, Users } from "lucide-vue-next";
import { t } from "@/Utils/i18n";

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
            <!-- ==================== Page Header ==================== -->

            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
            >
                <div>
                    <p
                        class="text-xs font-bold uppercase tracking-[0.16em] text-brand-600"
                    >
                        {{ t("adminPanel") }}
                    </p>

                    <h1
                        class="mt-1 text-2xl font-bold tracking-tight text-heading"
                    >
                        {{ t("sellers") }}
                    </h1>

                    <p class="mt-1 max-w-2xl text-sm text-muted">
                        {{ t("manageSellersDescription") }}
                    </p>
                </div>

                <div
                    class="inline-flex w-fit items-center rounded-full border border-border bg-surface px-4 py-2 text-sm font-semibold text-body shadow-sm"
                >
                    {{ sellers.length }} {{ t("sellers") }}
                </div>
            </div>

            <!-- ==================== Seller Statistics ==================== -->

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                        >
                            <Users class="h-5 w-5" />
                        </div>

                        <div>
                            <p
                                class="text-xs font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("totalSellers") }}
                            </p>

                            <p class="mt-0.5 text-xl font-bold text-heading">
                                {{ sellers.length }}
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                        >
                            <Package class="h-5 w-5" />
                        </div>

                        <div>
                            <p
                                class="text-xs font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("totalProducts") }}
                            </p>

                            <p class="mt-0.5 text-xl font-bold text-heading">
                                {{
                                    sellers.reduce(
                                        (total, seller) =>
                                            total + seller.products.length,
                                        0,
                                    )
                                }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==================== Empty State ==================== -->

            <div
                v-if="sellers.length === 0"
                class="rounded-2xl border border-border bg-surface px-6 py-14 text-center shadow-sm"
            >
                <div
                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"
                >
                    <Users class="h-6 w-6" />
                </div>

                <h2 class="mt-4 text-base font-semibold text-heading">
                    {{ t("noSellersYet") }}
                </h2>

                <p class="mt-1 text-sm text-muted">
                    {{ t("approvedSellersAppear") }}
                </p>
            </div>

            <!-- ==================== Desktop Table ==================== -->

            <div
                v-else
                class="hidden overflow-hidden rounded-2xl border border-border bg-surface shadow-sm md:block"
            >
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] text-left">
                        <thead class="border-b border-border bg-background">
                            <tr>
                                <th
                                    class="px-5 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    {{ t("seller") }}
                                </th>

                                <th
                                    class="px-5 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    {{ t("contact") }}
                                </th>

                                <th
                                    class="px-5 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    {{ t("products") }}
                                </th>

                                <th
                                    class="px-5 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    {{ t("emailStatus") }}
                                </th>

                                <th
                                    class="px-5 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                                >
                                    {{ t("joinDate") }}
                                </th>

                                <th
                                    class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wide text-muted"
                                ></th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-border">
                            <tr
                                v-for="seller in sellers"
                                :key="seller.id"
                                class="transition-colors hover:bg-background"
                            >
                                <!-- ==================== Seller ==================== -->

                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-brand-50 text-brand-600"
                                        >
                                            <img
                                                v-if="seller.profile_image"
                                                :src="seller.profile_image"
                                                :alt="fullName(seller)"
                                                class="h-full w-full object-cover"
                                            />

                                            <span
                                                v-else
                                                class="text-sm font-bold"
                                            >
                                                {{
                                                    seller.first_name
                                                        .charAt(0)
                                                        .toUpperCase()
                                                }}
                                            </span>
                                        </div>

                                        <div class="min-w-0">
                                            <p
                                                class="truncate font-semibold text-heading"
                                            >
                                                {{ fullName(seller) }}
                                            </p>

                                            <p
                                                class="mt-0.5 text-xs text-muted"
                                            >
                                                #{{ seller.id }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- ==================== Contact ==================== -->

                                <td class="px-5 py-4">
                                    <p
                                        class="max-w-xs truncate text-sm font-medium text-body"
                                    >
                                        {{ seller.email }}
                                    </p>

                                    <p class="mt-1 text-xs text-muted">
                                        {{ seller.phone || "No phone" }}
                                    </p>
                                </td>

                                <!-- ==================== Products ==================== -->

                                <td class="px-5 py-4">
                                    <div
                                        class="inline-flex items-center gap-2 rounded-lg bg-background px-2.5 py-1.5 text-sm font-semibold text-body"
                                    >
                                        <Package class="h-4 w-4 text-muted" />

                                        {{ seller.products.length }}
                                    </div>
                                </td>

                                <!-- ==================== Verification ==================== -->

                                <td class="px-5 py-4">
                                    <span
                                        v-if="seller.email_verified_at"
                                        class="inline-flex rounded-full border border-green-100 bg-green-50 px-3 py-1 text-xs font-bold text-green-700"
                                    >
                                        {{ t("verified") }}
                                    </span>

                                    <span
                                        v-else
                                        class="inline-flex rounded-full border border-yellow-100 bg-yellow-50 px-3 py-1 text-xs font-bold text-yellow-700"
                                    >
                                        {{ t("unverified") }}
                                    </span>
                                </td>

                                <!-- ==================== Joined ==================== -->

                                <td
                                    class="whitespace-nowrap px-5 py-4 text-sm text-body"
                                >
                                    {{ formatDate(seller.created_at) }}
                                </td>

                                <!-- ==================== Action ==================== -->

                                <td class="px-5 py-4 text-right">
                                    <Link
                                        :href="`/admin/users/${seller.id}`"
                                        class="inline-flex items-center gap-2 rounded-xl border border-border px-3 py-2 text-sm font-semibold text-body transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700 focus-visible:outline-none"
                                    >
                                        <Eye class="h-4 w-4" />
                                        {{ t("view") }}
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ==================== Mobile Cards ==================== -->

            <div v-if="sellers.length > 0" class="space-y-4 md:hidden">
                <div
                    v-for="seller in sellers"
                    :key="seller.id"
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm"
                >
                    <!-- ==================== Seller Header ==================== -->

                    <div class="flex items-start justify-between gap-3">
                        <div class="flex min-w-0 items-center gap-3">
                            <div
                                class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-full bg-brand-50 text-brand-600"
                            >
                                <img
                                    v-if="seller.profile_image"
                                    :src="seller.profile_image"
                                    :alt="fullName(seller)"
                                    class="h-full w-full object-cover"
                                />

                                <span v-else class="text-sm font-bold">
                                    {{
                                        seller.first_name
                                            .charAt(0)
                                            .toUpperCase()
                                    }}
                                </span>
                            </div>

                            <div class="min-w-0">
                                <p class="truncate font-semibold text-heading">
                                    {{ fullName(seller) }}
                                </p>

                                <p class="mt-0.5 text-xs text-muted">
                                    #{{ seller.id }}
                                </p>
                            </div>
                        </div>

                        <span
                            class="shrink-0 rounded-full border border-brand-100 bg-brand-50 px-2.5 py-1 text-xs font-bold text-brand-700"
                        >
                            {{ t("seller") }}
                        </span>
                    </div>

                    <!-- ==================== Contact ==================== -->

                    <div class="mt-4 border-t border-border pt-4">
                        <p
                            class="text-[11px] font-bold uppercase tracking-wide text-muted"
                        >
                            {{ t("contact") }}
                        </p>

                        <p class="mt-1 truncate text-sm font-medium text-body">
                            {{ seller.email }}
                        </p>

                        <p class="mt-0.5 text-xs text-muted">
                            {{ seller.phone || "No phone number" }}
                        </p>
                    </div>

                    <!-- ==================== Seller Metadata ==================== -->

                    <div
                        class="mt-4 grid grid-cols-2 gap-3 border-t border-border pt-4"
                    >
                        <div>
                            <p
                                class="text-[11px] font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("products") }}
                            </p>

                            <p
                                class="mt-1 flex items-center gap-1.5 text-sm font-semibold text-heading"
                            >
                                <Package class="h-4 w-4 text-brand-600" />

                                {{ seller.products.length }}
                            </p>
                        </div>

                        <div>
                            <p
                                class="text-[11px] font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("joinDate") }}
                            </p>

                            <p class="mt-1 text-sm font-semibold text-heading">
                                {{ formatDate(seller.created_at) }}
                            </p>
                        </div>
                    </div>

                    <!-- ==================== Verification ==================== -->

                    <div class="mt-4 border-t border-border pt-4">
                        <span
                            v-if="seller.email_verified_at"
                            class="inline-flex rounded-full border border-green-100 bg-green-50 px-3 py-1 text-xs font-bold text-green-700"
                        >
                            {{ t("verified") }}
                        </span>

                        <span
                            v-else
                            class="inline-flex rounded-full border border-yellow-100 bg-yellow-50 px-3 py-1 text-xs font-bold text-yellow-700"
                        >
                            {{ t("unverified") }}
                        </span>
                    </div>

                    <!-- ==================== Action ==================== -->

                    <Link
                        :href="`/admin/users/${seller.id}`"
                        class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-brand-700 focus-visible:outline-none"
                    >
                        <Eye class="h-4 w-4" />
                        {{ t("view") }}
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
