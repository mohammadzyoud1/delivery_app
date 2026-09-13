<script setup lang="ts">
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

    <div class="relative w-full">
        <!-- ==================== Search Icon ==================== -->

        <Search
            :size="20"
            :stroke-width="2"
            class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"
        />

        <!-- ==================== Input ==================== -->

        <input
            v-model="model"
            type="search"
            :placeholder="placeholder"
            class="w-full rounded-xl border border-gray-200 bg-white py-3.5 pl-11 pr-4 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-400 focus:ring-2 focus:ring-gray-100 sm:text-base"
        />
    </div>
</template>
