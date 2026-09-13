<script setup lang="ts">
import { Link } from "@inertiajs/vue3";

// ==================== Types ====================

interface Category {
    id: number;
    name: string;
    image: string | null;
}

// ==================== Props ====================

interface Props {
    categories: Category[];
}

defineProps<Props>();
</script>

<template>
    <!-- ==================== Category Slider ==================== -->

    <section>
        <!-- ==================== Section Header ==================== -->

        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">Categories</h2>

            <Link
                href="/categories"
                class="text-sm font-semibold text-gray-500 hover:text-gray-900"
            >
                See All
            </Link>
        </div>

        <!-- ==================== Horizontal Categories ==================== -->

        <div
            class="flex gap-3 overflow-x-auto pb-2"
            style="scrollbar-width: none"
        >
            <Link
                v-for="category in categories"
                :key="category.id"
                :href="`/categories/${category.name}`"
                class="flex min-w-[100px] shrink-0 flex-col items-center rounded-xl border border-gray-200 bg-white p-3 transition hover:border-gray-300 hover:bg-gray-50 active:scale-[0.98] sm:min-w-[120px]"
            >
                <!-- ==================== Category Placeholder ==================== -->

                <div
                    class="h-14 w-14 overflow-hidden rounded-full bg-gray-100 sm:h-16 sm:w-16"
                >
                    <img
                        v-if="category.image"
                        :src="category.image"
                        :alt="category.name"
                        class="h-full w-full object-cover"
                    />
                    <span
                        class="mt-2 max-w-full truncate text-center text-sm font-medium text-gray-900"
                    >
                        {{ category.name }}
                    </span>
                </div>

                <!-- ==================== Category Name ==================== -->

                <span
                    class="mt-2 max-w-full truncate text-center text-sm font-medium text-gray-900"
                >
                    {{ category.name }}
                </span>
            </Link>
        </div>
    </section>
</template>
