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
        <!-- ==================== Header ==================== -->

        <div class="mb-6">
            <Link
                href="/admin/categories"
                class="mb-4 inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900"
            >
                <ArrowLeft class="h-4 w-4" />
                Back to Categories
            </Link>

            <p
                class="text-xs font-semibold uppercase tracking-wide text-gray-400"
            >
                Catalog
            </p>

            <h1 class="mt-1 text-2xl font-bold text-gray-900">
                Create Category
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Add a new category or subcategory.
            </p>
        </div>

        <!-- ==================== Form ==================== -->

        <form
            class="max-w-3xl rounded-2xl border border-gray-200 bg-white p-5 sm:p-6"
            @submit.prevent="submit"
        >
            <!-- ==================== Name ==================== -->

            <div>
                <label for="name" class="text-sm font-semibold text-gray-900">
                    Category Name
                </label>

                <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    placeholder="e.g. Electronics"
                    class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-gray-900"
                />

                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                    {{ form.errors.name }}
                </p>
            </div>

            <!-- ==================== Parent Category ==================== -->

            <div class="mt-5">
                <label
                    for="parent_id"
                    class="text-sm font-semibold text-gray-900"
                >
                    Parent Category
                </label>

                <select
                    id="parent_id"
                    v-model="form.parent_id"
                    class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-gray-900"
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

                <p class="mt-1 text-xs text-gray-500">
                    Select a parent if this should be a subcategory.
                </p>

                <p
                    v-if="form.errors.parent_id"
                    class="mt-1 text-sm text-red-600"
                >
                    {{ form.errors.parent_id }}
                </p>
            </div>

            <!-- ==================== Description ==================== -->

            <div class="mt-5">
                <label
                    for="description"
                    class="text-sm font-semibold text-gray-900"
                >
                    Description
                </label>

                <textarea
                    id="description"
                    v-model="form.description"
                    rows="4"
                    placeholder="Describe this category..."
                    class="mt-2 w-full resize-none rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-gray-900"
                ></textarea>

                <p
                    v-if="form.errors.description"
                    class="mt-1 text-sm text-red-600"
                >
                    {{ form.errors.description }}
                </p>
            </div>

            <!-- ==================== Image ==================== -->

            <div class="mt-5">
                <label for="image" class="text-sm font-semibold text-gray-900">
                    Category Image
                </label>

                <label
                    for="image"
                    class="mt-2 flex cursor-pointer items-center gap-3 rounded-xl border border-dashed border-gray-300 p-4 transition hover:border-gray-900"
                >
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100"
                    >
                        <ImagePlus class="h-5 w-5 text-gray-500" />
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-900">
                            Choose an image
                        </p>

                        <p class="text-xs text-gray-500">
                            JPG, PNG, WEBP up to 2 MB
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
                            ($event.target as HTMLInputElement).files?.[0] ??
                            null
                    "
                />

                <p v-if="form.errors.image" class="mt-1 text-sm text-red-600">
                    {{ form.errors.image }}
                </p>

                <p v-if="form.image" class="mt-2 text-xs text-gray-500">
                    Selected: {{ form.image.name }}
                </p>
            </div>

            <!-- ==================== Status ==================== -->

            <label
                class="mt-5 flex items-center gap-3 rounded-xl bg-gray-50 p-4"
            >
                <input
                    v-model="form.is_active"
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300"
                />

                <div>
                    <p class="text-sm font-semibold text-gray-900">
                        Active category
                    </p>

                    <p class="text-xs text-gray-500">
                        Active categories can be used by sellers.
                    </p>
                </div>
            </label>

            <!-- ==================== Actions ==================== -->

            <div
                class="mt-6 flex flex-col-reverse gap-3 border-t border-gray-100 pt-6 sm:flex-row sm:justify-end"
            >
                <Link
                    href="/admin/categories"
                    class="rounded-xl border border-gray-200 px-5 py-3 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50"
                >
                    Cancel
                </Link>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    {{ form.processing ? "Creating..." : "Create Category" }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>
