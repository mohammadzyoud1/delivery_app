<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { t } from "@/Utils/i18n";
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

        <div class="mb-4 flex items-center justify-between">
            <h2
                class="text-lg font-bold tracking-tight text-heading sm:text-xl"
            >
                {{ t("categories") }}
            </h2>

            <Link
                href="/categories"
                class="text-sm font-semibold text-brand-600 transition-colors duration-200 hover:text-brand-700"
            >
                {{ t("seeAll") }}
            </Link>
        </div>

        <!-- ==================== Horizontal Categories ==================== -->

        <div
            class="flex gap-3 overflow-x-auto pb-2 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
        >
            <Link
                v-for="category in categories"
                :key="category.id"
                :href="`/categories/${category.name}`"
                class="group flex min-w-[104px] shrink-0 flex-col items-center rounded-2xl border border-border bg-surface p-3 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-brand-100 hover:shadow-md active:scale-[0.98] sm:min-w-[120px] sm:p-4"
            >
                <!-- ==================== Category Image ==================== -->

                <div
                    class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-full bg-brand-50 ring-1 ring-border transition-all duration-200 group-hover:ring-brand-100 sm:h-[72px] sm:w-[72px]"
                >
                    <img
                        v-if="category.image"
                        :src="category.image"
                        :alt="category.name"
                        class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                    />

                    <span v-else class="text-lg font-bold text-brand-600">
                        {{ category.name.charAt(0).toUpperCase() }}
                    </span>
                </div>

                <!-- ==================== Category Name ==================== -->

                <span
                    class="mt-3 w-full truncate text-center text-sm font-semibold text-body transition-colors duration-200 group-hover:text-brand-700"
                >
                    {{ t(`${category.name}`) }}
                </span>
            </Link>
        </div>
    </section>
</template>
