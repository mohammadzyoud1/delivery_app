<script setup lang="ts">
// ==================== Imports ====================

import { ref } from "vue";

// ==================== State ====================

const selectedLanguage = ref("English");

// ==================== Languages ====================

const languages = [
    {
        value: "English",
        label: "English",
    },
    {
        value: "Arabic",
        label: "العربية",
    },
];
</script>

<template>
    <div>
        <!-- ==================== Header ==================== -->

        <div>
            <h2 class="text-lg font-bold text-heading">Language</h2>

            <p class="mt-1 text-sm text-muted">
                Choose the language you want to use in DELIVER.
            </p>
        </div>

        <!-- ==================== Language Options ==================== -->

        <div class="mt-6 space-y-3">
            <button
                v-for="language in languages"
                :key="language.value"
                type="button"
                class="flex w-full items-center justify-between rounded-xl border p-4 text-left transition-colors duration-150"
                :class="
                    selectedLanguage === language.value
                        ? 'border-brand-600 bg-brand-50'
                        : 'border-border bg-surface hover:bg-background'
                "
                @click="selectedLanguage = language.value"
            >
                <span class="text-sm font-semibold text-heading">
                    {{ language.label }}
                </span>

                <span
                    v-if="selectedLanguage === language.value"
                    class="text-sm font-bold text-brand-600"
                >
                    Selected
                </span>
            </button>
        </div>
    </div>
</template>
