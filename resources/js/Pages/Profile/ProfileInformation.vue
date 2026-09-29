<script setup lang="ts">
// ==================== Imports ====================

import { computed } from "vue";
import { usePage, useForm } from "@inertiajs/vue3";
import { Mail, Phone, User } from "lucide-vue-next";

import AuthInput from "@/Components/Auth/AuthInput.vue";
import Button from "@/Components/UI/Button.vue";

// ==================== Types ====================

interface AuthUser {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone: string;
    profile_image: string | null;
    roles: string[];
}

interface PageProps {
    [key: string]: unknown;

    auth: {
        user: AuthUser | null;
    };
}

// ==================== Page ====================

const page = usePage<PageProps>();

const user = computed(() => page.props.auth.user);

// ==================== Form ====================

const form = useForm({
    first_name: user.value?.first_name ?? "",
    last_name: user.value?.last_name ?? "",
    phone: user.value?.phone ?? "",
});

// ==================== Submit ====================

const handleSubmit = () => {
    form.patch("/profile");
};
</script>

<template>
    <div>
        <!-- ==================== Header ==================== -->

        <div>
            <h2 class="text-lg font-bold text-heading">Personal Information</h2>

            <p class="mt-1 text-sm text-muted">
                Update your personal account information.
            </p>
        </div>

        <!-- ==================== Form ==================== -->

        <form class="mt-6 space-y-5" @submit.prevent="handleSubmit">
            <!-- ==================== First Name ==================== -->

            <AuthInput
                :model-value="form.first_name"
                label="First Name"
                placeholder="Your first name"
                :icon="User"
                :error="form.errors.first_name"
            />

            <!-- ==================== Last Name ==================== -->

            <AuthInput
                :model-value="form.last_name"
                label="Last Name"
                placeholder="Your last name"
                :icon="User"
                :error="form.errors.last_name"
            />

            <!-- ==================== Email ==================== -->

            <AuthInput
                :model-value="user?.email ?? ''"
                label="Email"
                placeholder="Your email"
                :icon="Mail"
                type="email"
                disabled
            />

            <!-- ==================== Phone ==================== -->

            <AuthInput
                :model-value="form.phone"
                label="Phone"
                placeholder="Your phone number"
                :icon="Phone"
                :error="form.errors.phone"
            />

            <!-- ==================== Save ==================== -->

            <div class="flex justify-end border-t border-border pt-5">
                <Button
                    type="submit"
                    variant="primary"
                    :loading="form.processing"
                    :disabled="form.processing"
                >
                    {{ form.processing ? "Saving..." : "Save Changes" }}
                </Button>
            </div>
        </form>
    </div>
</template>
