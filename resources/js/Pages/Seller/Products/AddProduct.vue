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
        <div class="mx-auto w-full max-w-3xl">
            <!-- ==================== Page Header ==================== -->

            <div class="mb-6">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                    Add Product
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Add a new product to your store.
                </p>
            </div>

            <!-- ==================== Product Form ==================== -->

            <form
                @submit.prevent="submit"
                class="space-y-5 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-7"
            >
                <!-- ==================== Product Images ==================== -->

                <div>
                    <label
                        for="images"
                        class="mb-2 block text-sm font-semibold text-gray-900"
                    >
                        Product Images
                    </label>

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
                        class="flex min-h-32 w-full flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 px-4 py-6 text-center transition hover:border-gray-300 hover:bg-gray-100"
                        @click="imageInput?.click()"
                    >
                        <span class="text-sm font-semibold text-gray-700">
                            Add Product Images
                        </span>

                        <span class="mt-1 text-xs text-gray-400">
                            Select one or more images
                        </span>
                    </button>

                    <!-- ==================== Image Previews ==================== -->

                    <div
                        v-if="imagePreviews.length"
                        class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3"
                    >
                        <div
                            v-for="(preview, index) in imagePreviews"
                            :key="preview"
                            class="relative aspect-square overflow-hidden rounded-xl border border-gray-200"
                        >
                            <img
                                :src="preview"
                                :alt="`Product image ${index + 1}`"
                                class="h-full w-full object-cover"
                            />

                            <button
                                type="button"
                                class="absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-full bg-white/90 text-sm font-bold text-gray-700 shadow-sm transition hover:bg-white"
                                @click="removeImage(index)"
                            >
                                ×
                            </button>
                        </div>
                    </div>

                    <p
                        v-if="form.errors.images"
                        class="mt-1.5 text-sm text-red-600"
                    >
                        {{ form.errors.images }}
                    </p>
                </div>
                <!-- ==================== Product Name ==================== -->

                <div>
                    <label
                        for="name"
                        class="mb-2 block text-sm font-semibold text-gray-900"
                    >
                        Product Name
                    </label>

                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        placeholder="e.g. iPhone 15"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-400 focus:bg-white"
                    />

                    <p
                        v-if="form.errors.name"
                        class="mt-1.5 text-sm text-red-600"
                    >
                        {{ form.errors.name }}
                    </p>
                </div>

                <!-- ==================== Category ==================== -->

                <div>
                    <label
                        for="category"
                        class="mb-2 block text-sm font-semibold text-gray-900"
                    >
                        Category
                    </label>

                    <select
                        id="category"
                        v-model="form.category_id"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-gray-400 focus:bg-white"
                    >
                        <option value="" disabled>Select a category</option>

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
                        class="mt-1.5 text-sm text-red-600"
                    >
                        {{ form.errors.category_id }}
                    </p>
                </div>

                <!-- ==================== Description ==================== -->

                <div>
                    <label
                        for="description"
                        class="mb-2 block text-sm font-semibold text-gray-900"
                    >
                        Description
                    </label>

                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="4"
                        placeholder="Describe your product..."
                        class="w-full resize-none rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-400 focus:bg-white"
                    ></textarea>

                    <p
                        v-if="form.errors.description"
                        class="mt-1.5 text-sm text-red-600"
                    >
                        {{ form.errors.description }}
                    </p>
                </div>

                <!-- ==================== Price / Stock ==================== -->

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label
                            for="price"
                            class="mb-2 block text-sm font-semibold text-gray-900"
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
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-400 focus:bg-white"
                        />

                        <p
                            v-if="form.errors.price"
                            class="mt-1.5 text-sm text-red-600"
                        >
                            {{ form.errors.price }}
                        </p>
                    </div>

                    <div v-if="!hasVariants">
                        <label
                            for="stock"
                            class="mb-2 block text-sm font-semibold text-gray-900"
                        >
                            Stock
                        </label>

                        <input
                            id="stock"
                            v-model="form.stock"
                            type="number"
                            min="0"
                            placeholder="0"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-400 focus:bg-white"
                        />

                        <p
                            v-if="form.errors.stock"
                            class="mt-1.5 text-sm text-red-600"
                        >
                            {{ form.errors.stock }}
                        </p>
                    </div>
                </div>

                <!-- ==================== Unit ==================== -->

                <div>
                    <label
                        for="unit"
                        class="mb-2 block text-sm font-semibold text-gray-900"
                    >
                        Unit
                    </label>

                    <input
                        id="unit"
                        v-model="form.unit"
                        type="text"
                        placeholder="e.g. piece, kg, liter"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-400 focus:bg-white"
                    />

                    <p
                        v-if="form.errors.unit"
                        class="mt-1.5 text-sm text-red-600"
                    >
                        {{ form.errors.unit }}
                    </p>
                </div>

                <!-- ==================== Product Variants ==================== -->

                <div
                    class="rounded-2xl border border-gray-100 bg-gray-50 p-4 sm:p-5"
                >
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">
                            Product Variants
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Does this product have different options such as
                            size or color?
                        </p>
                    </div>

                    <!-- ==================== Variant Choice ==================== -->

                    <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                        <label
                            class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 bg-white p-3"
                        >
                            <input
                                v-model="hasVariants"
                                type="radio"
                                :value="false"
                                class="h-4 w-4"
                            />

                            <span class="text-sm font-medium text-gray-900">
                                No variants
                            </span>
                        </label>

                        <label
                            class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 bg-white p-3"
                        >
                            <input
                                v-model="hasVariants"
                                type="radio"
                                :value="true"
                                class="h-4 w-4"
                            />

                            <span class="text-sm font-medium text-gray-900">
                                Has variants
                            </span>
                        </label>
                    </div>

                    <!-- ==================== Variant Builder Placeholder ==================== -->

                    <div v-if="hasVariants" class="mt-5 space-y-4">
                        <!-- ==================== Options ==================== -->

                        <div
                            v-for="(option, optionIndex) in options"
                            :key="optionIndex"
                            class="rounded-xl border border-gray-200 bg-white p-4"
                        >
                            <!-- ==================== Option Header ==================== -->

                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <label
                                        class="mb-2 block text-sm font-semibold text-gray-900"
                                    >
                                        Option Name
                                    </label>

                                    <input
                                        v-model="option.name"
                                        type="text"
                                        placeholder="e.g. Color, Size, Storage"
                                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-gray-400 focus:bg-white"
                                    />
                                </div>

                                <button
                                    type="button"
                                    class="mt-7 text-sm font-medium text-red-600 hover:text-red-700"
                                    @click="removeOption(optionIndex)"
                                >
                                    Remove
                                </button>
                            </div>

                            <!-- ==================== Option Values ==================== -->

                            <div class="mt-4">
                                <p
                                    class="mb-2 text-sm font-semibold text-gray-900"
                                >
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
                                            class="min-w-0 flex-1 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-gray-400 focus:bg-white"
                                        />

                                        <button
                                            type="button"
                                            class="rounded-xl border border-gray-200 px-4 text-sm text-gray-500 transition hover:bg-gray-50 hover:text-red-600"
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

                                <!-- ==================== Add Value ==================== -->

                                <button
                                    type="button"
                                    class="mt-3 text-sm font-semibold text-gray-700 hover:text-gray-900"
                                    @click="addOptionValue(optionIndex)"
                                >
                                    + Add value
                                </button>
                            </div>
                        </div>

                        <!-- ==================== Add Option ==================== -->

                        <button
                            type="button"
                            class="w-full rounded-xl border border-dashed border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                            @click="addOption"
                        >
                            + Add another option
                        </button>
                    </div>
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
                    class="mt-5 space-y-4"
                >
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">
                            Variants
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Set the SKU, price, and stock for each combination.
                        </p>
                        <!-- ==================== Total Variant Stock ==================== -->

                        <div
                            class="mt-4 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-600">
                                    Total Stock
                                </span>

                                <span class="text-lg font-bold text-gray-900">
                                    {{ totalVariantStock }}
                                </span>
                            </div>

                            <p class="mt-1 text-xs text-gray-500">
                                Calculated automatically from all variant
                                stocks.
                            </p>
                        </div>
                    </div>

                    <div
                        v-for="(variant, index) in variants"
                        :key="index"
                        class="rounded-xl border border-gray-200 bg-white p-4"
                    >
                        <!-- ==================== Variant Name ==================== -->

                        <div class="mb-4">
                            <p class="text-sm font-semibold text-gray-900">
                                {{ variant.values.join(" / ") }}
                            </p>
                        </div>

                        <!-- ==================== Variant Fields ==================== -->

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div>
                                <label
                                    class="mb-2 block text-xs font-semibold text-gray-700"
                                >
                                    SKU
                                </label>

                                <input
                                    v-model="variant.sku"
                                    type="text"
                                    placeholder="e.g. TS-BLK-M"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm outline-none transition focus:border-gray-400 focus:bg-white"
                                />
                                <p
                                    v-if="
                                        form.errors[`variants.${index}.price`]
                                    "
                                    class="mt-1.5 text-sm text-red-600"
                                >
                                    {{ "Combination field is required." }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-xs font-semibold text-gray-700"
                                >
                                    Price
                                </label>

                                <input
                                    v-model="variant.price"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    placeholder="0.00"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm outline-none transition focus:border-gray-400 focus:bg-white"
                                />
                                <p
                                    v-if="
                                        form.errors[`variants.${index}.price`]
                                    "
                                    class="mt-1.5 text-sm text-red-600"
                                >
                                    {{ "Price field is required." }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-xs font-semibold text-gray-700"
                                >
                                    Stock
                                </label>

                                <input
                                    v-model="variant.stock"
                                    type="number"
                                    min="0"
                                    placeholder="0"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm outline-none transition focus:border-gray-400 focus:bg-white"
                                />
                                <p
                                    v-if="
                                        form.errors[`variants.${index}.stock`]
                                    "
                                    class="mt-1.5 text-sm text-red-600"
                                >
                                    {{ "stock field is required." }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== Submit ==================== -->

                <div
                    class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end"
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
