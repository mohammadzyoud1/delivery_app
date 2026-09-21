<script setup lang="ts">
// ==================== Imports ====================

import { Head, Link, useForm } from "@inertiajs/vue3";
import { ArrowLeft, ImagePlus } from "lucide-vue-next";
import AdminLayout from "@/Layouts/AdminLayout.vue";

// ==================== Types ====================

interface ParentCategory {
    id: number;
    name: string;
}

// ==================== Props ====================

defineProps<{
    parentCategories: ParentCategory[];
}>();

// ==================== Form ====================

const form = useForm({
    name: "",
    description: "",
    parent_id: null as number | null,
    image: null as File | null,
    is_active: true,
});

// ==================== Submit ====================

const submit = () => {
    form.post("/admin/categories", {
        forceFormData: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Create Category" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- ==================== Page Header ==================== -->

            <div>
                <Link
                    href="/admin/categories"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-muted transition hover:text-brand-700"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Back to Categories
                </Link>

                <div class="mt-5">
                    <p
                        class="text-xs font-bold uppercase tracking-[0.16em] text-brand-600"
                    >
                        Catalog
                    </p>

                    <h1
                        class="mt-1 text-2xl font-bold tracking-tight text-heading"
                    >
                        Create Category
                    </h1>

                    <p class="mt-1 text-sm text-muted">
                        Add a new category or subcategory to your catalog.
                    </p>
                </div>
            </div>

            <!-- ==================== Form ==================== -->

            <form
                class="max-w-3xl overflow-hidden rounded-2xl border border-border bg-surface shadow-sm"
                @submit.prevent="submit"
            >
                <!-- ==================== Form Header ==================== -->

                <div
                    class="border-b border-border bg-background px-5 py-5 sm:px-6"
                >
                    <h2 class="font-semibold text-heading">
                        Category Information
                    </h2>

                    <p class="mt-1 text-sm text-muted">
                        Enter the details for your new catalog category.
                    </p>
                </div>

                <div class="space-y-6 p-5 sm:p-6">
                    <!-- ==================== Name ==================== -->

                    <div>
                        <label
                            for="name"
                            class="text-sm font-semibold text-heading"
                        >
                            Category Name
                        </label>

                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            placeholder="e.g. Electronics"
                            class="mt-2 w-full rounded-xl border border-border bg-surface px-4 py-3 text-sm text-heading placeholder:text-muted outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/10"
                            :class="
                                form.errors.name
                                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500/10'
                                    : ''
                            "
                        />

                        <p
                            v-if="form.errors.name"
                            class="mt-1.5 text-sm text-red-600"
                        >
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- ==================== Parent Category ==================== -->

                    <div>
                        <label
                            for="parent_id"
                            class="text-sm font-semibold text-heading"
                        >
                            Parent Category
                        </label>

                        <select
                            id="parent_id"
                            v-model="form.parent_id"
                            class="mt-2 w-full rounded-xl border border-border bg-surface px-4 py-3 text-sm text-heading outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/10"
                            :class="
                                form.errors.parent_id
                                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500/10'
                                    : ''
                            "
                        >
                            <option :value="null">Main Category</option>

                            <option
                                v-for="parent in parentCategories"
                                :key="parent.id"
                                :value="parent.id"
                            >
                                {{ parent.name }}
                            </option>
                        </select>

                        <p class="mt-1.5 text-xs text-muted">
                            Select a parent category if this should be a
                            subcategory.
                        </p>

                        <p
                            v-if="form.errors.parent_id"
                            class="mt-1.5 text-sm text-red-600"
                        >
                            {{ form.errors.parent_id }}
                        </p>
                    </div>

                    <!-- ==================== Description ==================== -->

                    <div>
                        <label
                            for="description"
                            class="text-sm font-semibold text-heading"
                        >
                            Description
                        </label>

                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="5"
                            placeholder="Describe this category..."
                            class="mt-2 w-full resize-none rounded-xl border border-border bg-surface px-4 py-3 text-sm leading-6 text-heading placeholder:text-muted outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/10"
                            :class="
                                form.errors.description
                                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500/10'
                                    : ''
                            "
                        ></textarea>

                        <p
                            v-if="form.errors.description"
                            class="mt-1.5 text-sm text-red-600"
                        >
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <!-- ==================== Image ==================== -->

                    <div>
                        <label
                            for="image"
                            class="text-sm font-semibold text-heading"
                        >
                            Category Image
                        </label>

                        <label
                            for="image"
                            class="mt-2 flex cursor-pointer items-center gap-4 rounded-2xl border border-dashed border-border bg-background p-5 transition hover:border-brand-300 hover:bg-brand-50/40"
                        >
                            <div
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                            >
                                <ImagePlus class="h-5 w-5" />
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-heading">
                                    Choose an image
                                </p>

                                <p class="mt-1 text-xs text-muted">
                                    JPG, PNG or WEBP up to 2 MB
                                </p>

                                <p
                                    v-if="form.image"
                                    class="mt-2 truncate text-xs font-medium text-brand-700"
                                >
                                    Selected: {{ form.image.name }}
                                </p>
                            </div>
                        </label>

                        <input
                            id="image"
                            type="file"
                            accept="image/*"
                            class="hidden"
                            @change="
                                form.image =
                                    ($event.target as HTMLInputElement)
                                        .files?.[0] ?? null
                            "
                        />

                        <p
                            v-if="form.errors.image"
                            class="mt-1.5 text-sm text-red-600"
                        >
                            {{ form.errors.image }}
                        </p>
                    </div>

                    <!-- ==================== Status ==================== -->

                    <label
                        class="flex cursor-pointer items-start gap-3 rounded-2xl border border-border bg-background p-4 transition hover:border-brand-100 hover:bg-brand-50/40"
                    >
                        <input
                            v-model="form.is_active"
                            type="checkbox"
                            class="mt-0.5 h-4 w-4 rounded border-border text-brand-600 focus:ring-brand-500"
                        />

                        <div>
                            <p class="text-sm font-semibold text-heading">
                                Active category
                            </p>

                            <p class="mt-1 text-xs leading-5 text-muted">
                                Active categories can be selected and used by
                                sellers.
                            </p>
                        </div>
                    </label>

                    <!-- ==================== Actions ==================== -->

                    <div
                        class="flex flex-col-reverse gap-3 border-t border-border pt-6 sm:flex-row sm:justify-end"
                    >
                        <Link
                            href="/admin/categories"
                            class="inline-flex items-center justify-center rounded-xl border border-border bg-surface px-5 py-3 text-sm font-semibold text-body transition hover:bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2"
                        >
                            Cancel
                        </Link>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center justify-center rounded-xl bg-brand-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {{
                                form.processing
                                    ? "Creating..."
                                    : "Create Category"
                            }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
