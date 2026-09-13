<script setup lang="ts">
// ==================== Props ====================

interface Props {
    type?: "button" | "submit" | "reset";
    variant?: "primary" | "secondary" | "danger" | "outline";
    size?: "sm" | "md" | "lg";
    loading?: boolean;
    disabled?: boolean;
    fullWidth?: boolean;
}

withDefaults(defineProps<Props>(), {
    type: "button",
    variant: "primary",
    size: "md",
    loading: false,
    disabled: false,
    fullWidth: false,
});
</script>

<template>
    <!-- ==================== Button ==================== -->

    <button
        :type="type"
        :disabled="disabled || loading"
        :class="[
            // Base styles
            'inline-flex items-center justify-center gap-2 rounded-xl font-semibold transition active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-60',

            // Width
            fullWidth ? 'w-full' : 'w-auto',

            // Sizes
            {
                'px-3 py-2 text-xs sm:text-sm': size === 'sm',
                'px-4 py-3 text-sm': size === 'md',
                'px-5 py-3.5 text-sm sm:text-base': size === 'lg',
            },

            // Variants
            {
                'bg-gray-900 text-white hover:bg-gray-800':
                    variant === 'primary',

                'bg-gray-100 text-gray-900 hover:bg-gray-200':
                    variant === 'secondary',

                'bg-red-600 text-white hover:bg-red-700': variant === 'danger',

                'border border-gray-200 bg-white text-gray-900 hover:bg-gray-50':
                    variant === 'outline',
            },
        ]"
    >
        <!-- ==================== Loading Spinner ==================== -->

        <span
            v-if="loading"
            class="h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent"
        ></span>

        <!-- ==================== Button Content ==================== -->

        <slot />
    </button>
</template>
