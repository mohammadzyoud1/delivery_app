```vue
<script setup lang="ts">
import { Link, useForm } from "@inertiajs/vue3";
import { Mail } from "lucide-vue-next";

import AuthHeader from "@/Components/Auth/AuthHeader.vue";
import AuthInput from "@/Components/Auth/AuthInput.vue";
import PasswordInput from "@/Components/Auth/PasswordInput.vue";
import Button from "@/Components/UI/Button.vue";
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
</script>

<template>
    <!-- ==================== Page Container ==================== -->

    <div class="min-h-screen bg-white px-5 py-8 sm:px-8">
        <div class="mx-auto w-full max-w-md">
            <!-- ==================== Header ==================== -->

            <AuthHeader
                title="Welcome Back!"
                subtitle="Login to your account"
                :on-back="goBack"
            />

            <!-- ==================== Login Form ==================== -->

            <form class="mt-10 space-y-5" @submit.prevent="handleSubmit">
                <AuthInput
                    v-model="form.login"
                    label="Email or Phone"
                    placeholder="Enter your email or phone"
                    :icon="Mail"
                    :error="form.errors.login"
                />

                <PasswordInput
                    v-model="form.password"
                    label="Password"
                    placeholder="Enter your password"
                    :error="form.errors.password"
                />

                <!-- ==================== Forgot Password ==================== -->

                <div class="text-right">
                    <Link
                        href="/forgot-password"
                        class="text-sm font-semibold text-gray-900 hover:underline"
                    >
                        Forgot Password?
                    </Link>
                </div>

                <!-- ==================== Submit Button ==================== -->

                <Button
                    type="submit"
                    :loading="form.processing"
                    :disabled="form.processing"
                    full-width
                >
                    {{ form.processing ? "Logging in..." : "Login" }}
                </Button>
            </form>

            <!-- ==================== Register Link ==================== -->

            <p class="mt-8 text-center text-sm text-gray-500">
                Don't have an account?
                <Link
                    href="/register"
                    class="font-semibold text-gray-900 hover:underline"
                >
                    Sign Up
                </Link>
            </p>
            <!--logout button-->
        </div>
    </div>
</template>
```
