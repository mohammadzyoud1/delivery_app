<script setup lang="ts">
// ==================== Imports ====================

import { ref } from "vue";
import { Camera, Trash2, User } from "lucide-vue-next";

// ==================== State ====================

const previewUrl = ref<string | null>(null);

// ==================== File Selection ====================

const handleFileChange = (event: Event) => {
    const input = event.target as HTMLInputElement;

    const file = input.files?.[0];

    if (!file) {
        return;
    }

    previewUrl.value = URL.createObjectURL(file);
};

// ==================== Remove Preview ====================

const removePicture = () => {
    previewUrl.value = null;
};
</script>

<template>
    <div>
        <!-- ==================== Header ==================== -->

        <div>
            <h2 class="text-lg font-bold text-heading">Profile Picture</h2>

            <p class="mt-1 text-sm text-muted">
                Manage the picture displayed on your profile.
            </p>
        </div>

        <!-- ==================== Profile Picture ==================== -->

        <div class="mt-8 flex flex-col items-center">
            <div
                class="relative flex h-32 w-32 items-center justify-center overflow-hidden rounded-full border-4 border-white bg-background shadow-md ring-1 ring-border"
            >
                <!-- ==================== Preview ==================== -->

                <img
                    v-if="previewUrl"
                    :src="previewUrl"
                    alt="Profile preview"
                    class="h-full w-full object-cover"
                />

                <!-- ==================== Empty State ==================== -->

                <User
                    v-else
                    :size="48"
                    :stroke-width="1.5"
                    class="text-muted"
                />
            </div>

            <!-- ==================== Actions ==================== -->

            <div class="mt-6 flex flex-wrap justify-center gap-3">
                <label
                    class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-border bg-surface px-4 py-2.5 text-sm font-semibold text-body transition-colors duration-150 hover:bg-background"
                >
                    <Camera :size="18" :stroke-width="2" />

                    Choose Picture

                    <input
                        type="file"
                        accept="image/*"
                        class="hidden"
                        @change="handleFileChange"
                    />
                </label>

                <button
                    v-if="previewUrl"
                    type="button"
                    class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-600 transition-colors duration-150 hover:bg-red-100"
                    @click="removePicture"
                >
                    <Trash2 :size="18" :stroke-width="2" />

                    Remove
                </button>
            </div>

            <!-- ==================== Help Text ==================== -->

            <p class="mt-4 max-w-sm text-center text-xs leading-5 text-muted">
                Choose a clear profile picture. JPG, PNG, and other common image
                formats are supported.
            </p>
        </div>
    </div>
</template>
