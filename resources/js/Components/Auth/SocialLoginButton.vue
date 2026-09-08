<script setup lang="ts">
interface Props {
    provider: "Google" | "Apple";
}

defineProps<Props>();
</script>

<template>
    <button
        type="button"
        class="flex w-full items-center justify-center gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3.5 text-sm font-semibold text-gray-800 transition hover:bg-gray-50"
    >
        <!-- Google -->
        <svg
            v-if="provider === 'Google'"
            class="h-5 w-5"
            viewBox="0 0 24 24"
            aria-hidden="true"
        >
            <path
                fill="#4285F4"
                d="M21.35 12.23c0-.79-.07-1.55-.23-2.27H12v4.3h5.24a4.48 4.48 0 0 1-1.94 2.94v2.45h3.14c1.84-1.7 2.91-4.2 2.91-7.42Z"
            />
            <path
                fill="#34A853"
                d="M12 21.75c2.63 0 4.84-.87 6.45-2.36l-3.14-2.45c-.87.58-1.98.93-3.31.93-2.54 0-4.69-1.72-5.46-4.03H3.3v2.53A9.75 9.75 0 0 0 12 21.75Z"
            />
            <path
                fill="#FBBC05"
                d="M6.54 13.84A5.86 5.86 0 0 1 6.23 12c0-.64.11-1.26.31-1.84V7.63H3.3A9.75 9.75 0 0 0 2.25 12c0 1.57.38 3.05 1.05 4.37l3.24-2.53Z"
            />
            <path
                fill="#EA4335"
                d="M12 6.13c1.43 0 2.71.49 3.72 1.46l2.79-2.79C16.84 3.22 14.63 2.25 12 2.25a9.75 9.75 0 0 0-8.7 5.38l3.24 2.53C7.31 7.85 9.46 6.13 12 6.13Z"
            />
        </svg>

        <!-- Apple -->
        <svg
            v-else
            class="h-5 w-5"
            viewBox="0 0 24 24"
            fill="currentColor"
            aria-hidden="true"
        >
            <path
                d="M17.05 12.54c-.02-2.08 1.7-3.08 1.78-3.13a3.82 3.82 0 0 0-3.01-1.63c-1.27-.13-2.49.76-3.14.76-.66 0-1.67-.75-2.74-.73a4.04 4.04 0 0 0-3.39 2.07c-1.46 2.53-.37 6.26 1.04 8.31.7 1 1.51 2.12 2.58 2.08 1.04-.04 1.43-.67 2.69-.67 1.25 0 1.61.67 2.7.65 1.12-.02 1.82-1.02 2.5-2.03a8.32 8.32 0 0 0 1.14-2.35 3.63 3.63 0 0 1-2.15-3.33ZM15 6.48a3.65 3.65 0 0 0 .83-2.62 3.72 3.72 0 0 0-2.4 1.24 3.48 3.48 0 0 0-.85 2.52A3.07 3.07 0 0 0 15 6.48Z"
            />
        </svg>

        Continue with {{ provider }}
    </button>
</template>
