<script setup lang="ts">
// ==================== Imports ====================

import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";
import { ChevronRight, MessageCircle, User } from "lucide-vue-next";

// ==================== Types ====================

interface User {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    profile_image: string | null;
    roles: string[];
}

interface Message {
    id: number;
    sender_id: number;
    message: string;
    created_at: string;
}

interface Conversation {
    id: number;
    type: string;
    order_id: number | null;
    participants: User[];
    messages: Message[];
}

// ==================== Props ====================

const props = defineProps<{
    conversations: Conversation[];
}>();

// ==================== Customer Name ====================

const getCustomer = (conversation: Conversation) => {
    return conversation.participants[0] ?? null;
};

// ==================== Latest Message ====================

const getLatestMessage = (conversation: Conversation) => {
    return conversation.messages[0] ?? null;
};
</script>

<template>
    <AdminLayout>
        <!-- ==================== Admin Messages ==================== -->

        <main class="min-h-screen bg-background">
            <div class="mx-auto w-full max-w-6xl px-5 py-6 sm:px-8 sm:py-8">
                <!-- ==================== Header ==================== -->

                <div class="mb-6">
                    <h1
                        class="text-2xl font-extrabold tracking-tight text-heading sm:text-3xl"
                    >
                        Messages
                    </h1>

                    <p class="mt-1 text-sm text-muted">
                        Manage customer support conversations.
                    </p>
                </div>

                <!-- ==================== Conversations ==================== -->

                <div
                    v-if="props.conversations.length"
                    class="overflow-hidden rounded-2xl border border-border bg-surface shadow-sm"
                >
                    <Link
                        v-for="conversation in props.conversations"
                        :key="conversation.id"
                        :href="`/messages/${conversation.id}`"
                        class="flex items-center gap-4 border-b border-border p-4 transition-colors last:border-b-0 hover:bg-background sm:p-5"
                    >
                        <!-- ==================== Customer Avatar ==================== -->

                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-full bg-brand-50 text-brand-600"
                        >
                            <img
                                v-if="getCustomer(conversation)?.profile_image"
                                :src="`/storage/${getCustomer(conversation)?.profile_image}`"
                                :alt="`${getCustomer(conversation)?.first_name} ${getCustomer(conversation)?.last_name}`"
                                class="h-full w-full object-cover"
                            />

                            <User v-else :size="21" :stroke-width="2" />
                        </div>

                        <!-- ==================== Conversation Information ==================== -->

                        <div class="min-w-0 flex-1">
                            <h2
                                class="truncate text-sm font-bold text-heading sm:text-base"
                            >
                                {{
                                    getCustomer(conversation)
                                        ? `${getCustomer(conversation)?.first_name} ${getCustomer(conversation)?.last_name}`
                                        : "Unknown Customer"
                                }}
                            </h2>

                            <p class="mt-1 truncate text-sm text-muted">
                                {{
                                    getLatestMessage(conversation)?.message ??
                                    "No messages yet."
                                }}
                            </p>
                        </div>

                        <!-- ==================== Conversation Icon ==================== -->

                        <div
                            class="hidden shrink-0 items-center gap-2 text-muted sm:flex"
                        >
                            <MessageCircle :size="18" />

                            <ChevronRight :size="18" />
                        </div>
                    </Link>
                </div>

                <!-- ==================== Empty State ==================== -->

                <div
                    v-else
                    class="rounded-2xl border border-border bg-surface px-6 py-14 text-center shadow-sm"
                >
                    <div
                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-brand-50 text-brand-600"
                    >
                        <MessageCircle :size="26" />
                    </div>

                    <h2 class="mt-4 text-base font-bold text-heading">
                        No customer messages
                    </h2>

                    <p
                        class="mx-auto mt-2 max-w-sm text-sm leading-6 text-muted"
                    >
                        Customer support conversations will appear here when
                        customers contact DELIVER.
                    </p>
                </div>
            </div>
        </main>
    </AdminLayout>
</template>
