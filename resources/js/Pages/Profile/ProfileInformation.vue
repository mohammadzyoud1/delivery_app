<script setup lang="ts">
// ==================== Imports ====================

import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
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

const firstName = computed(() => user.value?.first_name ?? "");
const lastName = computed(() => user.value?.last_name ?? "");
const email = computed(() => user.value?.email ?? "");
const phone = computed(() => user.value?.phone ?? "");
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

        <div class="mt-6 space-y-5">
            <!-- ==================== First Name ==================== -->

            <AuthInput
                :model-value="firstName"
                label="First Name"
                placeholder="Your first name"
                :icon="User"
                disabled
            />

            <!-- ==================== Last Name ==================== -->

            <AuthInput
                :model-value="lastName"
                label="Last Name"
                placeholder="Your last name"
                :icon="User"
                disabled
            />

            <!-- ==================== Email ==================== -->

            <AuthInput
                :model-value="email"
                label="Email"
                placeholder="Your email"
                :icon="Mail"
                type="email"
                disabled
            />

            <!-- ==================== Phone ==================== -->

            <AuthInput
                :model-value="phone"
                label="Phone"
                placeholder="Your phone number"
                :icon="Phone"
                disabled
            />

            <!-- ==================== Save ==================== -->

            <div class="flex justify-end border-t border-border pt-5">
                <Button type="button" variant="primary" disabled>
                    Save Changes
                </Button>
            </div>
        </div>
    </div>
</template>
