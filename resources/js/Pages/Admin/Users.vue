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

        <div
            class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Users</h1>

                <p class="mt-1 text-sm text-gray-500">
                    Manage and view registered users.
                </p>
            </div>

            <div
                class="rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700"
            >
                {{ props.users.length }} users
            </div>
        </div>

        <!-- ==================== Desktop Table ==================== -->

        <div
            class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white lg:block"
        >
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="border-b border-gray-200 bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                User
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Contact
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Role
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Email Status
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Joined
                            </th>

                            <th class="px-6 py-4"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        <tr
                            v-for="user in props.users"
                            :key="user.id"
                            class="transition hover:bg-gray-50"
                        >
                            <!-- ==================== User ==================== -->

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img
                                        v-if="user.profile_image"
                                        :src="user.profile_image"
                                        :alt="fullName(user)"
                                        class="h-10 w-10 rounded-full object-cover"
                                    />

                                    <div
                                        v-else
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100"
                                    >
                                        <UserRound
                                            class="h-5 w-5 text-gray-500"
                                        />
                                    </div>

                                    <div>
                                        <p
                                            class="text-sm font-semibold text-gray-900"
                                        >
                                            {{ fullName(user) }}
                                        </p>

                                        <p class="text-xs text-gray-400">
                                            #{{ user.id }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- ==================== Contact ==================== -->

                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <div
                                        class="flex items-center gap-2 text-sm text-gray-600"
                                    >
                                        <Mail class="h-4 w-4 text-gray-400" />
                                        <span>{{ user.email }}</span>
                                    </div>

                                    <div
                                        v-if="user.phone"
                                        class="flex items-center gap-2 text-sm text-gray-600"
                                    >
                                        <Phone class="h-4 w-4 text-gray-400" />
                                        <span>{{ user.phone }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- ==================== Role ==================== -->

                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-medium capitalize text-gray-700"
                                >
                                    {{ roleNames(user) }}
                                </span>
                            </td>

                            <!-- ==================== Email Status ==================== -->

                            <td class="px-6 py-4">
                                <span
                                    v-if="user.email_verified_at"
                                    class="inline-flex rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700"
                                >
                                    Verified
                                </span>

                                <span
                                    v-else
                                    class="inline-flex rounded-full bg-yellow-50 px-3 py-1 text-xs font-medium text-yellow-700"
                                >
                                    Unverified
                                </span>
                            </td>

                            <!-- ==================== Joined ==================== -->

                            <td
                                class="whitespace-nowrap px-6 py-4 text-sm text-gray-500"
                            >
                                {{ formatDate(user.created_at) }}
                            </td>

                            <!-- ==================== Action ==================== -->

                            <td class="px-6 py-4 text-right">
                                <Link
                                    :href="`/admin/users/${user.id}`"
                                    class="text-sm font-semibold text-gray-900 hover:underline"
                                >
                                    View
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- ==================== Empty State ==================== -->

            <div v-if="props.users.length === 0" class="px-6 py-12 text-center">
                <UserRound class="mx-auto h-10 w-10 text-gray-300" />

                <p class="mt-3 text-sm font-medium text-gray-900">
                    No users found.
                </p>
            </div>
        </div>

        <!-- ==================== Mobile Cards ==================== -->

        <div class="space-y-4 lg:hidden">
            <div
                v-for="user in props.users"
                :key="user.id"
                class="rounded-2xl border border-gray-200 bg-white p-4"
            >
                <!-- ==================== User Header ==================== -->

                <div class="flex items-center gap-3">
                    <img
                        v-if="user.profile_image"
                        :src="user.profile_image"
                        :alt="fullName(user)"
                        class="h-12 w-12 rounded-full object-cover"
                    />

                    <div
                        v-else
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gray-100"
                    >
                        <UserRound class="h-5 w-5 text-gray-500" />
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-gray-900">
                            {{ fullName(user) }}
                        </p>

                        <p class="text-xs text-gray-400">User #{{ user.id }}</p>
                    </div>

                    <span
                        class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium capitalize text-gray-700"
                    >
                        {{ roleNames(user) }}
                    </span>
                </div>

                <!-- ==================== Contact ==================== -->

                <div class="mt-4 space-y-2 border-t border-gray-100 pt-4">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <Mail class="h-4 w-4 shrink-0 text-gray-400" />
                        <span class="truncate">{{ user.email }}</span>
                    </div>

                    <div
                        v-if="user.phone"
                        class="flex items-center gap-2 text-sm text-gray-600"
                    >
                        <Phone class="h-4 w-4 shrink-0 text-gray-400" />
                        <span>{{ user.phone }}</span>
                    </div>
                </div>

                <!-- ==================== Status + Date ==================== -->

                <div
                    class="mt-4 flex items-center justify-between border-t border-gray-100 pt-4"
                >
                    <div>
                        <span
                            v-if="user.email_verified_at"
                            class="rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700"
                        >
                            Verified
                        </span>

                        <span
                            v-else
                            class="rounded-full bg-yellow-50 px-3 py-1 text-xs font-medium text-yellow-700"
                        >
                            Unverified
                        </span>
                    </div>

                    <span class="text-xs text-gray-400">
                        {{ formatDate(user.created_at) }}
                    </span>
                </div>

                <!-- ==================== Action ==================== -->

                <Link
                    :href="`/admin/users/${user.id}`"
                    class="mt-4 block w-full rounded-xl bg-gray-900 px-4 py-3 text-center text-sm font-semibold text-white transition hover:bg-gray-800"
                >
                    View User
                </Link>
            </div>

            <!-- ==================== Mobile Empty State ==================== -->

            <div
                v-if="props.users.length === 0"
                class="rounded-2xl border border-gray-200 bg-white px-6 py-12 text-center"
            >
                <UserRound class="mx-auto h-10 w-10 text-gray-300" />

                <p class="mt-3 text-sm font-medium text-gray-900">
                    No users found.
                </p>
            </div>
        </div>
    </AdminLayout>
</template>
