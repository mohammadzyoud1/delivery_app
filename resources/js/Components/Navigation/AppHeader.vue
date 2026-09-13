<script setup lang="ts">
import { User } from "lucide-vue-next";

// ==================== Props ====================

interface Props {
    title?: string;
}

withDefaults(defineProps<Props>(), {
    title: "Deliver",
});
</script>

<template>
    <!-- ==================== Header ==================== -->

    <header class="border-b border-gray-100 bg-white">
        <div
            class="mx-auto flex h-16 w-full max-w-7xl items-center justify-between px-5 sm:px-8"
        >
            <!-- ==================== App Name ==================== -->

            <h1 class="text-xl font-bold tracking-tight text-gray-900">
                {{ title }}
            </h1>

            <!-- ==================== Profile Button ==================== -->

            <button
                type="button"
                aria-label="Open profile"
                class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-700 transition hover:bg-gray-200"
            >
                <User :size="20" :stroke-width="2" />
            </button>
        </div>
    </header>
</template>
