<script setup lang="ts">
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

import CategorySlider from "@/Components/Categories/CategorySlider.vue";
import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import ProductSearch from "@/Components/Products/ProductSearch.vue";

interface Category {
    id: number;
    name: string;
    image: string | null;
}
const search = ref("");
defineProps<{
    categories: Category[];
}>();
const logout = () => {
    router.post("/logout");
};
</script>

<template>
    <CustomerLayout>
        <!-- ==================== Home Content ==================== -->

        <ProductSearch v-model="search" />
        <div class="mt-6">
            <CategorySlider :categories="categories" />
        </div>
        <Button type="button" variant="danger" fullWidth @click="logout">
            Logout
        </Button>
    </CustomerLayout>
</template>
