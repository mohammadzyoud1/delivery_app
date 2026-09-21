<script setup lang="ts">
// ==================== Imports ====================

import { useForm } from "@inertiajs/vue3";
import { ArrowLeft, FileText, Store } from "lucide-vue-next";
import { computed } from "vue";
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

    <div class="min-h-screen bg-gray-50">
        <!-- ==================== Header ==================== -->

        <header class="border-b border-gray-100 bg-white">
            <div
                class="mx-auto flex h-16 w-full max-w-3xl items-center gap-3 px-5 sm:px-8"
            >
                <button
                    type="button"
                    @click="$inertia.visit('/')"
                    class="flex h-9 w-9 items-center justify-center rounded-full text-gray-600 transition hover:bg-gray-100"
                    aria-label="Go back"
                >
                    <ArrowLeft :size="20" />
                </button>

                <h1 class="text-lg font-bold text-gray-900">Become a Seller</h1>
            </div>
        </header>

        <!-- ==================== Content ==================== -->

        <main class="mx-auto w-full max-w-3xl px-5 py-8 sm:px-8">
            <!-- ==================== Intro ==================== -->
            <!-- ==================== Application Status ==================== -->

            <div
                v-if="isPending"
                class="rounded-2xl border border-amber-100 bg-amber-50 p-6"
            >
                <div
                    class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-amber-700"
                >
                    <FileText :size="22" />
                </div>

                <h3 class="text-lg font-bold text-gray-900">
                    Application under review
                </h3>

                <p class="mt-2 text-sm leading-6 text-gray-600">
                    Your seller application has been submitted and is waiting
                    for an administrator to review it.
                </p>
            </div>

            <!-- ==================== Approved Status ==================== -->

            <div
                v-else-if="isApproved"
                class="rounded-2xl border border-green-100 bg-green-50 p-6"
            >
                <div
                    class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-green-100 text-green-700"
                >
                    <Store :size="22" />
                </div>

                <h3 class="text-lg font-bold text-gray-900">You're a seller</h3>

                <p class="mt-2 text-sm leading-6 text-gray-600">
                    Your seller application was approved. You can now manage
                    your products from the Seller Dashboard.
                </p>

                <a
                    href="/seller"
                    class="mt-5 inline-flex rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800"
                >
                    Open Seller Dashboard
                </a>
            </div>

            <!-- ==================== Rejected Status ==================== -->

            <div
                v-else-if="isRejected"
                class="rounded-2xl border border-red-100 bg-red-50 p-6"
            >
                <div
                    class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-700"
                >
                    <FileText :size="22" />
                </div>

                <h3 class="text-lg font-bold text-gray-900">
                    Application not approved
                </h3>

                <p class="mt-2 text-sm leading-6 text-gray-600">
                    Your previous seller application was not approved.
                </p>

                <div
                    v-if="props.application?.rejection_reason"
                    class="mt-4 rounded-xl bg-white p-4"
                >
                    <p
                        class="text-xs font-semibold uppercase tracking-wide text-gray-500"
                    >
                        Admin's reason
                    </p>

                    <p class="mt-1 text-sm leading-6 text-gray-700">
                        {{ props.application.rejection_reason }}
                    </p>
                </div>

                <form @submit.prevent="submitApplication" class="mt-5">
                    <label
                        for="reason"
                        class="mb-2 block text-sm font-semibold text-gray-900"
                    >
                        Apply again
                    </label>

                    <textarea
                        id="reason"
                        v-model="form.reason"
                        rows="6"
                        placeholder="Tell us why you would like to become a seller..."
                        class="w-full resize-none rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                    />

                    <p
                        v-if="form.errors.reason"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.reason }}
                    </p>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="mt-4 w-full rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {{
                            form.processing
                                ? "Submitting..."
                                : "Submit New Application"
                        }}
                    </button>
                </form>
            </div>

            <!-- ==================== New Application ==================== -->

            <form
                v-else
                @submit.prevent="submitApplication"
                class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-6"
            >
                <label
                    for="reason"
                    class="mb-2 flex items-center gap-2 text-sm font-semibold text-gray-900"
                >
                    <FileText :size="17" />
                    Why do you want to become a seller?
                </label>

                <textarea
                    id="reason"
                    v-model="form.reason"
                    rows="7"
                    placeholder="Tell us about what you would like to sell..."
                    class="w-full resize-none rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                />

                <p v-if="form.errors.reason" class="mt-2 text-sm text-red-600">
                    {{ form.errors.reason }}
                </p>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="mt-5 w-full rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    {{
                        form.processing ? "Submitting..." : "Submit Application"
                    }}
                </button>
            </form>
        </main>
    </div>
</template>
