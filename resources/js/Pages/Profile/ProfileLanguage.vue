<script setup lang="ts">
// ==================== Imports ====================

import { ref, inject } from "vue";
import { saveLanguage, type Language } from "@/Utils/language";
import { t } from "@/Utils/i18n";

// ==================== State ====================

const selectedLanguage = ref<Language>(
    inject<Language>("language") ?? "English",
);

// ==================== Languages ====================

const languages: {
    value: Language;
    label: string;
}[] = [
    {
        value: "English",
        label: "English",
    },
    {
        value: "Arabic",
        label: "العربية",
    },
];

const changeLanguage = (language: Language) => {
    selectedLanguage.value = language;

    saveLanguage(language);
};
</script>

<template>
    <div>
        <!-- ==================== Header ==================== -->

        <div>
            <h2 class="text-lg font-bold text-heading">{{ t("language") }}</h2>

            <p class="mt-1 text-sm text-muted">
                {{ t("chooseLanguage") }}
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
                @click="changeLanguage(language.value)"
            >
                <span class="text-sm font-semibold text-heading">
                    {{ language.label }}
                </span>

                <span
                    v-if="selectedLanguage === language.value"
                    class="text-sm font-bold text-brand-600"
                >
                    {{ t("selected") }}
                </span>
            </button>
        </div>
    </div>
</template>
