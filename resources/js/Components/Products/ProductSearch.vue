<script setup lang="ts">
// ==================== Imports ====================

import { Search } from "lucide-vue-next";

// ==================== Props ====================

interface Props {
    placeholder?: string;
}

withDefaults(defineProps<Props>(), {
    placeholder: "Search products...",
});

// ==================== Model ====================

const model = defineModel<string>({ default: "" });
</script>

<template>
    <!-- ==================== Search Input ==================== -->

    <div class="group relative w-full">
        <!-- ==================== Search Icon ==================== -->

        <Search
            :size="20"
            :stroke-width="2"
            class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-muted transition-colors duration-200 group-focus-within:text-brand-600"
        />

        <!-- ==================== Input ==================== -->

        <input
            v-model="model"
            type="search"
            :placeholder="placeholder"
            class="w-full rounded-2xl border border-border bg-surface py-3.5 pl-11 pr-4 text-sm text-heading shadow-sm outline-none transition-all duration-200 placeholder:text-muted hover:border-slate-300 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 sm:py-4 sm:text-base"
        />
    </div>
</template>
