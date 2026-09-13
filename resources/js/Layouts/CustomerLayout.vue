<script setup lang="ts">
import AppHeader from "@/Components/Navigation/AppHeader.vue";
import BottomNavigation from "@/Components/Navigation/BottomNavigation.vue";
</script>

<template>
    <!-- ==================== Customer Layout ==================== -->

    <div class="min-h-screen bg-gray-50">
        <!-- ==================== Header ==================== -->

        <AppHeader />

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
