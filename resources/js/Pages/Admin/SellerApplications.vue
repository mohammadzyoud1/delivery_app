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
import { t } from "@/Utils/i18n";

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
            class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
        >
            <div>
                <p
                    class="text-xs font-bold uppercase tracking-[0.16em] text-brand-600"
                >
                    {{ t("adminPanel") }}
                </p>

                <h1 class="mt-1 text-2xl font-bold tracking-tight text-heading">
                    {{ t("sellerApplications") }}
                </h1>

                <p class="mt-1 max-w-2xl text-sm text-muted">
                    {{ t("sellerApplicationsDescription") }}
                </p>
            </div>

            <div
                class="inline-flex w-fit items-center rounded-full border border-border bg-surface px-4 py-2 text-sm font-semibold text-body shadow-sm"
            >
                {{ props.applications.length }} {{ t("applications") }}
            </div>
        </div>

        <!-- ==================== Desktop Table ==================== -->

        <div
            class="hidden overflow-hidden rounded-2xl border border-border bg-surface shadow-sm lg:block"
        >
            <div class="overflow-x-auto">
                <table class="w-full min-w-[950px] text-left">
                    <thead class="border-b border-border bg-background">
                        <tr>
                            <th
                                class="px-6 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("applicant") }}
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("reason") }}
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("status") }}
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("submitDate") }}
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("reviewedBy") }}
                            </th>

                            <th class="px-6 py-4"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-border">
                        <tr
                            v-for="application in props.applications"
                            :key="application.id"
                            class="transition-colors hover:bg-background"
                        >
                            <!-- ==================== Applicant ==================== -->

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600"
                                    >
                                        <UserRound class="h-5 w-5" />
                                    </div>

                                    <div class="min-w-0">
                                        <p
                                            class="truncate text-sm font-semibold text-heading"
                                        >
                                            {{ fullName(application.user) }}
                                        </p>

                                        <p
                                            class="mt-0.5 truncate text-xs text-muted"
                                        >
                                            {{ application.user.email }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- ==================== Reason ==================== -->

                            <td class="max-w-xs px-6 py-4">
                                <p
                                    class="truncate text-sm text-body"
                                    :title="
                                        application.reason ??
                                        'No reason provided'
                                    "
                                >
                                    {{
                                        application.reason ??
                                        t("noReasonProvided")
                                    }}
                                </p>
                            </td>

                            <!-- ==================== Status ==================== -->

                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-bold capitalize"
                                    :class="{
                                        'border-yellow-100 bg-yellow-50 text-yellow-700':
                                            application.status === 'pending',
                                        'border-green-100 bg-green-50 text-green-700':
                                            application.status === 'approved',
                                        'border-red-100 bg-red-50 text-red-700':
                                            application.status === 'rejected',
                                        'border-border bg-background text-body':
                                            ![
                                                'pending',
                                                'approved',
                                                'rejected',
                                            ].includes(application.status),
                                    }"
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

                                    {{ t(application.status) }}
                                </span>
                            </td>

                            <!-- ==================== Submitted ==================== -->

                            <td class="whitespace-nowrap px-6 py-4">
                                <div
                                    class="flex items-center gap-2 text-sm text-body"
                                >
                                    <CalendarDays class="h-4 w-4 text-muted" />

                                    {{ formatDate(application.created_at) }}
                                </div>
                            </td>

                            <!-- ==================== Reviewed By ==================== -->

                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-body">
                                    {{ reviewerName(application) }}
                                </p>

                                <p
                                    v-if="application.reviewed_at"
                                    class="mt-1 text-xs text-muted"
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
                                    class="inline-flex items-center rounded-xl border border-border px-3 py-2 text-sm font-semibold text-body transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700 focus-visible:outline-none"
                                >
                                    {{ t("view") }}
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- ==================== Desktop Empty State ==================== -->

            <div
                v-if="props.applications.length === 0"
                class="px-6 py-14 text-center"
            >
                <div
                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"
                >
                    <UserRound class="h-6 w-6" />
                </div>

                <h3 class="mt-4 text-base font-semibold text-heading">
                    {{ t("noSellerApplications") }}
                </h3>

                <p class="mt-1 text-sm text-muted">
                    {{ t("noSellerApplicationsDescription") }}
                </p>
            </div>
        </div>

        <!-- ==================== Mobile Cards ==================== -->

        <div class="space-y-4 lg:hidden">
            <div
                v-for="application in props.applications"
                :key="application.id"
                class="rounded-2xl border border-border bg-surface p-5 shadow-sm"
            >
                <!-- ==================== Applicant Header ==================== -->

                <div class="flex items-start gap-3">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600"
                    >
                        <UserRound class="h-5 w-5" />
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-heading">
                            {{ fullName(application.user) }}
                        </p>

                        <p class="mt-0.5 truncate text-xs text-muted">
                            {{ application.user.email }}
                        </p>
                    </div>

                    <span
                        class="shrink-0 inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs font-bold capitalize"
                        :class="{
                            'border-yellow-100 bg-yellow-50 text-yellow-700':
                                application.status === 'pending',
                            'border-green-100 bg-green-50 text-green-700':
                                application.status === 'approved',
                            'border-red-100 bg-red-50 text-red-700':
                                application.status === 'rejected',
                            'border-border bg-background text-body': ![
                                'pending',
                                'approved',
                                'rejected',
                            ].includes(application.status),
                        }"
                    >
                        <Clock
                            v-if="application.status === 'pending'"
                            class="h-3.5 w-3.5"
                        />

                        <CheckCircle
                            v-else-if="application.status === 'approved'"
                            class="h-3.5 w-3.5"
                        />

                        <XCircle
                            v-else-if="application.status === 'rejected'"
                            class="h-3.5 w-3.5"
                        />

                        {{ t(application.status) }}
                    </span>
                </div>

                <!-- ==================== Reason ==================== -->

                <div class="mt-5 border-t border-border pt-4">
                    <p
                        class="text-[11px] font-bold uppercase tracking-wide text-muted"
                    >
                        {{ t("reason") }}
                    </p>

                    <p class="mt-1.5 text-sm leading-6 text-body">
                        {{ application.reason ?? t("noReasonProvided") }}
                    </p>
                </div>

                <!-- ==================== Metadata ==================== -->

                <div
                    class="mt-4 grid grid-cols-1 gap-3 border-t border-border pt-4 sm:grid-cols-2"
                >
                    <div>
                        <p
                            class="text-[11px] font-bold uppercase tracking-wide text-muted"
                        >
                            {{ t("submitDate") }}
                        </p>

                        <div
                            class="mt-1 flex items-center gap-1.5 text-sm font-medium text-body"
                        >
                            <CalendarDays class="h-4 w-4 text-muted" />

                            {{ formatDate(application.created_at) }}
                        </div>
                    </div>

                    <div>
                        <p
                            class="text-[11px] font-bold uppercase tracking-wide text-muted"
                        >
                            {{ t("reviewedBy") }}
                        </p>

                        <p class="mt-1 text-sm font-medium text-body">
                            {{ reviewerName(application) }}
                        </p>

                        <p
                            v-if="application.reviewed_at"
                            class="mt-0.5 text-xs text-muted"
                        >
                            {{ formatDateTime(application.reviewed_at) }}
                        </p>
                    </div>
                </div>

                <!-- ==================== Action ==================== -->

                <Link
                    :href="`/admin/seller-applications/${application.id}`"
                    class="mt-5 block w-full rounded-xl bg-brand-600 px-4 py-3 text-center text-sm font-bold text-white transition hover:bg-brand-700 focus-visible:outline-none"
                >
                    {{ t("view") }}
                </Link>
            </div>

            <!-- ==================== Mobile Empty State ==================== -->

            <div
                v-if="props.applications.length === 0"
                class="rounded-2xl border border-border bg-surface px-6 py-14 text-center shadow-sm"
            >
                <div
                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"
                >
                    <UserRound class="h-6 w-6" />
                </div>

                <h3 class="mt-4 text-base font-semibold text-heading">
                    {{ t("noSellerApplications") }}
                </h3>

                <p class="mt-1 text-sm text-muted">
                    {{ t("noSellerApplicationsDescription") }}
                </p>
            </div>
        </div>
    </AdminLayout>
</template>
