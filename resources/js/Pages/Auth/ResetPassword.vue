<script setup lang="ts">
// ==================== Imports ====================

import { useForm } from "@inertiajs/vue3";
import { Lock } from "lucide-vue-next";

import AuthHeader from "@/Components/Auth/AuthHeader.vue";
import AuthInput from "@/Components/Auth/AuthInput.vue";
import Button from "@/Components/UI/Button.vue";

// ==================== Props ====================

const props = defineProps<{
    token: string;
}>();

// ==================== Form ====================

const form = useForm({
    token: props.token,
    password: "",
    password_confirmation: "",
});

// ==================== Actions ====================

const goBack = () => {
    window.history.back();
};

const handleSubmit = () => {
    form.post("/reset-password");
};
</script>

<template>
    <div class="min-h-screen bg-white px-5 py-8 sm:px-8">
        <div class="mx-auto w-full max-w-md">
            <!-- ==================== Header ==================== -->

            <AuthHeader
                title="Reset Password"
                subtitle="Create a new password for your DELIVER account"
                :on-back="goBack"
            />

            <!-- ==================== Form ==================== -->

            <form class="mt-10 space-y-5" @submit.prevent="handleSubmit">
                <AuthInput
                    v-model="form.password"
                    label="New Password"
                    placeholder="Enter your new password"
                    :icon="Lock"
                    :error="form.errors.password"
                    type="password"
                />

                <AuthInput
                    v-model="form.password_confirmation"
                    label="Confirm Password"
                    placeholder="Confirm your new password"
                    :icon="Lock"
                    :error="form.errors.password_confirmation"
                    type="password"
                />

                <Button
                    type="submit"
                    :loading="form.processing"
                    :disabled="form.processing"
                    full-width
                >
                    {{
                        form.processing
                            ? "Resetting Password..."
                            : "Reset Password"
                    }}
                </Button>
            </form>
        </div>
    </div>
</template>
