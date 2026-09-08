<script setup lang="ts">
import { ref } from "vue";
import { Eye, EyeOff, Lock } from "lucide-vue-next";

interface Props {
    modelValue: string;
    label: string;
    placeholder?: string;
    error?: string;
}

withDefaults(defineProps<Props>(), {
    placeholder: "",
    error: "",
});

const emit = defineEmits<{
    "update:modelValue": [value: string];
}>();

const showPassword = ref(false);
</script>

<template>
    <div class="space-y-2">
        <label class="block text-sm font-semibold text-gray-800">
            {{ label }}
        </label>

        <div class="relative">
            <!-- Lock icon -->
            <Lock
                :size="18"
                class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"
            />

            <!-- Password input -->
            <input
                :type="showPassword ? 'text' : 'password'"
                :value="modelValue"
                :placeholder="placeholder"
                class="w-full rounded-xl border border-gray-200 py-3.5 pl-11 pr-12 text-sm outline-none transition focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                @input="
                    emit(
                        'update:modelValue',
                        ($event.target as HTMLInputElement).value,
                    )
                "
            />

            <!-- Show / hide button -->
            <button
                type="button"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 transition hover:text-gray-600"
                :aria-label="showPassword ? 'Hide password' : 'Show password'"
                @click="showPassword = !showPassword"
            >
                <EyeOff v-if="showPassword" :size="18" />
                <Eye v-else :size="18" />
            </button>
        </div>
        <p v-if="error" class="text-sm text-red-500">
            {{ error }}
        </p>
    </div>
</template>
