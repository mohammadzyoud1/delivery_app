<script setup lang="ts">
// ==================== Types ====================

interface NotificationSetting {
    key: string;
    title: string;
    description: string;
    enabled: boolean;
}

// ==================== State ====================

const notificationSettings: NotificationSetting[] = [
    {
        key: "orders",
        title: "Order Updates",
        description: "Receive updates about your orders and delivery status.",
        enabled: true,
    },
    {
        key: "messages",
        title: "Messages",
        description: "Receive notifications when you receive a new message.",
        enabled: true,
    },
    {
        key: "seller",
        title: "Seller Updates",
        description: "Receive updates related to your seller activity.",
        enabled: true,
    },
];
</script>

<template>
    <div>
        <!-- ==================== Header ==================== -->

        <div>
            <h2 class="text-lg font-bold text-heading">Notifications</h2>

            <p class="mt-1 text-sm text-muted">
                Choose which notifications you want to receive.
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
                    @click="setting.enabled = !setting.enabled"
                >
                    <span
                        class="absolute top-1 h-4 w-4 rounded-full bg-white shadow-sm transition-transform duration-200"
                        :class="
                            setting.enabled ? 'translate-x-6' : 'translate-x-1'
                        "
                    ></span>
                </button>
            </div>
        </div>
    </div>
</template>
