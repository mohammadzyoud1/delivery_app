<script setup lang="ts">
// ==================== Imports ====================

import { Head, Link } from "@inertiajs/vue3";
import {
    CalendarDays,
    CheckCircle,
    Clock,
    UserRound,
    XCircle,
} from "lucide-vue-next";
import AdminLayout from "@/Layouts/AdminLayout.vue";

// ==================== Interfaces ====================

interface User {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone: string | null;
}

interface Reviewer {
    id: number;
    first_name: string;
    last_name: string;
}

interface SellerApplication {
    id: number;
    user_id: number;
    status: string;
    reason: string | null;
    reviewed_by: number | null;
    reviewed_at: string | null;
    created_at: string;
    user: User;
    reviewer?: Reviewer;
}

// ==================== Props ====================

const props = defineProps<{
    applications: SellerApplication[];
}>();

// ==================== Helpers ====================

const fullName = (user: Reviewer): string => {
    return `${user.first_name} ${user.last_name}`.trim();
};

const reviewerName = (application: SellerApplication): string => {
    if (!application.reviewer) {
        return "—";
    }

    return fullName(application.reviewer);
};

const formatDate = (date: string): string => {
    return new Date(date).toLocaleDateString();
};

const formatDateTime = (date: string): string => {
    return new Date(date).toLocaleString();
};

const formatStatus = (status: string): string => {
    return status.replace(/_/g, " ");
};

const statusClasses = (status: string): string => {
    switch (status) {
        case "pending":
            return "bg-yellow-50 text-yellow-700";

        case "approved":
            return "bg-green-50 text-green-700";

        case "rejected":
            return "bg-red-50 text-red-700";

        default:
            return "bg-gray-100 text-gray-700";
    }
};
</script>

<template>
    <Head title="Seller Applications" />

    <AdminLayout>
        <!-- ==================== Page Header ==================== -->

        <div
            class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Seller Applications
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Review users who have applied to become sellers.
                </p>
            </div>

            <div
                class="rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700"
            >
                {{ props.applications.length }} applications
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
                                Applicant
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Reason
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Status
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Submitted
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Reviewed By
                            </th>

                            <th class="px-6 py-4"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        <tr
                            v-for="application in props.applications"
                            :key="application.id"
                            class="transition hover:bg-gray-50"
                        >
                            <!-- ==================== Applicant ==================== -->

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
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
                                            {{ fullName(application.user) }}
                                        </p>

                                        <p class="text-xs text-gray-400">
                                            {{ application.user.email }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- ==================== Reason ==================== -->

                            <td class="max-w-xs px-6 py-4">
                                <p
                                    class="truncate text-sm text-gray-600"
                                    :title="
                                        application.reason ??
                                        'No reason provided'
                                    "
                                >
                                    {{
                                        application.reason ??
                                        "No reason provided"
                                    }}
                                </p>
                            </td>

                            <!-- ==================== Status ==================== -->

                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium capitalize"
                                    :class="statusClasses(application.status)"
                                >
                                    <Clock
                                        v-if="application.status === 'pending'"
                                        class="h-3.5 w-3.5"
                                    />

                                    <CheckCircle
                                        v-else-if="
                                            application.status === 'approved'
                                        "
                                        class="h-3.5 w-3.5"
                                    />

                                    <XCircle
                                        v-else-if="
                                            application.status === 'rejected'
                                        "
                                        class="h-3.5 w-3.5"
                                    />

                                    {{ formatStatus(application.status) }}
                                </span>
                            </td>

                            <!-- ==================== Submitted ==================== -->

                            <td class="whitespace-nowrap px-6 py-4">
                                <div
                                    class="flex items-center gap-2 text-sm text-gray-500"
                                >
                                    <CalendarDays
                                        class="h-4 w-4 text-gray-400"
                                    />

                                    {{ formatDate(application.created_at) }}
                                </div>
                            </td>

                            <!-- ==================== Reviewed By ==================== -->

                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">
                                    {{ reviewerName(application) }}
                                </span>

                                <p
                                    v-if="application.reviewed_at"
                                    class="mt-1 text-xs text-gray-400"
                                >
                                    {{
                                        formatDateTime(application.reviewed_at)
                                    }}
                                </p>
                            </td>

                            <!-- ==================== Action ==================== -->

                            <td class="px-6 py-4 text-right">
                                <Link
                                    :href="`/admin/seller-applications/${application.id}`"
                                    class="text-sm font-semibold text-gray-900 hover:underline"
                                >
                                    View
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- ==================== Desktop Empty State ==================== -->

            <div
                v-if="props.applications.length === 0"
                class="px-6 py-12 text-center"
            >
                <UserRound class="mx-auto h-10 w-10 text-gray-300" />

                <p class="mt-3 text-sm font-medium text-gray-900">
                    No seller applications found.
                </p>
            </div>
        </div>

        <!-- ==================== Mobile Cards ==================== -->

        <div class="space-y-4 lg:hidden">
            <div
                v-for="application in props.applications"
                :key="application.id"
                class="rounded-2xl border border-gray-200 bg-white p-4"
            >
                <!-- ==================== Applicant ==================== -->

                <div class="flex items-center gap-3">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gray-100"
                    >
                        <UserRound class="h-5 w-5 text-gray-500" />
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-gray-900">
                            {{ fullName(application.user) }}
                        </p>

                        <p class="truncate text-xs text-gray-400">
                            {{ application.user.email }}
                        </p>
                    </div>

                    <span
                        class="shrink-0 rounded-full px-3 py-1 text-xs font-medium capitalize"
                        :class="statusClasses(application.status)"
                    >
                        {{ formatStatus(application.status) }}
                    </span>
                </div>

                <!-- ==================== Reason ==================== -->

                <div class="mt-4 border-t border-gray-100 pt-4">
                    <p
                        class="text-xs font-medium uppercase tracking-wide text-gray-400"
                    >
                        Reason
                    </p>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ application.reason ?? "No reason provided" }}
                    </p>
                </div>

                <!-- ==================== Metadata ==================== -->

                <div
                    class="mt-4 flex flex-wrap gap-x-5 gap-y-2 border-t border-gray-100 pt-4 text-xs text-gray-400"
                >
                    <span>
                        Submitted:
                        {{ formatDate(application.created_at) }}
                    </span>

                    <span v-if="application.reviewed_at">
                        Reviewed:
                        {{ formatDate(application.reviewed_at) }}
                    </span>
                </div>

                <!-- ==================== Action ==================== -->

                <Link
                    :href="`/admin/seller-applications/${application.id}`"
                    class="mt-4 block w-full rounded-xl bg-gray-900 px-4 py-3 text-center text-sm font-semibold text-white transition hover:bg-gray-800"
                >
                    View Application
                </Link>
            </div>

            <!-- ==================== Mobile Empty State ==================== -->

            <div
                v-if="props.applications.length === 0"
                class="rounded-2xl border border-gray-200 bg-white px-6 py-12 text-center"
            >
                <UserRound class="mx-auto h-10 w-10 text-gray-300" />

                <p class="mt-3 text-sm font-medium text-gray-900">
                    No seller applications found.
                </p>
            </div>
        </div>
    </AdminLayout>
</template>
