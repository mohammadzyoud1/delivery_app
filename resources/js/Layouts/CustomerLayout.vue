<script setup lang="ts">
import AppHeader from "@/Components/Navigation/AppHeader.vue";
import BottomNavigation from "@/Components/Navigation/BottomNavigation.vue";
import { usePage } from "@inertiajs/vue3";

const page = usePage<{
    flash: {
        success?: string;
        error?: string;
    };
}>();
</script>

<template>
    <!-- ==================== Customer Layout ==================== -->

    <div class="min-h-screen bg-gray-50">
        <!-- ==================== Header ==================== -->

        <AppHeader />
        <!-- ==================== Flash Messages ==================== -->

        <div class="mx-auto w-full max-w-7xl px-5 pt-4 sm:px-8">
            <div
                v-if="page.props.flash?.success"
                class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700"
            >
                {{ page.props.flash.success }}
            </div>

            <div
                v-if="page.props.flash?.error"
                class="mt-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700"
            >
                {{ page.props.flash.error }}
            </div>
        </div>

        <!-- ==================== Page Content ==================== -->

        <main class="pb-20">
            <div class="mx-auto w-full max-w-7xl px-5 py-6 sm:px-8">
                <slot />
            </div>
        </main>

        <!-- ==================== Bottom Navigation ==================== -->

        <BottomNavigation />
    </div>
</template>
