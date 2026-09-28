<script setup lang="ts">
// ==================== Imports ====================

import { Link, useForm, usePage } from "@inertiajs/vue3";
import { Mail } from "lucide-vue-next";
import { ref } from "vue";
import AuthHeader from "@/Components/Auth/AuthHeader.vue";
import AuthInput from "@/Components/Auth/AuthInput.vue";
import Button from "@/Components/UI/Button.vue";
const emailSent = ref(false);
interface PageProps {
    flash: {
        success?: string | null;
        error?: string | null;
    };
}

const page = usePage<PageProps>();

// ==================== Form ====================

const form = useForm({
    email: "",
});

// ==================== Actions ====================

const goBack = () => {
    window.history.back();
};
const goToLogin = () => {
    window.location.href = "/login";
};

const handleSubmit = () => {
    form.post("/forgot-password", {
        onSuccess: () => {
            emailSent.value = true;
        },
    });
};
</script>

<!-- ==================== Forgot Password State ==================== -->

<template>
    <div class="min-h-screen bg-white px-5 py-8 sm:px-8">
        <div class="mx-auto w-full max-w-md">
            <!-- ==================== Header ==================== -->

            <AuthHeader
                title="Forgot Password?"
                subtitle="Enter your email to receive a password reset link"
                :on-back="goBack"
            />

            <!-- ==================== Email Form ==================== -->

            <div v-if="!emailSent">
                <form class="mt-10 space-y-5" @submit.prevent="handleSubmit">
                    <AuthInput
                        v-model="form.email"
                        label="Email"
                        placeholder="Enter your email"
                        :icon="Mail"
                        :error="form.errors.email"
                        type="email"
                    />

                    <Button
                        type="submit"
                        :loading="form.processing"
                        :disabled="form.processing"
                        full-width
                    >
                        {{
                            form.processing
                                ? "Sending Link..."
                                : "Send Reset Link"
                        }}
                    </Button>
                </form>

                <p class="mt-8 text-center text-sm text-gray-500">
                    Remember your password?

                    <Link
                        href="/login"
                        class="font-semibold text-gray-900 hover:underline"
                    >
                        Login
                    </Link>
                </p>
            </div>

            <!-- ==================== Email Sent State ==================== -->

            <div v-else class="mt-10 text-center">
                <div
                    class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 text-emerald-600"
                >
                    <Mail :size="28" :stroke-width="2" />
                </div>

                <h2 class="mt-6 text-xl font-bold text-gray-900">
                    Check Your Email
                </h2>

                <p
                    class="mx-auto mt-3 max-w-sm text-sm leading-6 text-gray-500"
                >
                    If an account exists for this email, we have sent you a
                    password reset link.
                </p>

                <Button
                    type="button"
                    class="mt-8"
                    full-width
                    @click="goToLogin"
                >
                    Back to Login
                </Button>
            </div>
        </div>
    </div>
</template>
