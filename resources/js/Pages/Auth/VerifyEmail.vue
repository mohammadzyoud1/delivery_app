<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { t } from "@/Utils/i18n";

import AuthHeader from "@/Components/Auth/AuthHeader.vue";
import Button from "@/Components/UI/Button.vue";
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
            <AuthHeader title="verifyYourEmail" subtitle="ent6digit" />

            <form class="mt-10 space-y-6" @submit.prevent="handleSubmit">
                <div class="space-y-2">
                    <label
                        for="otp"
                        class="block text-sm font-semibold text-gray-800"
                    >
                        {{ t("verificationCode") }}
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

                <Button
                    type="submit"
                    :loading="form.processing"
                    :disabled="form.processing"
                    full-width
                >
                    {{ form.processing ? t("verifying") : t("verifyEmail") }}
                </Button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                {{ t("didNotReceiveCode") }}
                <button
                    type="button"
                    class="font-semibold text-gray-900 hover:underline"
                >
                    {{ t("resendCode") }}
                </button>
            </p>
        </div>
    </div>
</template>
