<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import { Mail, Phone } from "lucide-vue-next";

import AuthHeader from "@/Components/Auth/AuthHeader.vue";
import AuthInput from "@/Components/Auth/AuthInput.vue";
import PasswordInput from "@/Components/Auth/PasswordInput.vue";
import ProfileImageUpload from "@/Components/Auth/ProfileImageUpload.vue";
import SocialLoginButton from "@/Components/Auth/SocialLoginButton.vue";
import Button from "@/Components/UI/Button.vue";
const form = useForm({
    first_name: "",
    last_name: "",
    email: "",
    phone: "",
    password: "",
    password_confirmation: "",
    profile_image: null as File | null,
});
const goBack = () => {
    window.history.back();
};

const handleSubmit = () => {
    form.post("/register", {
        forceFormData: true,
        replace: true,
    });
};
</script>

<template>
    <div class="min-h-screen bg-white px-5 py-8 sm:px-8">
        <div class="mx-auto w-full max-w-md">
            <!-- Header -->
            <AuthHeader
                title="Hello!"
                subtitle="Create your account"
                :on-back="goBack"
            />

            <!-- Profile image -->
            <div class="mt-8">
                <ProfileImageUpload v-model="form.profile_image" />
            </div>

            <!-- Register form -->
            <form class="mt-8 space-y-5" @submit.prevent="handleSubmit">
                <!-- First name -->
                <AuthInput
                    v-model="form.first_name"
                    label="First Name"
                    placeholder="Enter your first name"
                    :error="form.errors.first_name"
                />

                <!-- Last name -->
                <AuthInput
                    v-model="form.last_name"
                    label="Last Name"
                    placeholder="Enter your last name"
                    :error="form.errors.last_name"
                />

                <!-- Email -->
                <AuthInput
                    v-model="form.email"
                    type="email"
                    label="Email"
                    placeholder="Enter your email"
                    :icon="Mail"
                    :error="form.errors.email"
                />

                <!-- Phone -->
                <AuthInput
                    v-model="form.phone"
                    type="tel"
                    label="Phone Number"
                    placeholder="Enter your phone number"
                    :icon="Phone"
                    :error="form.errors.phone"
                />

                <!-- Password -->
                <PasswordInput
                    v-model="form.password"
                    label="Password"
                    placeholder="Enter your password"
                    :error="form.errors.password"
                />

                <!-- Confirm password -->
                <PasswordInput
                    v-model="form.password_confirmation"
                    label="Confirm Password"
                    placeholder="Confirm your password"
                    :error="form.errors.password_confirmation"
                />

                <!-- Sign up -->
                <Button
                    type="submit"
                    :loading="form.processing"
                    :disabled="form.processing"
                    full-width
                >
                    {{ form.processing ? "Creating Account..." : "Sign Up" }}
                </Button>
            </form>

            <!-- Divider -->
            <div class="my-7 flex items-center gap-4">
                <div class="h-px flex-1 bg-gray-200"></div>

                <span class="text-sm text-gray-400"> Or </span>

                <div class="h-px flex-1 bg-gray-200"></div>
            </div>

            <!-- Social login -->
            <div class="space-y-3">
                <SocialLoginButton provider="Google" />

                <SocialLoginButton provider="Apple" />
            </div>

            <!-- Login -->
            <p class="mt-8 text-center text-sm text-gray-500">
                Already have an account?
                <Link
                    href="/login"
                    class="font-semibold text-gray-900 hover:underline"
                >
                    Login
                </Link>
            </p>
        </div>
    </div>
</template>
