<script setup lang="ts">
// ==================== Imports ====================

import { router } from "@inertiajs/vue3";
import { Send } from "lucide-vue-next";
import { ref } from "vue";

// ==================== Types ====================

interface User {
    id: number;
    first_name: string;
    last_name: string;
    email?: string;
    profile_image: string | null;
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
    currentUserId: number | null;
    emptyTitle?: string;
    emptyMessage?: string;
}>();

// ==================== State ====================

const message = ref("");

// ==================== Send Message ====================

const sendMessage = () => {
    if (!message.value.trim()) {
        return;
    }

    router.post(
        `/messages/${props.conversation.id}`,
        {
            message: message.value,
        },
        {
            preserveScroll: true,

            onSuccess: () => {
                message.value = "";
            },
        },
    );
};
</script>

<template>
    <!-- ==================== Chat ==================== -->

    <div
        class="flex min-h-[60vh] flex-col overflow-hidden rounded-2xl border border-border bg-surface shadow-sm"
    >
        <!-- ==================== Messages ==================== -->

        <div class="flex-1 space-y-4 overflow-y-auto p-4 sm:p-6">
            <!-- ==================== Empty State ==================== -->

            <div
                v-if="!props.conversation.messages.length"
                class="flex min-h-[45vh] items-center justify-center text-center"
            >
                <div>
                    <h2 class="text-sm font-bold text-heading">
                        {{ emptyTitle ?? "No messages yet" }}
                    </h2>

                    <p class="mt-1 text-sm text-muted">
                        {{ emptyMessage ?? "Start the conversation." }}
                    </p>
                </div>
            </div>

            <!-- ==================== Messages ==================== -->

            <div
                v-for="item in props.conversation.messages"
                :key="item.id"
                class="flex"
                :class="
                    item.sender_id === props.currentUserId
                        ? 'justify-end'
                        : 'justify-start'
                "
            >
                <div
                    class="max-w-[85%] rounded-2xl px-4 py-3 text-sm leading-6 sm:max-w-[70%]"
                    :class="
                        item.sender_id === props.currentUserId
                            ? 'rounded-br-md bg-brand-600 text-white'
                            : 'rounded-bl-md bg-background text-body'
                    "
                >
                    {{ item.message }}
                </div>
            </div>
        </div>

        <!-- ==================== Message Input ==================== -->

        <form
            class="border-t border-border bg-surface p-3 sm:p-4"
            @submit.prevent="sendMessage"
        >
            <div class="flex items-end gap-2">
                <textarea
                    v-model="message"
                    rows="1"
                    maxlength="5000"
                    placeholder="Write a message..."
                    class="min-h-11 flex-1 resize-none rounded-xl border border-border bg-background px-4 py-3 text-sm text-body outline-none transition-colors placeholder:text-muted focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"
                    @keydown.enter.exact.prevent="sendMessage"
                ></textarea>

                <button
                    type="submit"
                    :disabled="!message.trim()"
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-600 text-white transition-colors hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-40"
                    aria-label="Send message"
                >
                    <Send :size="19" :stroke-width="2" />
                </button>
            </div>
        </form>
    </div>
</template>
```
