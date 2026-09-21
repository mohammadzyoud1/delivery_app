<script setup lang="ts">
// ==================== Imports ====================

import { Head, Link } from "@inertiajs/vue3";
import { Mail, Phone, UserRound } from "lucide-vue-next";
import AdminLayout from "@/Layouts/AdminLayout.vue";

// ==================== Interfaces ====================

interface Role {
    id: number;
    name: string;
}

interface User {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone: string | null;
    profile_image: string | null;
    email_verified_at: string | null;
    created_at: string;
    roles: Role[];
}

// ==================== Props ====================

const props = defineProps<{
    users: User[];
}>();

// ==================== Helpers ====================

const fullName = (user: User): string => {
    return `${user.first_name} ${user.last_name}`.trim();
};

const formatDate = (date: string): string => {
    return new Date(date).toLocaleDateString();
};

const roleNames = (user: User): string => {
    if (user.roles.length === 0) {
        return "Customer";
    }

    return user.roles.map((role) => role.name).join(", ");
};
</script>

<template>
    <Head title="Users" />

    <AdminLayout>
        <!-- ==================== Page Header ==================== -->

        <div class="mb-8">
            <p
                class="mb-2 text-xs font-bold uppercase tracking-[0.18em] text-brand-600"
            >
                Admin Panel
            </p>

            <div
                class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end"
            >
                <div>
                    <h1
                        class="text-2xl font-bold tracking-tight text-heading sm:text-3xl"
                    >
                        Users
                    </h1>

                    <p class="mt-2 text-sm text-muted sm:text-base">
                        Manage and view all registered users.
                    </p>
                </div>

                <div
                    class="inline-flex w-fit items-center rounded-xl border border-border bg-surface px-4 py-2.5 text-sm font-semibold text-body shadow-sm"
                >
                    {{ props.users.length }}
                    {{ props.users.length === 1 ? "user" : "users" }}
                </div>
            </div>
        </div>

        <!-- ==================== Desktop Table ==================== -->

        <div
            class="hidden overflow-hidden rounded-2xl border border-border bg-surface shadow-sm lg:block"
        >
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <!-- ==================== Table Header ==================== -->

                    <thead class="border-b border-border bg-background">
                        <tr>
                            <th
                                class="px-6 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                            >
                                User
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                            >
                                Contact
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                            >
                                Role
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                            >
                                Email Status
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                            >
                                Joined
                            </th>

                            <th class="px-6 py-4"></th>
                        </tr>
                    </thead>

                    <!-- ==================== Table Body ==================== -->

                    <tbody class="divide-y divide-border">
                        <tr
                            v-for="user in props.users"
                            :key="user.id"
                            class="transition-colors hover:bg-background"
                        >
                            <!-- ==================== User ==================== -->

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img
                                        v-if="user.profile_image"
                                        :src="user.profile_image"
                                        :alt="fullName(user)"
                                        class="h-10 w-10 rounded-full object-cover ring-2 ring-brand-50"
                                    />

                                    <div
                                        v-else
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600"
                                    >
                                        <UserRound class="h-5 w-5" />
                                    </div>

                                    <div class="min-w-0">
                                        <p
                                            class="truncate text-sm font-semibold text-heading"
                                        >
                                            {{ fullName(user) }}
                                        </p>

                                        <p class="mt-0.5 text-xs text-muted">
                                            #{{ user.id }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- ==================== Contact ==================== -->

                            <td class="px-6 py-4">
                                <div class="space-y-1.5">
                                    <div
                                        class="flex items-center gap-2 text-sm text-body"
                                    >
                                        <Mail
                                            class="h-4 w-4 shrink-0 text-muted"
                                        />

                                        <span class="max-w-xs truncate">
                                            {{ user.email }}
                                        </span>
                                    </div>

                                    <div
                                        v-if="user.phone"
                                        class="flex items-center gap-2 text-sm text-body"
                                    >
                                        <Phone
                                            class="h-4 w-4 shrink-0 text-muted"
                                        />

                                        <span>{{ user.phone }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- ==================== Role ==================== -->

                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex rounded-full border border-brand-100 bg-brand-50 px-3 py-1 text-xs font-bold capitalize text-brand-700"
                                >
                                    {{ roleNames(user) }}
                                </span>
                            </td>

                            <!-- ==================== Email Status ==================== -->

                            <td class="px-6 py-4">
                                <span
                                    v-if="user.email_verified_at"
                                    class="inline-flex rounded-full border border-green-100 bg-green-50 px-3 py-1 text-xs font-bold text-green-700"
                                >
                                    Verified
                                </span>

                                <span
                                    v-else
                                    class="inline-flex rounded-full border border-yellow-100 bg-yellow-50 px-3 py-1 text-xs font-bold text-yellow-700"
                                >
                                    Unverified
                                </span>
                            </td>

                            <!-- ==================== Joined ==================== -->

                            <td
                                class="whitespace-nowrap px-6 py-4 text-sm text-muted"
                            >
                                {{ formatDate(user.created_at) }}
                            </td>

                            <!-- ==================== Action ==================== -->

                            <td class="px-6 py-4 text-right">
                                <Link
                                    :href="`/admin/users/${user.id}`"
                                    class="inline-flex items-center rounded-xl border border-border px-3 py-2 text-sm font-semibold text-body transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700 focus-visible:outline-none"
                                >
                                    View
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- ==================== Desktop Empty State ==================== -->

            <div v-if="props.users.length === 0" class="px-6 py-14 text-center">
                <div
                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"
                >
                    <UserRound class="h-6 w-6" />
                </div>

                <h2 class="mt-4 text-base font-semibold text-heading">
                    No users found
                </h2>

                <p class="mt-1 text-sm text-muted">
                    Registered users will appear here.
                </p>
            </div>
        </div>

        <!-- ==================== Mobile Cards ==================== -->

        <div class="space-y-4 lg:hidden">
            <div
                v-for="user in props.users"
                :key="user.id"
                class="rounded-2xl border border-border bg-surface p-5 shadow-sm transition hover:shadow-md"
            >
                <!-- ==================== User Header ==================== -->

                <div class="flex items-start gap-3">
                    <img
                        v-if="user.profile_image"
                        :src="user.profile_image"
                        :alt="fullName(user)"
                        class="h-12 w-12 shrink-0 rounded-full object-cover ring-2 ring-brand-50"
                    />

                    <div
                        v-else
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600"
                    >
                        <UserRound class="h-5 w-5" />
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-heading">
                            {{ fullName(user) }}
                        </p>

                        <p class="mt-0.5 text-xs text-muted">
                            User #{{ user.id }}
                        </p>
                    </div>

                    <span
                        class="shrink-0 rounded-full border border-brand-100 bg-brand-50 px-2.5 py-1 text-xs font-bold capitalize text-brand-700"
                    >
                        {{ roleNames(user) }}
                    </span>
                </div>

                <!-- ==================== Contact ==================== -->

                <div class="mt-5 space-y-3 rounded-xl bg-background p-4">
                    <div class="flex min-w-0 items-center gap-2.5">
                        <Mail class="h-4 w-4 shrink-0 text-muted" />

                        <span class="truncate text-sm text-body">
                            {{ user.email }}
                        </span>
                    </div>

                    <div v-if="user.phone" class="flex items-center gap-2.5">
                        <Phone class="h-4 w-4 shrink-0 text-muted" />

                        <span class="text-sm text-body">
                            {{ user.phone }}
                        </span>
                    </div>
                </div>

                <!-- ==================== Status + Date ==================== -->

                <div class="mt-4 flex items-center justify-between gap-4">
                    <span
                        v-if="user.email_verified_at"
                        class="rounded-full border border-green-100 bg-green-50 px-3 py-1 text-xs font-bold text-green-700"
                    >
                        Verified
                    </span>

                    <span
                        v-else
                        class="rounded-full border border-yellow-100 bg-yellow-50 px-3 py-1 text-xs font-bold text-yellow-700"
                    >
                        Unverified
                    </span>

                    <span class="text-xs font-medium text-muted">
                        {{ formatDate(user.created_at) }}
                    </span>
                </div>

                <!-- ==================== Action ==================== -->

                <Link
                    :href="`/admin/users/${user.id}`"
                    class="mt-4 flex w-full items-center justify-center rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-700 focus-visible:outline-none"
                >
                    View User
                </Link>
            </div>

            <!-- ==================== Mobile Empty State ==================== -->

            <div
                v-if="props.users.length === 0"
                class="rounded-2xl border border-border bg-surface px-6 py-14 text-center shadow-sm"
            >
                <div
                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"
                >
                    <UserRound class="h-6 w-6" />
                </div>

                <h2 class="mt-4 text-base font-semibold text-heading">
                    No users found
                </h2>

                <p class="mt-1 text-sm text-muted">
                    Registered users will appear here.
                </p>
            </div>
        </div>
    </AdminLayout>
</template>
