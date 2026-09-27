<script setup lang="ts">
// ==================== Imports ====================

import ConversationChat from "@/Components/Messaging/ConversationChat.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { router } from "@inertiajs/vue3";
import { ArrowLeft, User } from "lucide-vue-next";

// ==================== Types ====================

interface User {
    id: number;
    first_name: string;
    last_name: string;
    email?: string;
    profile_image: string | null;
    roles: {
        id: number;
        name: string;
    }[];
}

interface Message {
    id: number;
    conversation_id: number;
    sender_id: number;
    message: string;
    read_at: string | null;
    created_at: string;
    sender: User;
}

interface Order {
    id: number;
    status: string;
}

interface Conversation {
    id: number;
    type: string;
    order_id: number | null;
    order: Order | null;
    participants: User[];
    messages: Message[];
}

// ==================== Props ====================

const props = defineProps<{
    conversation: Conversation;
}>();

// ==================== Customer ====================

const conversationUser = props.conversation.participants[0] ?? null;

const conversationRole = () => {
    if (!conversationUser) {
        return "User";
    }

    if (conversationUser.roles.some((role) => role.name === "seller")) {
        return "Seller";
    }

    if (conversationUser.roles.some((role) => role.name === "delivery")) {
        return "Delivery";
    }

    return "Customer";
};

// ==================== Back ====================

const goBack = () => {
    router.visit("/admin/messages");
};
</script>

<template>
    <AdminLayout>
        <!-- ==================== Conversation Page ==================== -->

        <main class="min-h-screen bg-background">
            <div
                class="mx-auto flex w-full max-w-4xl flex-col px-5 py-6 sm:px-8 sm:py-8"
            >
                <!-- ==================== Header ==================== -->

                <div class="mb-5 flex items-center gap-3">
                    <button
                        type="button"
                        aria-label="Back to messages"
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-border bg-surface text-muted transition-colors hover:bg-background hover:text-heading"
                        @click="goBack"
                    >
                        <ArrowLeft :size="19" />
                    </button>

                    <!-- ==================== Customer ==================== -->

                    <div class="flex min-w-0 items-center gap-3">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-brand-50 text-brand-600"
                        >
                            <img
                                v-if="conversationUser?.profile_image"
                                :src="`/storage/${conversationUser.profile_image}`"
                                :alt="`${conversationUser.first_name} ${conversationUser.last_name}`"
                                class="h-full w-full object-cover"
                            />

                            <User v-else :size="19" :stroke-width="2" />
                        </div>

                        <div class="min-w-0">
                            <h1
                                class="truncate text-base font-bold text-heading"
                            >
                                {{
                                    conversationUser
                                        ? `${conversationUser.first_name} ${conversationUser.last_name}`
                                        : "User"
                                }}
                            </h1>

                            <p class="text-xs text-muted">
                                {{ conversationRole() }} Support ·
                                {{ conversationUser.email }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ==================== Reusable Chat ==================== -->

                <ConversationChat
                    :conversation="props.conversation"
                    :current-user-id="$page.props.auth.user?.id ?? null"
                    empty-title="No messages yet"
                    empty-message="Start the conversation with the customer."
                />
            </div>
        </main>
    </AdminLayout>
</template>
