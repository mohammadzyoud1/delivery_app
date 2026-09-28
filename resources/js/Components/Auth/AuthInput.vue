<script setup lang="ts">
import type { Component } from "vue";

interface Props {
    modelValue: string;
    label: string;
    type?: string;
    placeholder?: string;
    icon?: Component;
    error?: string;
    disabled?: boolean;
}

withDefaults(defineProps<Props>(), {
    type: "text",
    placeholder: "",
    icon: undefined,
    error: "",
});

const emit = defineEmits<{
    "update:modelValue": [value: string];
}>();
</script>

<template>
    <div class="space-y-2">
        <label class="block text-sm font-semibold text-gray-800">
            {{ label }}
        </label>

        <div class="relative">
            <component
                v-if="icon"
                :is="icon"
                :size="18"
                class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"
            />

            <input
                :type="type"
                :value="modelValue"
                :placeholder="placeholder"
                :class="[
                    'w-full rounded-xl border py-3.5 text-sm outline-none transition',
                    icon ? 'pl-11' : 'px-4',
                    error
                        ? 'border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-100'
                        : 'border-gray-200 focus:border-gray-400 focus:ring-2 focus:ring-gray-100',
                ]"
                @input="
                    emit(
                        'update:modelValue',
                        ($event.target as HTMLInputElement).value,
                    )
                "
                :disabled="disabled"
            />
        </div>

        <p v-if="error" class="text-sm text-red-500">
            {{ error }}
        </p>
    </div>
</template>
