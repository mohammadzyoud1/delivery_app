<script setup lang="ts">
// ==================== Imports ====================

import { router } from "@inertiajs/vue3";
import { Bell, MessageCircle, Package } from "lucide-vue-next";
import CustomerLayout from "@/Layouts/CustomerLayout.vue";

// ==================== Types ====================

interface NotificationData {
    order_id?: number;
    conversation_id?: number;
    status?: string;
}

interface Notification {
    id: number;
    type: "message" | "order";
    title: string;
    message: string;
    data: NotificationData | null;
    read_at: string | null;
    created_at: string;
}

// ==================== Props ====================

const props = defineProps<{
    notifications: Notification[];
    unreadCount: number;
}>();

// ==================== Mark As Read ====================

const markAsRead = (notification: Notification) => {
    if (notification.read_at) {
        return;
    }

    router.patch(
        `/notifications/${notification.id}/read`,
        {},
        {
            preserveScroll: true,
        },
    );
};

// ==================== Open Notification ====================

const openNotification = (notification: Notification) => {
    // ==================== Mark As Read ====================

    markAsRead(notification);

    // ==================== Message Notification ====================

    if (notification.type === "message" && notification.data?.conversation_id) {
        router.visit(`/messages/${notification.data.conversation_id}`);

        return;
    }

    // ==================== Order Notification ====================

    if (notification.type === "order" && notification.data?.order_id) {
        router.visit(`/orders/${notification.data.order_id}`);
    }
};
</script>

<template>
    <CustomerLayout>
        <!-- ==================== Notifications ==================== -->

        <main class="bg-background">
            <div class="mx-auto w-full max-w-3xl px-5 py-6 sm:px-8 sm:py-8">
                <!-- ==================== Header ==================== -->

                <div class="mb-6">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"
                        >
                            <Bell :size="22" :stroke-width="2" />
                        </div>

                        <div>
                            <h1
                                class="text-2xl font-extrabold tracking-tight text-heading"
                            >
                                Notifications
                            </h1>

                            <p class="mt-1 text-sm text-muted">
                                {{
                                    props.unreadCount > 0
                                        ? `${props.unreadCount} unread notification${props.unreadCount === 1 ? "" : "s"}`
                                        : "You're all caught up"
                                }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ==================== Empty State ==================== -->

                <div
                    v-if="!props.notifications.length"
                    class="rounded-2xl border border-border bg-surface p-10 text-center shadow-sm"
                >
                    <Bell
                        :size="32"
                        :stroke-width="1.5"
                        class="mx-auto text-muted"
                    />

                    <h2 class="mt-4 text-base font-bold text-heading">
                        No notifications yet
                    </h2>

                    <p class="mt-1 text-sm text-muted">
                        New messages and order updates will appear here.
                    </p>
                </div>

                <!-- ==================== Notification List ==================== -->

                <div
                    v-else
                    class="overflow-hidden rounded-2xl border border-border bg-surface shadow-sm"
                >
                    <button
                        v-for="notification in props.notifications"
                        :key="notification.id"
                        type="button"
                        class="flex w-full items-start gap-4 border-b border-border p-4 text-left transition-colors last:border-b-0 hover:bg-background sm:p-5"
                        :class="
                            notification.read_at
                                ? 'bg-surface'
                                : 'bg-brand-50/40'
                        "
                        @click="openNotification(notification)"
                    >
                        <!-- ==================== Icon ==================== -->

                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                            :class="
                                notification.type === 'message'
                                    ? 'bg-blue-50 text-blue-600'
                                    : 'bg-emerald-50 text-emerald-600'
                            "
                        >
                            <MessageCircle
                                v-if="notification.type === 'message'"
                                :size="19"
                                :stroke-width="2"
                            />

                            <Package v-else :size="19" :stroke-width="2" />
                        </div>

                        <!-- ==================== Content ==================== -->

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <h2 class="text-sm font-bold text-heading">
                                    {{ notification.title }}
                                </h2>

                                <!-- ==================== Unread Indicator ==================== -->

                                <span
                                    v-if="!notification.read_at"
                                    class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full bg-brand-600"
                                ></span>
                            </div>

                            <p class="mt-1 text-sm leading-5 text-body">
                                {{ notification.message }}
                            </p>

                            <p class="mt-2 text-xs font-medium text-muted">
                                {{ notification.created_at }}
                            </p>
                        </div>
                    </button>
                </div>
            </div>
        </main>
    </CustomerLayout>
</template>
