<script setup lang="ts">
// ==================== Imports ====================

import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import Button from "@/Components/UI/Button.vue";
import { useForm } from "@inertiajs/vue3";
import { ClipboardList, MapPin } from "lucide-vue-next";
import { t } from "@/Utils/i18n";

// ==================== Form ====================

const form = useForm({
    description: "",
    address: "",
});

// ==================== Submit ====================

const submitOrder = () => {
    form.post("/custom-orders", {
        preserveScroll: true,
    });
};
const customPlaceholder = t("customExample");
</script>

<template>
    <CustomerLayout>
        <!-- ==================== Custom Order Page ==================== -->

        <main class="min-h-screen bg-background">
            <div class="mx-auto w-full max-w-3xl px-5 py-6 sm:px-8 sm:py-10">
                <!-- ==================== Header ==================== -->

                <div class="mb-8">
                    <div
                        class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"
                    >
                        <ClipboardList :size="24" :stroke-width="2" />
                    </div>

                    <h1
                        class="text-2xl font-extrabold tracking-tight text-heading sm:text-3xl"
                    >
                        {{ t("customOrder") }}
                    </h1>

                    <p class="mt-2 max-w-xl text-sm leading-6 text-muted">
                        {{ t("customDescription") }}
                    </p>
                </div>

                <!-- ==================== Form ==================== -->

                <form
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
                    @submit.prevent="submitOrder"
                >
                    <!-- ==================== Description ==================== -->

                    <div>
                        <label
                            for="description"
                            class="text-sm font-bold text-heading"
                        >
                            {{ t("whatDoYouNeed") }}
                        </label>

                        <p class="mt-1 text-xs text-muted">
                            {{ t("customDescriptionHint") }}
                        </p>

                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="6"
                            maxlength="5000"
                            :placeholder="customPlaceholder"
                            class="mt-3 w-full resize-y rounded-xl border border-border bg-background px-4 py-3 text-sm leading-6 text-body outline-none transition-colors placeholder:text-muted focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"
                        ></textarea>

                        <p
                            v-if="form.errors.description"
                            class="mt-2 text-xs font-medium text-red-600"
                        >
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <!-- ==================== Address ==================== -->

                    <div class="mt-6">
                        <label
                            for="address"
                            class="flex items-center gap-2 text-sm font-bold text-heading"
                        >
                            <MapPin :size="17" :stroke-width="2" />

                            {{ t("deliveryAddress") }}
                        </label>

                        <p class="mt-1 text-xs text-muted">
                            {{ t("enterAddress") }}
                        </p>

                        <textarea
                            id="address"
                            v-model="form.address"
                            rows="3"
                            maxlength="1000"
                            class="mt-3 w-full resize-y rounded-xl border border-border bg-background px-4 py-3 text-sm leading-6 text-body outline-none transition-colors placeholder:text-muted focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"
                        ></textarea>

                        <p
                            v-if="form.errors.address"
                            class="mt-2 text-xs font-medium text-red-600"
                        >
                            {{ form.errors.address }}
                        </p>
                    </div>

                    <!-- ==================== Submit ==================== -->

                    <div class="mt-8 border-t border-border pt-6">
                        <Button
                            type="submit"
                            variant="primary"
                            size="lg"
                            fullWidth
                            :disabled="
                                form.processing ||
                                !form.description.trim() ||
                                !form.address.trim()
                            "
                        >
                            {{
                                form.processing ? t("submitting") : t("submit")
                            }}
                        </Button>
                    </div>
                </form>
            </div>
        </main>
    </CustomerLayout>
</template>
