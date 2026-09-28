<script setup lang="ts">
// ==================== Imports ====================

import { ref } from "vue";
import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import ProfileInformation from "@/Pages/Profile/ProfileInformation.vue";
import ProfileLanguage from "./ProfileLanguage.vue";
import ProfileNotifications from "./ProfileNotifications.vue";
import ProfilePicture from "./ProfilePicture.vue";
// ==================== Types ====================

type ProfileTab = "information" | "language" | "notifications" | "picture";

// ==================== State ====================

const activeTab = ref<ProfileTab>("information");

// ==================== Tabs ====================

const tabs: {
    key: ProfileTab;
    label: string;
}[] = [
    {
        key: "information",
        label: "Information",
    },
    {
        key: "language",
        label: "Language",
    },
    {
        key: "notifications",
        label: "Notifications",
    },
    {
        key: "picture",
        label: "Profile Picture",
    },
];
</script>

<template>
    <CustomerLayout>
        <main class="bg-background">
            <div
                class="mx-auto w-full max-w-6xl px-5 py-6 sm:px-8 sm:py-8 lg:py-10"
            >
                <!-- ==================== Header ==================== -->

                <div>
                    <h1
                        class="text-2xl font-extrabold tracking-tight text-heading sm:text-3xl"
                    >
                        Profile
                    </h1>

                    <p class="mt-2 text-sm text-muted">
                        Manage your account and preferences.
                    </p>
                </div>

                <!-- ==================== Profile Layout ==================== -->

                <div
                    class="mt-8 lg:grid lg:grid-cols-[220px_minmax(0,1fr)] lg:gap-8"
                >
                    <!-- ==================== Tabs ==================== -->

                    <nav
                        class="flex gap-2 overflow-x-auto pb-2 lg:flex-col lg:overflow-visible lg:pb-0"
                    >
                        <button
                            v-for="tab in tabs"
                            :key="tab.key"
                            type="button"
                            class="shrink-0 rounded-xl px-4 py-3 text-left text-sm font-semibold transition-colors duration-150"
                            :class="
                                activeTab === tab.key
                                    ? 'bg-brand-600 text-white shadow-sm'
                                    : 'bg-surface text-body hover:bg-brand-50 hover:text-brand-700'
                            "
                            @click="activeTab = tab.key"
                        >
                            {{ tab.label }}
                        </button>
                    </nav>

                    <!-- ==================== Tab Content ==================== -->

                    <section
                        class="mt-6 rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6 lg:mt-0"
                    >
                        <ProfileInformation v-if="activeTab === 'information'">
                            <h2 class="text-lg font-bold text-heading">
                                Personal Information
                            </h2>

                            <p class="mt-1 text-sm text-muted">
                                Update your personal account information.
                            </p>

                            <!-- Information form will be added next -->
                        </ProfileInformation>

                        <ProfileLanguage v-else-if="activeTab === 'language'">
                            <h2 class="text-lg font-bold text-heading">
                                Language
                            </h2>

                            <p class="mt-1 text-sm text-muted">
                                Choose your preferred language.
                            </p>
                        </ProfileLanguage>

                        <ProfileNotifications
                            v-else-if="activeTab === 'notifications'"
                        >
                            <h2 class="text-lg font-bold text-heading">
                                Notifications
                            </h2>

                            <p class="mt-1 text-sm text-muted">
                                Manage your notification preferences.
                            </p>
                        </ProfileNotifications>

                        <ProfilePicture v-else>
                            <h2 class="text-lg font-bold text-heading">
                                Profile Picture
                            </h2>

                            <p class="mt-1 text-sm text-muted">
                                Manage your profile picture.
                            </p>
                        </ProfilePicture>
                    </section>
                </div>
            </div>
        </main>
    </CustomerLayout>
</template>
