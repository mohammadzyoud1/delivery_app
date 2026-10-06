<script setup lang="ts">
// ==================== Imports ====================

import { Head, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Button from "@/Components/UI/Button.vue";
import {
    ArrowLeft,
    CalendarDays,
    CheckCircle2,
    Clock3,
    FileText,
    UserRound,
    XCircle,
} from "lucide-vue-next";
import { t } from "@/Utils/i18n";

// ==================== Interfaces ====================

interface User {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone: string | null;
    profile_image: string | null;
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
    reason: string;
    rejection_reason: string | null;
    reviewed_at: string | null;
    created_at: string;
    user: User;
    reviewer?: Reviewer;
}

// ==================== Props ====================

const props = defineProps<{
    application: SellerApplication;
}>();

// ==================== Action State ====================

const rejecting = ref(false);
const rejectionReason = ref("");

// ==================== Approve Application ====================

const approveApplication = () => {
    router.patch(
        `/admin/seller-applications/${props.application.id}/approve`,
        {},
        {
            preserveScroll: true,
        },
    );
};

// ==================== Reject Application ====================

const rejectApplication = () => {
    if (!rejectionReason.value.trim()) {
        return;
    }

    rejecting.value = true;

    router.patch(
        `/admin/seller-applications/${props.application.id}/reject`,
        {
            rejection_reason: rejectionReason.value,
        },
        {
            preserveScroll: true,
            onFinish: () => {
                rejecting.value = false;
            },
        },
    );
};

// ==================== Helpers ====================

const fullName = (user: User | Reviewer): string => {
    return `${user.first_name} ${user.last_name}`;
};

const formatDate = (date: string | null): string => {
    if (!date) {
        return "—";
    }

    return new Date(date).toLocaleString();
};

const statusLabel = (status: string): string => {
    return status.charAt(0).toUpperCase() + status.slice(1);
};
</script>

<template>
    <Head title="Seller Application Details" />

    <AdminLayout>
        <div class="mx-auto max-w-6xl space-y-6">
            <!-- ==================== Header ==================== -->

            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex items-center gap-3">
                    <Link
                        href="/admin/seller-applications"
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-border bg-surface text-muted shadow-sm transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700"
                    >
                        <ArrowLeft class="h-5 w-5" />
                    </Link>

                    <div>
                        <p
                            class="text-xs font-bold uppercase tracking-[0.16em] text-brand-600"
                        >
                            {{ t("sellerApplications") }}
                        </p>

                        <h1
                            class="mt-1 text-2xl font-bold tracking-tight text-heading"
                        >
                            {{ t("application") }} #{{ application.id }}
                        </h1>
                    </div>
                </div>

                <!-- ==================== Header Status ==================== -->

                <div>
                    <span
                        v-if="application.status === 'approved'"
                        class="inline-flex items-center gap-1.5 rounded-full border border-green-100 bg-green-50 px-3 py-1.5 text-xs font-bold text-green-700"
                    >
                        <CheckCircle2 class="h-4 w-4" />
                        {{ t("approved") }}
                    </span>

                    <span
                        v-else-if="application.status === 'rejected'"
                        class="inline-flex items-center gap-1.5 rounded-full border border-red-100 bg-red-50 px-3 py-1.5 text-xs font-bold text-red-700"
                    >
                        <XCircle class="h-4 w-4" />
                        {{ t("rejected") }}
                    </span>

                    <span
                        v-else
                        class="inline-flex items-center gap-1.5 rounded-full border border-yellow-100 bg-yellow-50 px-3 py-1.5 text-xs font-bold text-yellow-700"
                    >
                        <Clock3 class="h-4 w-4" />
                        {{ t(application.status) }}
                    </span>
                </div>
            </div>

            <!-- ==================== Main Information ==================== -->

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- ==================== Applicant ==================== -->

                <section
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                        >
                            <UserRound class="h-5 w-5" />
                        </div>

                        <div>
                            <p
                                class="text-xs font-bold uppercase tracking-wide text-brand-600"
                            >
                                {{ t("applicant") }}
                            </p>

                            <h2
                                class="mt-0.5 text-lg font-semibold text-heading"
                            >
                                {{ t("userInfo") }}
                            </h2>
                        </div>
                    </div>

                    <div class="mt-6 space-y-5">
                        <!-- ==================== Name ==================== -->

                        <div>
                            <p
                                class="text-[11px] font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("name") }}
                            </p>

                            <p class="mt-1 text-sm font-semibold text-heading">
                                {{ fullName(application.user) }}
                            </p>
                        </div>

                        <!-- ==================== Email ==================== -->

                        <div>
                            <p
                                class="text-[11px] font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("email") }}
                            </p>

                            <p
                                class="mt-1 break-all text-sm font-medium text-body"
                            >
                                {{ application.user.email }}
                            </p>
                        </div>

                        <!-- ==================== Phone ==================== -->

                        <div>
                            <p
                                class="text-[11px] font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("phone") }}
                            </p>

                            <p class="mt-1 text-sm font-medium text-body">
                                {{ application.user.phone || "—" }}
                            </p>
                        </div>

                        <!-- ==================== View User ==================== -->

                        <Link
                            :href="`/admin/users/${application.user.id}`"
                            class="inline-flex w-full items-center justify-center rounded-xl border border-border px-4 py-2.5 text-sm font-semibold text-body transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700"
                        >
                            {{ t("view") }}
                        </Link>
                    </div>
                </section>

                <!-- ==================== Application Information ==================== -->

                <section
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6 lg:col-span-2"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                        >
                            <FileText class="h-5 w-5" />
                        </div>

                        <div>
                            <p
                                class="text-xs font-bold uppercase tracking-wide text-brand-600"
                            >
                                {{ t("application") }}
                            </p>

                            <h2
                                class="mt-0.5 text-lg font-semibold text-heading"
                            >
                                {{ t("applicationInfo") }}
                            </h2>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        <!-- ==================== Status ==================== -->

                        <div
                            class="rounded-xl border border-border bg-background p-4"
                        >
                            <p
                                class="text-[11px] font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("status") }}
                            </p>

                            <div class="mt-2">
                                <span
                                    v-if="application.status === 'approved'"
                                    class="inline-flex items-center gap-1.5 rounded-full border border-green-100 bg-green-50 px-3 py-1 text-xs font-bold text-green-700"
                                >
                                    <CheckCircle2 class="h-4 w-4" />
                                    {{ t("approved") }}
                                </span>

                                <span
                                    v-else-if="
                                        application.status === 'rejected'
                                    "
                                    class="inline-flex items-center gap-1.5 rounded-full border border-red-100 bg-red-50 px-3 py-1 text-xs font-bold text-red-700"
                                >
                                    <XCircle class="h-4 w-4" />
                                    {{ t("rejected") }}
                                </span>

                                <span
                                    v-else
                                    class="inline-flex items-center gap-1.5 rounded-full border border-yellow-100 bg-yellow-50 px-3 py-1 text-xs font-bold text-yellow-700"
                                >
                                    <Clock3 class="h-4 w-4" />
                                    {{ t(application.status) }}
                                </span>
                            </div>
                        </div>

                        <!-- ==================== Submitted ==================== -->

                        <div
                            class="rounded-xl border border-border bg-background p-4"
                        >
                            <p
                                class="text-[11px] font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("submitDate") }}
                            </p>

                            <div
                                class="mt-2 flex items-center gap-2 text-sm font-semibold text-heading"
                            >
                                <CalendarDays class="h-4 w-4 text-muted" />
                                {{ formatDate(application.created_at) }}
                            </div>
                        </div>

                        <!-- ==================== Reviewed ==================== -->

                        <div
                            class="rounded-xl border border-border bg-background p-4"
                        >
                            <p
                                class="text-[11px] font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("reviewDate") }}
                            </p>

                            <p class="mt-2 text-sm font-semibold text-heading">
                                {{ formatDate(application.reviewed_at) }}
                            </p>
                        </div>

                        <!-- ==================== Reviewer ==================== -->

                        <div
                            class="rounded-xl border border-border bg-background p-4"
                        >
                            <p
                                class="text-[11px] font-bold uppercase tracking-wide text-muted"
                            >
                                {{ t("reviewedBy") }}
                            </p>

                            <p class="mt-2 text-sm font-semibold text-heading">
                                {{
                                    application.reviewer
                                        ? fullName(application.reviewer)
                                        : "—"
                                }}
                            </p>
                        </div>
                    </div>
                </section>
            </div>

            <!-- ==================== Application Reason ==================== -->

            <section
                class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                    >
                        <FileText class="h-5 w-5" />
                    </div>

                    <div>
                        <p
                            class="text-xs font-bold uppercase tracking-wide text-brand-600"
                        >
                            {{ t("applicantStatement") }}
                        </p>

                        <h2 class="mt-0.5 text-lg font-semibold text-heading">
                            {{ t("statement") }}
                        </h2>
                    </div>
                </div>

                <div
                    class="mt-5 rounded-xl border border-border bg-background p-4 sm:p-5"
                >
                    <p class="whitespace-pre-line text-sm leading-7 text-body">
                        {{ application.reason }}
                    </p>
                </div>
            </section>

            <!-- ==================== Rejection Reason ==================== -->

            <section
                v-if="
                    application.status === 'rejected' &&
                    application.rejection_reason
                "
                class="rounded-2xl border border-red-200 bg-red-50 p-5 sm:p-6"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-red-600"
                    >
                        <XCircle class="h-5 w-5" />
                    </div>

                    <div>
                        <p
                            class="text-xs font-bold uppercase tracking-wide text-red-600"
                        >
                            {{ t("reviewResult") }}
                        </p>

                        <h2 class="mt-0.5 text-lg font-semibold text-red-900">
                            {{ t("rejectionReason") }}
                        </h2>
                    </div>
                </div>

                <div
                    class="mt-5 rounded-xl border border-red-100 bg-white p-4 sm:p-5"
                >
                    <p
                        class="whitespace-pre-line text-sm leading-7 text-red-800"
                    >
                        {{ application.rejection_reason }}
                    </p>
                </div>
            </section>

            <!-- ==================== Application Actions ==================== -->

            <section
                v-if="application.status === 'pending'"
                class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
            >
                <div>
                    <p
                        class="text-xs font-bold uppercase tracking-wide text-brand-600"
                    >
                        {{ t("pendingReview") }}
                    </p>

                    <h2 class="mt-1 text-lg font-semibold text-heading">
                        {{ t("reviewApplication") }}
                    </h2>

                    <p class="mt-1 text-sm text-muted">
                        {{ t("approveOrRejectApplicant") }}
                    </p>
                </div>

                <!-- ==================== Approve ==================== -->

                <div class="mt-6">
                    <Button
                        type="button"
                        class="w-full rounded-xl bg-green-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-green-700"
                        @click="approveApplication"
                    >
                        <CheckCircle2 class="mr-2 inline-block h-4 w-4" />
                        {{ t("approveApplication") }}
                    </Button>
                </div>

                <!-- ==================== Reject ==================== -->

                <div class="mt-6 border-t border-border pt-6">
                    <label
                        for="rejection_reason"
                        class="block text-sm font-semibold text-heading"
                    >
                        {{ t("rejectionReason") }}
                    </label>

                    <p class="mt-1 text-xs text-muted">
                        {{ t("explainRejectionReason") }}
                    </p>

                    <textarea
                        id="rejection_reason"
                        v-model="rejectionReason"
                        rows="4"
                        class="mt-3 w-full resize-none rounded-xl border border-border bg-surface px-4 py-3 text-sm text-heading placeholder:text-muted focus:border-brand-500 focus:ring-2 focus:ring-brand-100 focus:outline-none"
                    ></textarea>

                    <Button
                        type="button"
                        class="mt-3 w-full rounded-xl bg-red-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="!rejectionReason.trim() || rejecting"
                        @click="rejectApplication"
                    >
                        <XCircle class="mr-2 inline-block h-4 w-4" />
                        {{
                            rejecting ? t("Rejecting") : t("rejectApplication")
                        }}
                    </Button>
                </div>
            </section>
        </div>
    </AdminLayout>
</template>
