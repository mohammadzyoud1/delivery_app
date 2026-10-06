<script setup lang="ts">
// ==================== Imports ====================

import { ref } from "vue";
import { Camera, Trash2, Upload } from "lucide-vue-next";
import { useForm, usePage } from "@inertiajs/vue3";
import Button from "@/Components/UI/Button.vue";
import { t } from "@/Utils/i18n";

// ==================== Types ====================

interface AuthUser {
    profile_image: string | null;
}

interface PageProps {
    [key: string]: unknown;

    auth: {
        user: AuthUser | null;
    };
}
const page = usePage<PageProps>();

const user = page.props.auth.user;
const selectedFile = ref<File | null>(null);
const previewUrl = ref<string | null>(null);

// ==================== File Selection ====================

const handleFileChange = (event: Event) => {
    const input = event.target as HTMLInputElement;

    const file = input.files?.[0];

    if (!file) {
        return;
    }

    selectedFile.value = file;

    // ==================== Preview ====================

    previewUrl.value = URL.createObjectURL(file);
};

// ==================== Remove Preview ====================

const removePreview = () => {
    selectedFile.value = null;
    previewUrl.value = null;
};
const form = useForm({
    _method: "PATCH",
    profile_image: null as File | null,
});
const uploadPicture = () => {
    if (!selectedFile.value) {
        console.log("heheheehhe");
        return;
    }

    form.profile_image = selectedFile.value;

    form.post("/profile/picture", {
        forceFormData: true,
    });
};
</script>

<template>
    <div>
        <!-- ==================== Header ==================== -->

        <div>
            <h2 class="text-lg font-bold text-heading">
                {{ t("profilePic") }}
            </h2>

            <p class="mt-1 text-sm text-muted">
                {{ t("profilePicDescription") }}
            </p>
        </div>

        <!-- ==================== Picture Area ==================== -->

        <div class="mt-8 flex flex-col items-center">
            <!-- ==================== Preview ==================== -->

            <div
                class="relative flex h-32 w-32 items-center justify-center overflow-hidden rounded-full border-2 border-dashed border-border bg-background"
            >
                <img
                    v-if="previewUrl"
                    :src="previewUrl"
                    alt="Profile preview"
                    class="h-full w-full object-cover"
                />

                <div v-else-if="user?.profile_image" class="h-full w-full">
                    <img
                        :src="`/storage/${user.profile_image}`"
                        alt="Current profile picture"
                        class="h-full w-full object-cover"
                    />
                </div>

                <div v-else class="flex flex-col items-center gap-2 text-muted">
                    <Camera :size="32" :stroke-width="1.5" />

                    <span class="text-xs font-medium"> No picture </span>
                </div>
            </div>

            <!-- ==================== Actions ==================== -->

            <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
                <label
                    class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-border bg-surface px-4 py-2.5 text-sm font-semibold text-body transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700"
                >
                    <Upload :size="17" :stroke-width="2" />

                    {{ t("choosePic") }}

                    <input
                        type="file"
                        accept="image/*"
                        class="hidden"
                        @change="handleFileChange"
                    />
                </label>

                <Button
                    v-if="selectedFile"
                    type="button"
                    variant="primary"
                    :loading="form.processing"
                    :disabled="form.processing"
                    @click="uploadPicture"
                >
                    {{ form.processing ? t("uploading") : t("savePic") }}
                    <p
                        v-if="form.errors.profile_image"
                        class="mt-3 text-sm text-red-500"
                    >
                        {{ form.errors.profile_image }}
                    </p>
                </Button>

                <button
                    v-if="previewUrl"
                    type="button"
                    class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-100"
                    @click="removePreview"
                >
                    <Trash2 :size="17" :stroke-width="2" />

                    {{ t("remove") }}
                </button>
            </div>

            <!-- ==================== Selected File ==================== -->

            <p
                v-if="selectedFile"
                class="mt-4 max-w-full truncate text-xs text-muted"
            >
                {{ selectedFile.name }}
            </p>

            <!-- ==================== Hint ==================== -->

            <p class="mt-3 text-center text-xs text-muted">
                JPG, PNG, or WEBP. Maximum size: 2 MB.
            </p>
        </div>
    </div>
</template>
