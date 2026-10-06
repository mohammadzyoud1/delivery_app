<script setup lang="ts">
// ==================== Imports ====================

import { useForm } from "@inertiajs/vue3";
import { ArrowLeft, FileText, Store } from "lucide-vue-next";
import { computed } from "vue";
import { t } from "@/Utils/i18n";

// ==================== Types ====================

interface SellerApplication {
    id: number;
    status: "pending" | "approved" | "rejected";
    reason: string;
    rejection_reason: string | null;
    created_at: string;
}

interface Props {
    application: SellerApplication | null;
    isSeller: boolean;
}
const props = defineProps<Props>();

// ==================== Form ====================

const form = useForm({
    reason: "",
});

// ==================== Application State ====================

const isPending = computed(() => props.application?.status === "pending");

const isApproved = computed(
    () => props.application?.status === "approved" && props.isSeller,
);
const isRejected = computed(() => props.application?.status === "rejected");
// ==================== Submit Application ====================

const submitApplication = () => {
    form.post("/seller/apply", {
        preserveScroll: true,
    });
};
</script>

<template>
    <!-- ==================== Page ==================== -->

    <div class="min-h-screen bg-background">
        <!-- ==================== Header ==================== -->

        <header
            class="sticky top-0 z-40 border-b border-border bg-surface/95 backdrop-blur"
        >
            <div
                class="mx-auto flex h-16 w-full max-w-3xl items-center gap-3 px-5 sm:px-8"
            >
                <button
                    type="button"
                    @click="$inertia.visit('/')"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-body transition-all duration-200 hover:bg-background hover:text-heading focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500"
                    aria-label="Go back"
                >
                    <ArrowLeft :size="20" :stroke-width="2" />
                </button>

                <div>
                    <p class="text-xs font-medium text-muted"></p>

                    <h1
                        class="text-lg font-extrabold tracking-tight text-heading"
                    >
                        {{ t("becomeSeller") }}
                    </h1>
                </div>
            </div>
        </header>

        <!-- ==================== Content ==================== -->

        <main class="mx-auto w-full max-w-3xl px-5 py-8 sm:px-8 sm:py-10">
            <!-- ==================== Intro ==================== -->

            <section class="mb-6">
                <div
                    class="flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"
                >
                    <Store :size="26" :stroke-width="1.8" />
                </div>

                <h2
                    class="mt-5 text-2xl font-extrabold tracking-tight text-heading sm:text-3xl"
                >
                    {{ t("startSelling") }}
                </h2>
            </section>

            <!-- ==================== Application Status ==================== -->

            <div
                v-if="isPending"
                class="rounded-2xl border border-amber-200 bg-amber-50/70 p-5 shadow-sm sm:p-6"
            >
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-700"
                >
                    <FileText :size="22" :stroke-width="2" />
                </div>

                <h3 class="mt-5 text-lg font-extrabold text-heading">
                    {{ t("applicationUnderReview") }}
                </h3>

                <p class="mt-2 text-sm leading-6 text-body">
                    {{ t("applicationUnderReviewDescription") }}
                </p>

                <div
                    class="mt-5 rounded-xl border border-amber-200/70 bg-white/70 p-4"
                >
                    <p
                        class="text-xs font-bold uppercase tracking-wide text-amber-700"
                    >
                        {{ t("status") }}
                    </p>

                    <p class="mt-1 text-sm font-semibold text-heading">
                        {{ t("pendingReview") }}
                    </p>
                </div>
            </div>

            <!-- ==================== Approved Status ==================== -->

            <div
                v-else-if="isApproved"
                class="rounded-2xl border border-green-200 bg-green-50/70 p-5 shadow-sm sm:p-6"
            >
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 text-green-700"
                >
                    <Store :size="22" :stroke-width="2" />
                </div>

                <h3 class="mt-5 text-lg font-extrabold text-heading">
                    {{ t("youAreSeller") }}
                </h3>

                <p class="mt-2 text-sm leading-6 text-body">
                    {{ t("sellerApprovedDescription") }}
                </p>

                <a
                    href="/seller"
                    class="mt-6 inline-flex w-full items-center justify-center rounded-xl bg-brand-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition-all duration-200 hover:bg-brand-700 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 sm:w-auto"
                >
                    {{ t("openSellerDashboard") }}
                </a>
            </div>

            <!-- ==================== Rejected Status ==================== -->

            <div
                v-else-if="isRejected"
                class="rounded-2xl border border-red-200 bg-red-50/60 p-5 shadow-sm sm:p-6"
            >
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-100 text-red-700"
                >
                    <FileText :size="22" :stroke-width="2" />
                </div>

                <h3 class="mt-5 text-lg font-extrabold text-heading">
                    {{ t("applicationNotApproved") }}
                </h3>

                <p class="mt-2 text-sm leading-6 text-body">
                    {{ t("applicationNotApprovedDescription") }}
                </p>

                <!-- ==================== Rejection Reason ==================== -->

                <div
                    v-if="props.application?.rejection_reason"
                    class="mt-5 rounded-xl border border-red-100 bg-white p-4"
                >
                    <p
                        class="text-xs font-bold uppercase tracking-wide text-red-600"
                    >
                        {{ t("adminReason") }}
                    </p>

                    <p class="mt-2 text-sm leading-6 text-body">
                        {{ props.application.rejection_reason }}
                    </p>
                </div>

                <!-- ==================== Reapply Form ==================== -->

                <form
                    class="mt-6 rounded-xl border border-border bg-white p-4 sm:p-5"
                    @submit.prevent="submitApplication"
                >
                    <label
                        for="reason"
                        class="mb-2 block text-sm font-bold text-heading"
                    >
                        {{ t("applyAgain") }}
                    </label>

                    <p class="mb-3 text-xs leading-5 text-muted">
                        {{ t("sellerApplicationHint") }}
                    </p>

                    <textarea
                        id="reason"
                        v-model="form.reason"
                        rows="6"
                        class="w-full resize-none rounded-xl border border-border bg-background px-4 py-3 text-sm leading-6 text-heading outline-none transition placeholder:text-muted focus:border-brand-500 focus:bg-surface focus:ring-2 focus:ring-brand-100"
                    ></textarea>

                    <p
                        v-if="form.errors.reason"
                        class="mt-2 text-sm font-medium text-red-600"
                    >
                        {{ form.errors.reason }}
                    </p>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="mt-4 w-full rounded-xl bg-brand-600 px-4 py-3 text-sm font-bold text-white shadow-sm transition-all duration-200 hover:bg-brand-700 hover:shadow-md disabled:cursor-not-allowed disabled:opacity-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2"
                    >
                        {{ form.processing ? t("submitting") : t("submit") }}
                    </button>
                </form>
            </div>

            <!-- ==================== New Application ==================== -->

            <form
                v-else
                class="overflow-hidden rounded-2xl border border-border bg-surface shadow-sm"
                @submit.prevent="submitApplication"
            >
                <!-- ==================== Form Header ==================== -->

                <div class="border-b border-border bg-background/70 p-5 sm:p-6">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                        >
                            <FileText :size="19" :stroke-width="2" />
                        </div>

                        <div>
                            <h3 class="text-base font-extrabold text-heading">
                                {{ t("sellerApplication") }}
                            </h3>

                            <p class="mt-0.5 text-xs text-muted">
                                {{ t("completeShortApplication") }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ==================== Form Content ==================== -->

                <div class="p-5 sm:p-6">
                    <label
                        for="reason"
                        class="mb-2 block text-sm font-bold text-heading"
                    >
                        {{ t("tellAboutYourself") }}
                    </label>

                    <p class="mb-3 text-sm leading-6 text-muted">
                        {{ t("sellerApplicationHint") }}
                    </p>

                    <textarea
                        id="reason"
                        v-model="form.reason"
                        rows="7"
                        class="w-full resize-none rounded-xl border border-border bg-background px-4 py-3 text-sm leading-6 text-heading outline-none transition placeholder:text-muted focus:border-brand-500 focus:bg-surface focus:ring-2 focus:ring-brand-100"
                    ></textarea>

                    <p
                        v-if="form.errors.reason"
                        class="mt-2 text-sm font-medium text-red-600"
                    >
                        {{ form.errors.reason }}
                    </p>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="mt-5 w-full rounded-xl bg-brand-600 px-4 py-3 text-sm font-bold text-white shadow-sm transition-all duration-200 hover:bg-brand-700 hover:shadow-md disabled:cursor-not-allowed disabled:opacity-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2"
                    >
                        {{ form.processing ? t("submitting") : t("submit") }}
                    </button>
                </div>
            </form>
        </main>
    </div>
</template>
