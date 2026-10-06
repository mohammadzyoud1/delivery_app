<script setup lang="ts">
// ==================== Imports ====================

import { useForm } from "@inertiajs/vue3";
import { Lock } from "lucide-vue-next";

import AuthHeader from "@/Components/Auth/AuthHeader.vue";
import AuthInput from "@/Components/Auth/AuthInput.vue";
import Button from "@/Components/UI/Button.vue";
import { t } from "@/Utils/i18n";

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
const labels: Record<string, string> = {
    newPassword: t("newPass"),
    confirmPassword: t("confirmPass"),
    passwordPlaceholder: t("entNewPassPlace"),
    confirmPasswordPlaceholder: t("confNewPassPlace"),
};
</script>

<template>
    <div class="min-h-screen bg-white px-5 py-8 sm:px-8">
        <div class="mx-auto w-full max-w-md">
            <!-- ==================== Header ==================== -->

            <AuthHeader
                title="rstPass"
                subtitle="createNewPass"
                :on-back="goBack"
            />

            <!-- ==================== Form ==================== -->

            <form class="mt-10 space-y-5" @submit.prevent="handleSubmit">
                <AuthInput
                    v-model="form.password"
                    :label="labels.newPassword"
                    :placeholder="labels.passwordPlaceholder"
                    :icon="Lock"
                    :error="form.errors.password"
                    type="password"
                />

                <AuthInput
                    v-model="form.password_confirmation"
                    :label="labels.confNewPass"
                    :placeholder="labels.confirmPasswordPlaceholder"
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
                    {{ form.processing ? t("resetingPass") : t("rstPass") }}
                </Button>
            </form>
        </div>
    </div>
</template>
