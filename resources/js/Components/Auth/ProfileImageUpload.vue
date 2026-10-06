<script setup lang="ts">
import { ref, onBeforeUnmount } from "vue";
import { Camera } from "lucide-vue-next";
import { t } from "@/Utils/i18n";

interface Props {
    modelValue?: File | null;
    label?: string;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: null,
    label: t("addProfPic"),
});

const emit = defineEmits<{
    "update:modelValue": [file: File | null];
}>();

const fileInput = ref<HTMLInputElement | null>(null);
const previewUrl = ref<string | null>(null);

const openFilePicker = () => {
    fileInput.value?.click();
};

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (!file) {
        return;
    }

    if (!file.type.startsWith("image/")) {
        return;
    }

    if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value);
    }

    previewUrl.value = URL.createObjectURL(file);

    emit("update:modelValue", file);
};

onBeforeUnmount(() => {
    if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value);
    }
});
</script>

<template>
    <div class="flex flex-col items-center">
        <!-- Upload area -->
        <button
            type="button"
            class="relative flex h-28 w-28 items-center justify-center overflow-hidden rounded-full border-2 border-dashed border-gray-300 bg-gray-50 transition hover:border-gray-400 hover:bg-gray-100"
            @click="openFilePicker"
        >
            <!-- Preview -->
            <img
                v-if="previewUrl"
                :src="previewUrl"
                alt="Profile preview"
                class="h-full w-full object-cover"
            />

            <!-- Empty state -->
            <div
                v-else
                class="flex flex-col items-center justify-center text-gray-400"
            >
                <Camera :size="28" />
            </div>

            <!-- Camera badge -->
            <span
                class="absolute bottom-1 right-1 flex h-8 w-8 items-center justify-center rounded-full bg-gray-900 text-white shadow"
            >
                <Camera :size="16" />
            </span>
        </button>

        <!-- Label -->
        <button
            type="button"
            class="mt-3 text-sm font-medium text-gray-500 transition hover:text-gray-700"
            @click="openFilePicker"
        >
            {{ label }}
        </button>

        <!-- Hidden file input -->
        <input
            ref="fileInput"
            type="file"
            accept="image/*"
            class="hidden"
            @change="handleFileChange"
        />
    </div>
</template>
