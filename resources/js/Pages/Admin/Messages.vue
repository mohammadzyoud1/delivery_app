<script setup lang="ts">
// ==================== Imports ====================

import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";
import { ChevronRight, MessageCircle, User } from "lucide-vue-next";
import { computed, ref } from "vue";

// ==================== Types ====================

interface User {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    profile_image: string | null;
    roles: {
        id: number;
        name: string;
    }[];
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
// ==================== Filter ====================

type ConversationFilter = "all" | "customer" | "seller" | "delivery";

const activeFilter = ref<ConversationFilter>("all");

const filters: {
    value: ConversationFilter;
    label: string;
}[] = [
    { value: "all", label: "All" },
    { value: "customer", label: "Customers" },
    { value: "seller", label: "Sellers" },
    { value: "delivery", label: "Delivery" },
];

// ==================== Customer Name ====================

const getConversationUser = (conversation: Conversation) => {
    return conversation.participants[0] ?? null;
};
// ==================== Conversation Role ====================

const getConversationRole = (
    conversation: Conversation,
): Exclude<ConversationFilter, "all"> | null => {
    const user = getConversationUser(conversation);

    console.log("Conversation user:", user);
    console.log("User roles:", user?.roles);

    if (!user) {
        return null;
    }

    if (user.roles.some((role) => role.name === "seller")) {
        return "seller";
    }

    if (user.roles.some((role) => role.name === "delivery")) {
        return "delivery";
    }

    return "customer";
};

// ==================== Filtered Conversations ====================

const filteredConversations = computed(() => {
    if (activeFilter.value === "all") {
        return props.conversations;
    }

    return props.conversations.filter((conversation) => {
        return getConversationRole(conversation) === activeFilter.value;
    });
});

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
                        Manage support conversations with customers, sellers,
                        and delivery users.
                    </p>
                </div>

                <!-- ==================== Conversation Filters ==================== -->

                <div class="mb-5 flex flex-wrap gap-2">
                    <button
                        v-for="filter in filters"
                        :key="filter.value"
                        type="button"
                        class="rounded-xl border px-4 py-2 text-sm font-semibold transition-colors"
                        :class="
                            activeFilter === filter.value
                                ? 'border-brand-600 bg-brand-600 text-white'
                                : 'border-border bg-surface text-body hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700'
                        "
                        @click="activeFilter = filter.value"
                    >
                        {{ filter.label }}
                    </button>
                </div>

                <!-- ==================== Conversations ==================== -->

                <div
                    v-if="filteredConversations.length"
                    class="overflow-hidden rounded-2xl border border-border bg-surface shadow-sm"
                >
                    <Link
                        v-for="conversation in filteredConversations"
                        :key="conversation.id"
                        :href="`/messages/${conversation.id}`"
                        class="flex items-center gap-4 border-b border-border p-4 transition-colors last:border-b-0 hover:bg-background sm:p-5"
                    >
                        <!-- ==================== Conversation Avatar ==================== -->

                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-full bg-brand-50 text-brand-600"
                        >
                            <img
                                v-if="
                                    getConversationUser(conversation)
                                        ?.profile_image
                                "
                                :src="`/storage/${getConversationUser(conversation)?.profile_image}`"
                                :alt="`${getConversationUser(conversation)?.first_name} ${getConversationUser(conversation)?.last_name}`"
                                class="h-full w-full object-cover"
                            />

                            <!-- ==================== Customer Support ==================== -->

                            <User
                                v-else-if="
                                    !getConversationUser(conversation)
                                        ?.profile_image
                                "
                                :size="21"
                                :stroke-width="2"
                            />
                        </div>

                        <!-- ==================== Conversation Information ==================== -->

                        <div class="min-w-0 flex-1">
                            <h2
                                class="truncate text-sm font-bold text-heading sm:text-base"
                            >
                                {{
                                    getConversationUser(conversation)
                                        ? `${getConversationUser(conversation)?.first_name} ${getConversationUser(conversation)?.last_name} `
                                        : "Unknown Customer"
                                }}
                            </h2>
                            <span
                                class="mt-1 inline-flex w-fit rounded-full bg-brand-50 px-2.5 py-1 text-xs font-semibold capitalize text-brand-700"
                            >
                                {{ getConversationRole(conversation) }}
                            </span>

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
                        No conversations found
                    </h2>

                    <p
                        class="mx-auto mt-2 max-w-sm text-sm leading-6 text-muted"
                    >
                        Support conversations will appear here when users
                        contact DELIVER.
                    </p>
                </div>
            </div>
        </main>
    </AdminLayout>
</template>
