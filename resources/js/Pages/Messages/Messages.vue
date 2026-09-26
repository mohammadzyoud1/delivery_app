<script setup lang="ts">
// ==================== Imports ====================

import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import { Link } from "@inertiajs/vue3";
import { MessageCircle, Truck, Headphones } from "lucide-vue-next";

// ==================== Types ====================

interface User {
    id: number;
    first_name: string;
    last_name: string;
}

interface Order {
    id: number;
    status: string;
}

interface Message {
    id: number;
    message: string;
    sender_id: number;
    created_at: string;
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
    conversations: Conversation[];
}>();
</script>

<template>
    <CustomerLayout>
        <!-- ==================== Contact Support ==================== -->

        <div class="mb-6">
            <Link
                href="/messages/support"
                class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-brand-700"
            >
                <Headphones :size="18" />

                Contact Support
            </Link>
        </div>
        <!-- ==================== Messages Page ==================== -->

        <main class="min-h-screen bg-background">
            <div class="mx-auto w-full max-w-3xl px-5 py-6 sm:px-8 sm:py-8">
                <!-- ==================== Header ==================== -->

                <div class="mb-6">
                    <h1
                        class="text-2xl font-extrabold tracking-tight text-heading sm:text-3xl"
                    >
                        Messages
                    </h1>

                    <p class="mt-1 text-sm text-muted">
                        Contact DELIVER support or your delivery person.
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
                        <!-- ==================== Icon ==================== -->

                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600"
                        >
                            <Truck
                                v-if="conversation.type === 'delivery'"
                                :size="22"
                            />

                            <MessageCircle v-else :size="22" />
                        </div>

                        <!-- ==================== Conversation Info ==================== -->

                        <div class="min-w-0 flex-1">
                            <h2
                                class="truncate text-sm font-bold text-heading sm:text-base"
                            >
                                {{
                                    conversation.type === "delivery"
                                        ? `Order #${conversation.order_id}`
                                        : "DELIVER Admin"
                                }}
                            </h2>

                            <p class="mt-1 truncate text-sm text-muted">
                                {{
                                    conversation.type === "delivery"
                                        ? "Delivery conversation"
                                        : "Platform Support"
                                }}
                            </p>
                        </div>

                        <!-- ==================== Order Status ==================== -->

                        <span
                            v-if="
                                conversation.type === 'delivery' &&
                                conversation.order
                            "
                            class="hidden shrink-0 rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold capitalize text-brand-700 sm:inline-flex"
                        >
                            {{ conversation.order.status }}
                        </span>
                    </Link>
                </div>

                <!-- ==================== Empty State ==================== -->

                <div
                    v-else
                    class="rounded-2xl border border-border bg-surface px-6 py-12 text-center shadow-sm"
                >
                    <div
                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-brand-50 text-brand-600"
                    >
                        <MessageCircle :size="26" />
                    </div>

                    <h2 class="mt-4 text-base font-bold text-heading">
                        No conversations yet
                    </h2>

                    <p
                        class="mx-auto mt-2 max-w-sm text-sm leading-6 text-muted"
                    >
                        Start a conversation with DELIVER support when you need
                        help.
                    </p>
                </div>
            </div>
        </main>
    </CustomerLayout>
</template>
