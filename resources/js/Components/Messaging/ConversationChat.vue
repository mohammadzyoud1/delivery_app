<script setup lang="ts">
// ==================== Imports ====================

import { router } from "@inertiajs/vue3";
import { Send } from "lucide-vue-next";
import { nextTick, onMounted, onUnmounted, ref, watch } from "vue";
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
const isSending = ref(false);
// ==================== Auto Scroll ====================

const messagesContainer = ref<HTMLElement | null>(null);

const isNearBottom = (): boolean => {
    const el = messagesContainer.value;

    if (!el) {
        return true;
    }

    return el.scrollHeight - el.scrollTop - el.clientHeight < 120;
};

const scrollToBottom = (smooth = false) => {
    nextTick(() => {
        const el = messagesContainer.value;

        el?.scrollTo({
            top: el.scrollHeight,
            behavior: smooth ? "smooth" : "auto",
        });
    });
};

// ==================== Stick To Bottom ====================

let resizeObserver: ResizeObserver | null = null;

// True until the user scrolls up to read old messages
let stickToBottom = true;

const onMessagesScroll = () => {
    stickToBottom = isNearBottom();
};

onMounted(() => {
    scrollToBottom();

    // The parent page sets the chat height after this component mounts,
    // so scroll to the bottom again whenever the box is resized.
    if (messagesContainer.value) {
        resizeObserver = new ResizeObserver(() => {
            const el = messagesContainer.value;

            if (stickToBottom && el) {
                el.scrollTop = el.scrollHeight;
            }
        });

        resizeObserver.observe(messagesContainer.value);
    }
});

onUnmounted(() => {
    resizeObserver?.disconnect();
    resizeObserver = null;
});

// Scroll when messages change
watch(
    () => props.conversation.messages.length,
    () => {
        const last = props.conversation.messages.at(-1);
        const sentByMe = last?.sender_id === props.currentUserId;

        if (sentByMe || isNearBottom()) {
            scrollToBottom(true);
        }
    },
    { flush: "pre" },
);
// refresh messages

const refreshMessages = () => {
    router.reload({
        only: ["conversation"],
        preserveScroll: true,
        preserveState: true,
    });
};

onMounted(() => {
    window.Echo.private(`conversation.${props.conversation.id}`).listen(
        ".App\\Events\\MessageSent",
        refreshMessages,
    );
});

onUnmounted(() => {
    window.Echo.leave(`conversation.${props.conversation.id}`);
});
// ==================== Send Message ====================

const sendMessage = () => {
    if (!message.value.trim() || isSending.value) {
        return;
    }
    isSending.value = true;
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
            onFinish: () => {
                isSending.value = false;
            },
        },
    );
};
</script>

<template>
    <!-- ==================== Chat ==================== -->

    <div
        class="flex h-full min-h-0 min-w-0 max-w-full flex-col overflow-hidden rounded-2xl border border-border bg-surface shadow-sm"
    >
        <!-- ==================== Messages ==================== -->

        <div
            ref="messagesContainer"
            @scroll="onMessagesScroll"
            class="min-h-0 min-w-0 flex-1 space-y-4 overflow-x-hidden overflow-y-auto overscroll-contain p-4 sm:p-6"
        >
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
                    class="min-w-0 max-w-[85%] break-words whitespace-pre-wrap rounded-2xl px-4 py-3 text-sm leading-6 sm:max-w-[70%]"
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
                    :disabled="!message.trim() || isSending"
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-600 text-white transition-colors hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-40"
                    aria-label="Send message"
                >
                    <Send :size="19" :stroke-width="2" />
                </button>
            </div>
        </form>
    </div>
</template>
