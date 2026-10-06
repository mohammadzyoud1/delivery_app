<script setup lang="ts">
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { Mail } from "lucide-vue-next";

import AuthHeader from "@/Components/Auth/AuthHeader.vue";
import AuthInput from "@/Components/Auth/AuthInput.vue";
import PasswordInput from "@/Components/Auth/PasswordInput.vue";
import Button from "@/Components/UI/Button.vue";
import { t } from "@/Utils/i18n";
interface PageProps {
    flash: {
        success?: string | null;
        error?: string | null;
    };
}

const page = usePage<PageProps>();
// ==================== Form ====================

const form = useForm({
    login: "",
    password: "",
});

// ==================== Actions ====================

const goBack = () => {
    window.history.back();
};

const handleSubmit = () => {
    form.post("/login", {
        replace: true,
    });
};
const labels: Record<string, string> = {
    email: t("emailorPhone"),
    password: t("password"),
    emailPlaceholder: t("emailPlace"),
    passwordPlaceholder: t("passwordPlace"),
};
</script>

<template>
    <!-- ==================== Page Container ==================== -->

    <div class="min-h-screen bg-white px-5 py-8 sm:px-8">
        <div class="mx-auto w-full max-w-md">
            <!-- ==================== Header ==================== -->

            <AuthHeader
                title="welcomebk"
                subtitle="loginAcc"
                :on-back="goBack"
            />

            <!-- ==================== Flash Message ==================== -->

            <div
                v-if="page.props.flash?.success"
                class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700"
            >
                {{ page.props.flash.success }}
            </div>

            <div
                v-if="page.props.flash?.error"
                class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700"
            >
                {{ page.props.flash.error }}
            </div>
            <!-- ==================== Login Form ==================== -->

            <form class="mt-10 space-y-5" @submit.prevent="handleSubmit">
                <AuthInput
                    v-model="form.login"
                    :label="labels.email"
                    :placeholder="labels.emailPlaceholder"
                    :icon="Mail"
                    :error="form.errors.login"
                />

                <PasswordInput
                    v-model="form.password"
                    :label="labels.password"
                    :placeholder="labels.passwordPlaceholder"
                    :error="form.errors.password"
                />

                <!-- ==================== Forgot Password ==================== -->

                <div class="text-right">
                    <Link
                        href="/forgot-password"
                        class="text-sm font-semibold text-gray-900 hover:underline"
                    >
                        {{ t("forgotPass") }}
                    </Link>
                </div>

                <!-- ==================== Submit Button ==================== -->

                <Button
                    type="submit"
                    :loading="form.processing"
                    :disabled="form.processing"
                    full-width
                >
                    {{ form.processing ? t("logging") : t("login") }}
                </Button>
            </form>

            <!-- ==================== Register Link ==================== -->

            <p class="mt-8 text-center text-sm text-gray-500">
                {{ t("noAcc") }}
                <Link
                    href="/register"
                    class="font-semibold text-gray-900 hover:underline"
                >
                    {{ t("signup") }}
                </Link>
            </p>
            <!--logout button-->
        </div>
    </div>
</template>
