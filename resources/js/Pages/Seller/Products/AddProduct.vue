<script setup lang="ts">
import { computed, onBeforeUnmount, ref, watch } from "vue";
import { useForm } from "@inertiajs/vue3";

import Button from "@/Components/UI/Button.vue";
import SellerLayout from "@/Layouts/SellerLayout.vue";

// ==================== Types ====================

interface Category {
    id: number;
    name: string;
}

// ==================== Props ====================

defineProps<{
    categories: Category[];
}>();

// ==================== Form ====================

const form = useForm({
    category_id: "",
    name: "",
    description: "",
    price: "",
    stock: "",
    unit: "",
    images: [] as File[],

    options: [] as ProductOption[],

    variants: [] as VariantCombination[],
});
// ==================== Image Preview ====================

const imageInput = ref<HTMLInputElement | null>(null);
const imagePreviews = ref<string[]>([]);

const handleImages = (event: Event) => {
    const target = event.target as HTMLInputElement;

    if (!target.files) {
        return;
    }

    const files = Array.from(target.files);

    form.images = [...form.images, ...files];

    imagePreviews.value = [
        ...imagePreviews.value,
        ...files.map((file) => URL.createObjectURL(file)),
    ];

    // Allow selecting the same file again later.
    target.value = "";
};

// ==================== Remove Image ====================

const removeImage = (index: number) => {
    URL.revokeObjectURL(imagePreviews.value[index]);

    form.images.splice(index, 1);
    imagePreviews.value.splice(index, 1);
};

// ==================== Cleanup ====================

onBeforeUnmount(() => {
    imagePreviews.value.forEach((preview) => {
        URL.revokeObjectURL(preview);
    });
});

// ==================== Variants ====================
// ==================== Variant Types ====================

interface ProductOption {
    name: string;
    values: string[];
}

// ==================== Variant State ====================

const hasVariants = ref(false);
watch(hasVariants, (enabled) => {
    if (!enabled) {
        options.value = [];
        variants.value = [];
    }
});

const options = ref<ProductOption[]>([]);

// ==================== Add Option ====================

const addOption = () => {
    options.value.push({
        name: "",
        values: [],
    });
};

// ==================== Remove Option ====================

const removeOption = (optionIndex: number) => {
    options.value.splice(optionIndex, 1);
};

// ==================== Add Option Value ====================

const addOptionValue = (optionIndex: number) => {
    options.value[optionIndex].values.push("");
};

// ==================== Remove Option Value ====================

const removeOptionValue = (optionIndex: number, valueIndex: number) => {
    options.value[optionIndex].values.splice(valueIndex, 1);
};

// ==================== Variant Types ====================

interface VariantCombination {
    values: string[];
    sku: string;
    price: string;
    stock: string;
}
// ==================== Total Variant Stock ====================

const totalVariantStock = computed(() => {
    return variants.value.reduce((total, variant) => {
        return total + (Number(variant.stock) || 0);
    }, 0);
});
// ==================== Variant Combinations ====================

const variants = ref<VariantCombination[]>([]);

// ==================== Generate Combinations ====================

const generateCombinations = () => {
    if (!validOptions.value.length) {
        variants.value = [];
        return;
    }

    let combinations: string[][] = [[]];

    for (const option of validOptions.value) {
        const newCombinations: string[][] = [];

        for (const combination of combinations) {
            for (const value of option.values) {
                newCombinations.push([...combination, value]);
            }
        }

        combinations = newCombinations;
    }

    variants.value = combinations.map((values) => ({
        values,
        sku: "",
        price: "",
        stock: "",
    }));
};
// ==================== Valid Product Options ====================

const validOptions = computed(() => {
    return options.value
        .map((option) => ({
            ...option,
            name: option.name.trim(),
            values: option.values
                .map((value) => value.trim())
                .filter((value) => value !== ""),
        }))
        .filter((option) => option.name !== "" && option.values.length > 0);
});
// ==================== Submit ====================

const submit = () => {
    form.options = hasVariants.value ? validOptions.value : [];

    form.variants = hasVariants.value ? variants.value : [];

    form.post("/seller/products", {
        forceFormData: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <SellerLayout>
        <div class="mx-auto w-full max-w-4xl">
            <!-- ==================== Page Header ==================== -->

            <section class="mb-8">
                <p
                    class="text-sm font-bold uppercase tracking-wider text-brand-600"
                >
                    Store Management
                </p>

                <h1
                    class="mt-1 text-2xl font-extrabold tracking-tight text-heading sm:text-3xl"
                >
                    Add Product
                </h1>

                <p class="mt-2 text-sm leading-6 text-muted">
                    Add a new product to your store and configure its
                    availability, pricing, and variants.
                </p>
            </section>

            <!-- ==================== Product Form ==================== -->

            <form class="space-y-6" @submit.prevent="submit">
                <!-- ==================== Product Images ==================== -->

                <section
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
                >
                    <div class="mb-5">
                        <h2 class="text-base font-extrabold text-heading">
                            Product Images
                        </h2>

                        <p class="mt-1 text-sm text-muted">
                            Add clear images that show your product.
                        </p>
                    </div>

                    <input
                        id="images"
                        ref="imageInput"
                        type="file"
                        accept="image/*"
                        multiple
                        class="hidden"
                        @change="handleImages"
                    />

                    <button
                        type="button"
                        class="flex min-h-36 w-full flex-col items-center justify-center rounded-2xl border-2 border-dashed border-border bg-background px-5 py-8 text-center transition-all duration-200 hover:border-brand-300 hover:bg-brand-50/50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2"
                        @click="imageInput?.click()"
                    >
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="22"
                                height="22"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <rect
                                    width="18"
                                    height="18"
                                    x="3"
                                    y="3"
                                    rx="2"
                                />
                                <circle cx="9" cy="9" r="2" />
                                <path
                                    d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"
                                />
                            </svg>
                        </div>

                        <span class="mt-3 text-sm font-bold text-heading">
                            Add Product Images
                        </span>

                        <span class="mt-1 text-xs text-muted">
                            Select one or more images
                        </span>
                    </button>

                    <!-- ==================== Image Previews ==================== -->

                    <div
                        v-if="imagePreviews.length"
                        class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4"
                    >
                        <div
                            v-for="(preview, index) in imagePreviews"
                            :key="preview"
                            class="group relative aspect-square overflow-hidden rounded-xl border border-border bg-background"
                        >
                            <img
                                :src="preview"
                                :alt="`Product image ${index + 1}`"
                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                            />

                            <button
                                type="button"
                                :aria-label="`Remove image ${index + 1}`"
                                class="absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-full border border-white/60 bg-white/95 text-lg font-medium text-slate-600 shadow-sm backdrop-blur transition hover:bg-white hover:text-red-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500"
                                @click="removeImage(index)"
                            >
                                ×
                            </button>
                        </div>
                    </div>

                    <p
                        v-if="form.errors.images"
                        class="mt-2 text-sm font-medium text-red-600"
                    >
                        {{ form.errors.images }}
                    </p>
                </section>

                <!-- ==================== Basic Information ==================== -->

                <section
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
                >
                    <div class="mb-5">
                        <h2 class="text-base font-extrabold text-heading">
                            Basic Information
                        </h2>

                        <p class="mt-1 text-sm text-muted">
                            Provide the main details customers will see.
                        </p>
                    </div>

                    <div class="space-y-5">
                        <!-- ==================== Product Name ==================== -->

                        <div>
                            <label
                                for="name"
                                class="mb-2 block text-sm font-bold text-heading"
                            >
                                Product Name
                            </label>

                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="e.g. iPhone 15"
                                class="w-full rounded-xl border border-border bg-background px-4 py-3 text-sm text-heading outline-none transition placeholder:text-muted focus:border-brand-500 focus:bg-surface focus:ring-2 focus:ring-brand-100"
                            />

                            <p
                                v-if="form.errors.name"
                                class="mt-1.5 text-sm font-medium text-red-600"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- ==================== Category ==================== -->

                        <div>
                            <label
                                for="category"
                                class="mb-2 block text-sm font-bold text-heading"
                            >
                                Category
                            </label>

                            <select
                                id="category"
                                v-model="form.category_id"
                                class="w-full rounded-xl border border-border bg-background px-4 py-3 text-sm text-heading outline-none transition focus:border-brand-500 focus:bg-surface focus:ring-2 focus:ring-brand-100"
                            >
                                <option value="" disabled>
                                    Select a category
                                </option>

                                <option
                                    v-for="category in categories"
                                    :key="category.id"
                                    :value="category.id"
                                >
                                    {{ category.name }}
                                </option>
                            </select>

                            <p
                                v-if="form.errors.category_id"
                                class="mt-1.5 text-sm font-medium text-red-600"
                            >
                                {{ form.errors.category_id }}
                            </p>
                        </div>

                        <!-- ==================== Description ==================== -->

                        <div>
                            <label
                                for="description"
                                class="mb-2 block text-sm font-bold text-heading"
                            >
                                Description
                            </label>

                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="5"
                                placeholder="Describe your product..."
                                class="w-full resize-none rounded-xl border border-border bg-background px-4 py-3 text-sm leading-6 text-heading outline-none transition placeholder:text-muted focus:border-brand-500 focus:bg-surface focus:ring-2 focus:ring-brand-100"
                            ></textarea>

                            <p
                                v-if="form.errors.description"
                                class="mt-1.5 text-sm font-medium text-red-600"
                            >
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <!-- ==================== Price / Stock ==================== -->

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label
                                    for="price"
                                    class="mb-2 block text-sm font-bold text-heading"
                                >
                                    Price
                                </label>

                                <input
                                    id="price"
                                    v-model="form.price"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    placeholder="0.00"
                                    class="w-full rounded-xl border border-border bg-background px-4 py-3 text-sm text-heading outline-none transition placeholder:text-muted focus:border-brand-500 focus:bg-surface focus:ring-2 focus:ring-brand-100"
                                />

                                <p
                                    v-if="form.errors.price"
                                    class="mt-1.5 text-sm font-medium text-red-600"
                                >
                                    {{ form.errors.price }}
                                </p>
                            </div>

                            <div v-if="!hasVariants">
                                <label
                                    for="stock"
                                    class="mb-2 block text-sm font-bold text-heading"
                                >
                                    Stock
                                </label>

                                <input
                                    id="stock"
                                    v-model="form.stock"
                                    type="number"
                                    min="0"
                                    placeholder="0"
                                    class="w-full rounded-xl border border-border bg-background px-4 py-3 text-sm text-heading outline-none transition placeholder:text-muted focus:border-brand-500 focus:bg-surface focus:ring-2 focus:ring-brand-100"
                                />

                                <p
                                    v-if="form.errors.stock"
                                    class="mt-1.5 text-sm font-medium text-red-600"
                                >
                                    {{ form.errors.stock }}
                                </p>
                            </div>
                        </div>

                        <!-- ==================== Unit ==================== -->

                        <div>
                            <label
                                for="unit"
                                class="mb-2 block text-sm font-bold text-heading"
                            >
                                Unit
                            </label>

                            <input
                                id="unit"
                                v-model="form.unit"
                                type="text"
                                placeholder="e.g. piece, kg, liter"
                                class="w-full rounded-xl border border-border bg-background px-4 py-3 text-sm text-heading outline-none transition placeholder:text-muted focus:border-brand-500 focus:bg-surface focus:ring-2 focus:ring-brand-100"
                            />

                            <p
                                v-if="form.errors.unit"
                                class="mt-1.5 text-sm font-medium text-red-600"
                            >
                                {{ form.errors.unit }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- ==================== Product Variants ==================== -->

                <section
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
                >
                    <div>
                        <h2 class="text-base font-extrabold text-heading">
                            Product Variants
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-muted">
                            Add options such as size, color, storage, or other
                            product combinations.
                        </p>
                    </div>

                    <!-- ==================== Variant Choice ==================== -->

                    <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <label
                            class="flex cursor-pointer items-center gap-3 rounded-xl border border-border bg-background p-4 transition hover:border-slate-300 hover:bg-surface"
                            :class="
                                !hasVariants
                                    ? 'border-brand-300 bg-brand-50/50 ring-1 ring-brand-200'
                                    : ''
                            "
                        >
                            <input
                                v-model="hasVariants"
                                type="radio"
                                :value="false"
                                class="h-4 w-4 accent-blue-600"
                            />

                            <div>
                                <p class="text-sm font-bold text-heading">
                                    No variants
                                </p>

                                <p class="mt-0.5 text-xs text-muted">
                                    One price and stock level
                                </p>
                            </div>
                        </label>

                        <label
                            class="flex cursor-pointer items-center gap-3 rounded-xl border border-border bg-background p-4 transition hover:border-slate-300 hover:bg-surface"
                            :class="
                                hasVariants
                                    ? 'border-brand-300 bg-brand-50/50 ring-1 ring-brand-200'
                                    : ''
                            "
                        >
                            <input
                                v-model="hasVariants"
                                type="radio"
                                :value="true"
                                class="h-4 w-4 accent-blue-600"
                            />

                            <div>
                                <p class="text-sm font-bold text-heading">
                                    Has variants
                                </p>

                                <p class="mt-0.5 text-xs text-muted">
                                    Different options and stock
                                </p>
                            </div>
                        </label>
                    </div>

                    <!-- ==================== Variant Options ==================== -->

                    <div v-if="hasVariants" class="mt-6 space-y-4">
                        <div
                            v-for="(option, optionIndex) in options"
                            :key="optionIndex"
                            class="rounded-2xl border border-border bg-background p-4 sm:p-5"
                        >
                            <!-- ==================== Option Header ==================== -->

                            <div
                                class="flex flex-col gap-3 sm:flex-row sm:items-end"
                            >
                                <div class="flex-1">
                                    <label
                                        class="mb-2 block text-sm font-bold text-heading"
                                    >
                                        Option Name
                                    </label>

                                    <input
                                        v-model="option.name"
                                        type="text"
                                        placeholder="e.g. Color, Size, Storage"
                                        class="w-full rounded-xl border border-border bg-surface px-4 py-3 text-sm text-heading outline-none transition placeholder:text-muted focus:border-brand-500 focus:ring-2 focus:ring-brand-100"
                                    />
                                </div>

                                <button
                                    type="button"
                                    class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700 transition hover:bg-red-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500"
                                    @click="removeOption(optionIndex)"
                                >
                                    Remove
                                </button>
                            </div>

                            <!-- ==================== Option Values ==================== -->

                            <div class="mt-5">
                                <p class="mb-2 text-sm font-bold text-heading">
                                    Values
                                </p>

                                <div class="space-y-2">
                                    <div
                                        v-for="(
                                            value, valueIndex
                                        ) in option.values"
                                        :key="valueIndex"
                                        class="flex gap-2"
                                    >
                                        <input
                                            v-model="option.values[valueIndex]"
                                            type="text"
                                            placeholder="e.g. Black"
                                            class="min-w-0 flex-1 rounded-xl border border-border bg-surface px-4 py-3 text-sm text-heading outline-none transition placeholder:text-muted focus:border-brand-500 focus:ring-2 focus:ring-brand-100"
                                        />

                                        <button
                                            type="button"
                                            aria-label="Remove value"
                                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-border bg-surface text-lg text-muted transition hover:border-red-200 hover:bg-red-50 hover:text-red-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500"
                                            @click="
                                                removeOptionValue(
                                                    optionIndex,
                                                    valueIndex,
                                                )
                                            "
                                        >
                                            ×
                                        </button>
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    class="mt-3 inline-flex items-center rounded-lg px-1 py-1 text-sm font-bold text-brand-600 transition hover:text-brand-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500"
                                    @click="addOptionValue(optionIndex)"
                                >
                                    + Add value
                                </button>
                            </div>
                        </div>

                        <!-- ==================== Add Option ==================== -->

                        <button
                            type="button"
                            class="w-full rounded-xl border border-dashed border-border bg-background px-4 py-3.5 text-sm font-bold text-body transition hover:border-brand-300 hover:bg-brand-50/50 hover:text-brand-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500"
                            @click="addOption"
                        >
                            + Add another option
                        </button>
                    </div>

                    <!-- ==================== Generate Variants ==================== -->

                    <div v-if="hasVariants && options.length" class="mt-5">
                        <Button
                            type="button"
                            variant="outline"
                            fullWidth
                            @click="generateCombinations"
                        >
                            Generate Variants
                        </Button>
                    </div>

                    <!-- ==================== Generated Variants ==================== -->

                    <div
                        v-if="variants.length && hasVariants"
                        class="mt-6 space-y-4"
                    >
                        <div>
                            <h3 class="text-sm font-extrabold text-heading">
                                Generated Variants
                            </h3>

                            <p class="mt-1 text-sm text-muted">
                                Set the SKU, price, and stock for each
                                combination.
                            </p>

                            <!-- ==================== Total Variant Stock ==================== -->

                            <div
                                class="mt-4 flex items-center justify-between rounded-xl border border-brand-100 bg-brand-50 px-4 py-3"
                            >
                                <div>
                                    <p class="text-sm font-bold text-brand-700">
                                        Total Stock
                                    </p>

                                    <p class="mt-0.5 text-xs text-brand-600">
                                        Calculated from all variants
                                    </p>
                                </div>

                                <span
                                    class="text-xl font-extrabold text-brand-700"
                                >
                                    {{ totalVariantStock }}
                                </span>
                            </div>
                        </div>

                        <!-- ==================== Variant Rows ==================== -->

                        <div
                            v-for="(variant, index) in variants"
                            :key="index"
                            class="rounded-2xl border border-border bg-background p-4 sm:p-5"
                        >
                            <div
                                class="mb-4 rounded-xl border border-border bg-surface px-4 py-3"
                            >
                                <p class="text-sm font-extrabold text-heading">
                                    {{ variant.values.join(" / ") }}
                                </p>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <!-- ==================== SKU ==================== -->

                                <div>
                                    <label
                                        class="mb-2 block text-xs font-bold uppercase tracking-wide text-muted"
                                    >
                                        SKU
                                    </label>

                                    <input
                                        v-model="variant.sku"
                                        type="text"
                                        placeholder="e.g. TS-BLK-M"
                                        class="w-full rounded-xl border border-border bg-surface px-3 py-2.5 text-sm text-heading outline-none transition placeholder:text-muted focus:border-brand-500 focus:ring-2 focus:ring-brand-100"
                                    />

                                    <p
                                        v-if="
                                            form.errors[`variants.${index}.sku`]
                                        "
                                        class="mt-1.5 text-sm font-medium text-red-600"
                                    >
                                        {{
                                            form.errors[`variants.${index}.sku`]
                                        }}
                                    </p>
                                </div>

                                <!-- ==================== Variant Price ==================== -->

                                <div>
                                    <label
                                        class="mb-2 block text-xs font-bold uppercase tracking-wide text-muted"
                                    >
                                        Price
                                    </label>

                                    <input
                                        v-model="variant.price"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        placeholder="0.00"
                                        class="w-full rounded-xl border border-border bg-surface px-3 py-2.5 text-sm text-heading outline-none transition placeholder:text-muted focus:border-brand-500 focus:ring-2 focus:ring-brand-100"
                                    />

                                    <p
                                        v-if="
                                            form.errors[
                                                `variants.${index}.price`
                                            ]
                                        "
                                        class="mt-1.5 text-sm font-medium text-red-600"
                                    >
                                        {{
                                            form.errors[
                                                `variants.${index}.price`
                                            ]
                                        }}
                                    </p>
                                </div>

                                <!-- ==================== Variant Stock ==================== -->

                                <div>
                                    <label
                                        class="mb-2 block text-xs font-bold uppercase tracking-wide text-muted"
                                    >
                                        Stock
                                    </label>

                                    <input
                                        v-model="variant.stock"
                                        type="number"
                                        min="0"
                                        placeholder="0"
                                        class="w-full rounded-xl border border-border bg-surface px-3 py-2.5 text-sm text-heading outline-none transition placeholder:text-muted focus:border-brand-500 focus:ring-2 focus:ring-brand-100"
                                    />

                                    <p
                                        v-if="
                                            form.errors[
                                                `variants.${index}.stock`
                                            ]
                                        "
                                        class="mt-1.5 text-sm font-medium text-red-600"
                                    >
                                        {{
                                            form.errors[
                                                `variants.${index}.stock`
                                            ]
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==================== Form Actions ==================== -->

                <div
                    class="flex flex-col-reverse gap-3 pb-2 sm:flex-row sm:justify-end"
                >
                    <Button
                        type="button"
                        variant="outline"
                        size="md"
                        @click="$inertia.visit('/seller/products')"
                    >
                        Cancel
                    </Button>

                    <Button type="submit" size="md" :loading="form.processing">
                        Add Product
                    </Button>
                </div>
            </form>
        </div>
    </SellerLayout>
</template>
