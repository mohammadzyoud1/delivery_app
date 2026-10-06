<script setup lang="ts">
interface Props {
    title: string;
    subtitle: string;
    onBack?: () => void;
}
import { t } from "@/Utils/i18n";

withDefaults(defineProps<Props>(), {
    onBack: undefined,
});
</script>

<template>
    <div class="relative text-center">
        <!-- Back button -->
        <button
            v-if="onBack"
            type="button"
            class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-700 transition hover:bg-gray-200"
            aria-label="Go back"
            @click="onBack"
        >
            ←
        </button>

        <!-- Title -->
        <h1 class="text-2xl font-bold text-gray-900">
            {{ t(title) }}
        </h1>

        <!-- Subtitle -->
        <p class="mt-2 text-sm text-gray-500">
            {{ t(subtitle) }}
        </p>
    </div>
</template>
