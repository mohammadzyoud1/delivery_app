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

            <div class="flex items-center gap-3">
                <Link
                    href="/admin/seller-applications"
                    class="rounded-xl p-2 text-gray-600 transition hover:bg-gray-100"
                >
                    <ArrowLeft class="h-5 w-5" />
                </Link>

                <div>
                    <p
                        class="text-xs font-medium uppercase tracking-wide text-gray-400"
                    >
                        Seller Application
                    </p>

                    <h1 class="text-2xl font-bold text-gray-900">
                        Application #{{ application.id }}
                    </h1>
                </div>
            </div>

            <!-- ==================== Main Content ==================== -->

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- ==================== Applicant ==================== -->

                <div
                    class="rounded-2xl border border-gray-200 bg-white p-5 lg:col-span-1"
                >
                    <div class="mb-5 flex items-center gap-2">
                        <UserRound class="h-5 w-5 text-gray-500" />

                        <h2 class="font-semibold text-gray-900">Applicant</h2>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-gray-400">Name</p>
                            <p class="mt-1 font-medium text-gray-900">
                                {{ fullName(application.user) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-400">Email</p>
                            <p class="mt-1 break-all text-sm text-gray-700">
                                {{ application.user.email }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-400">Phone</p>
                            <p class="mt-1 text-sm text-gray-700">
                                {{ application.user.phone || "—" }}
                            </p>
                        </div>

                        <div>
                            <Link
                                :href="`/admin/users/${application.user.id}`"
                                class="inline-flex items-center text-sm font-semibold text-gray-900 hover:underline"
                            >
                                View User
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- ==================== Application Information ==================== -->

                <div
                    class="rounded-2xl border border-gray-200 bg-white p-5 lg:col-span-2"
                >
                    <div class="mb-5 flex items-center gap-2">
                        <FileText class="h-5 w-5 text-gray-500" />

                        <h2 class="font-semibold text-gray-900">
                            Application Information
                        </h2>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <!-- Status -->

                        <div
                            class="rounded-xl border border-gray-100 bg-gray-50 p-4"
                        >
                            <p class="text-xs text-gray-400">Status</p>

                            <div class="mt-2">
                                <span
                                    v-if="application.status === 'approved'"
                                    class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700"
                                >
                                    <CheckCircle2 class="h-4 w-4" />
                                    Approved
                                </span>

                                <span
                                    v-else-if="
                                        application.status === 'rejected'
                                    "
                                    class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700"
                                >
                                    <XCircle class="h-4 w-4" />
                                    Rejected
                                </span>

                                <span
                                    v-else
                                    class="inline-flex items-center gap-1.5 rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700"
                                >
                                    <Clock3 class="h-4 w-4" />
                                    {{ statusLabel(application.status) }}
                                </span>
                            </div>
                        </div>

                        <!-- Submitted -->

                        <div
                            class="rounded-xl border border-gray-100 bg-gray-50 p-4"
                        >
                            <p class="text-xs text-gray-400">Submitted</p>

                            <div
                                class="mt-2 flex items-center gap-2 text-sm font-medium text-gray-900"
                            >
                                <CalendarDays class="h-4 w-4 text-gray-400" />
                                {{ formatDate(application.created_at) }}
                            </div>
                        </div>

                        <!-- Reviewed -->

                        <div
                            class="rounded-xl border border-gray-100 bg-gray-50 p-4"
                        >
                            <p class="text-xs text-gray-400">Reviewed</p>

                            <div class="mt-2 text-sm font-medium text-gray-900">
                                {{ formatDate(application.reviewed_at) }}
                            </div>
                        </div>

                        <!-- Reviewer -->

                        <div
                            class="rounded-xl border border-gray-100 bg-gray-50 p-4"
                        >
                            <p class="text-xs text-gray-400">Reviewed By</p>

                            <div class="mt-2 text-sm font-medium text-gray-900">
                                {{
                                    application.reviewer
                                        ? fullName(application.reviewer)
                                        : "—"
                                }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==================== Application Reason ==================== -->

            <div class="rounded-2xl border border-gray-200 bg-white p-5">
                <div class="mb-4 flex items-center gap-2">
                    <FileText class="h-5 w-5 text-gray-500" />

                    <h2 class="font-semibold text-gray-900">
                        Application Reason
                    </h2>
                </div>

                <div
                    class="rounded-xl bg-gray-50 p-4 text-sm leading-7 text-gray-700"
                >
                    {{ application.reason }}
                </div>
            </div>

            <!-- ==================== Rejection Reason ==================== -->

            <div
                v-if="
                    application.status === 'rejected' &&
                    application.rejection_reason
                "
                class="rounded-2xl border border-red-200 bg-red-50 p-5"
            >
                <div class="mb-4 flex items-center gap-2">
                    <XCircle class="h-5 w-5 text-red-600" />

                    <h2 class="font-semibold text-red-900">Rejection Reason</h2>
                </div>

                <div
                    class="rounded-xl bg-white p-4 text-sm leading-7 text-red-800"
                >
                    {{ application.rejection_reason }}
                </div>
            </div>
            <!-- ==================== Application Actions ==================== -->

            <div
                v-if="application.status === 'pending'"
                class="rounded-2xl border border-gray-200 bg-white p-5"
            >
                <div class="mb-5">
                    <h2 class="font-semibold text-gray-900">
                        Review Application
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Approve the applicant or provide a reason for rejection.
                    </p>
                </div>

                <!-- ==================== Approve ==================== -->

                <div class="mb-6">
                    <Button
                        type="button"
                        class="w-full rounded-xl bg-green-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-green-700"
                        @click="approveApplication"
                    >
                        Approve Application
                    </Button>
                </div>

                <!-- ==================== Reject ==================== -->

                <div class="border-t border-gray-100 pt-6">
                    <label
                        for="rejection_reason"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Rejection Reason
                    </label>

                    <textarea
                        id="rejection_reason"
                        v-model="rejectionReason"
                        rows="4"
                        class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-gray-400 focus:ring-0"
                        placeholder="Explain why this application is being rejected..."
                    />

                    <Button
                        type="button"
                        class="mt-3 w-full rounded-xl bg-red-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="!rejectionReason.trim() || rejecting"
                        @click="rejectApplication"
                    >
                        {{ rejecting ? "Rejecting..." : "Reject Application" }}
                    </Button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
