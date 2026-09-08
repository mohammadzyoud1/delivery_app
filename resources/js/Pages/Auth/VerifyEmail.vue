<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";

import AuthHeader from "@/Components/Auth/AuthHeader.vue";

const form = useForm({
    otp: "",
});

const handleSubmit = () => {
    form.post("/verify-email", { replace: true });
};
</script>

<template>
    <div class="min-h-screen bg-white px-5 py-8 sm:px-8">
        <div class="mx-auto w-full max-w-md">
            <AuthHeader
                title="Verify Your Email"
                subtitle="Enter the 6-digit code we sent to your email"
            />

            <form class="mt-10 space-y-6" @submit.prevent="handleSubmit">
                <div class="space-y-2">
                    <label
                        for="otp"
                        class="block text-sm font-semibold text-gray-800"
                    >
                        Verification Code
                    </label>

                    <input
                        id="otp"
                        v-model="form.otp"
                        type="text"
                        inputmode="numeric"
                        maxlength="6"
                        autocomplete="one-time-code"
                        placeholder="000000"
                        class="w-full rounded-xl border border-gray-200 px-4 py-4 text-center text-2xl font-semibold tracking-[0.5em] outline-none transition focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                    />

                    <p v-if="form.errors.otp" class="text-sm text-red-500">
                        {{ form.errors.otp }}
                    </p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 py-3.5 text-sm font-semibold text-white transition hover:bg-gray-800 active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <span
                        v-if="form.processing"
                        class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"
                    ></span>

                    {{ form.processing ? "Verifying..." : "Verify Email" }}
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                Didn't receive the code?
                <button
                    type="button"
                    class="font-semibold text-gray-900 hover:underline"
                >
                    Resend Code
                </button>
            </p>
        </div>
    </div>
</template>
