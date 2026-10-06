<script setup lang="ts">
import { ref } from "vue";
import { usePage, useForm } from "@inertiajs/vue3";
import { t } from "@/Utils/i18n";

// ==================== Types ====================

interface NotificationPreferences {
    messages: boolean;
    orders: boolean;
}
interface AuthUser {
    notification_preferences: NotificationPreferences;
}
interface PageProps {
    [key: string]: unknown;

    auth: {
        user: AuthUser | null;
    };
}
const page = usePage<PageProps>();

const user = page.props.auth.user;

interface NotificationSetting {
    key: string;
    title: string;
    description: string;
    enabled: boolean;
}

// ==================== State ====================

const notificationSettings = ref<NotificationSetting[]>([
    {
        key: "messages",
        title: t("messages"),
        description: t("msgNotificationDescription"),
        enabled: user?.notification_preferences.messages ?? true,
    },
    {
        key: "orders",
        title: t("orderUpdates"),
        description: t("orderUpdateNotificationDescription"),
        enabled: user?.notification_preferences.orders ?? true,
    },
]);

const form = useForm({
    messages: user?.notification_preferences.messages ?? true,
    orders: user?.notification_preferences.orders ?? true,
});

// ==================== Change Notification ====================

const toggleNotification = (key: "messages" | "orders") => {
    const setting = notificationSettings.value.find(
        (setting) => setting.key === key,
    );

    if (!setting) {
        return;
    }

    setting.enabled = !setting.enabled;

    form[key] = setting.enabled;

    form.patch("/profile/notifications");
};
</script>

<template>
    <div>
        <!-- ==================== Header ==================== -->

        <div>
            <h2 class="text-lg font-bold text-heading">
                {{ t("notifications") }}
            </h2>

            <p class="mt-1 text-sm text-muted">
                {{ t("chooseNotification") }}
            </p>
        </div>

        <!-- ==================== Notification Settings ==================== -->

        <div
            class="mt-6 divide-y divide-border rounded-2xl border border-border"
        >
            <div
                v-for="setting in notificationSettings"
                :key="setting.key"
                class="flex items-center justify-between gap-5 p-4 sm:p-5"
            >
                <!-- ==================== Information ==================== -->

                <div class="min-w-0">
                    <h3 class="text-sm font-semibold text-heading">
                        {{ setting.title }}
                    </h3>

                    <p class="mt-1 text-sm leading-5 text-muted">
                        {{ setting.description }}
                    </p>
                </div>

                <!-- ==================== Toggle ==================== -->

                <button
                    type="button"
                    role="switch"
                    :aria-checked="setting.enabled"
                    class="relative h-6 w-11 shrink-0 rounded-full transition-colors duration-200"
                    :class="setting.enabled ? 'bg-brand-600' : 'bg-slate-300'"
                    @click="
                        toggleNotification(setting.key as 'messages' | 'orders')
                    "
                >
                    <span
                        class="absolute top-1 h-4 w-4 rounded-full bg-white shadow-sm transition-transform duration-200"
                        :class="
                            setting.enabled ? 'translate-x-1' : '-translate-x-5'
                        "
                    ></span>
                </button>
            </div>
        </div>
    </div>
</template>
