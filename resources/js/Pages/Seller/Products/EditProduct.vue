<script setup lang="ts">
import { computed, onBeforeUnmount, ref } from "vue";
import { useForm } from "@inertiajs/vue3";

import Button from "@/Components/UI/Button.vue";
import SellerLayout from "@/Layouts/SellerLayout.vue";

// ==================== Types ====================

interface Category {
    id: number;
    name: string;
}

interface ProductImage {
    id: number;
    image: string;
    sort_order: number;
}

interface ProductOptionValue {
    id: number;
    value: string;
}

interface ProductOption {
    id?: number;
    name: string;
    values: ProductOptionValue[] | string[];
}

interface VariantValue {
    id: number;
    product_option_id: number;
    product_option_value_id: number;
    option_value: {
        id: number;
        value: string;
    };
}

interface ProductVariant {
    id: number;
    sku: string | null;
    price: number;
    stock: number;
    variant_values: VariantValue[];
}

interface Product {
    id: number;
    category_id: number;
    name: string;
    description: string | null;
    price: number;
    stock: number;
    unit: string | null;
    is_active: boolean;
    images: ProductImage[];
    options: ProductOption[];
    variants: ProductVariant[];
}

interface EditableOption {
    id?: number;
    name: string;
    values: string[];
}

interface EditableVariant {
    id?: number;
    values: string[];
    sku: string;
    price: string;
    stock: string;
}

// ==================== Props ====================

const props = defineProps<{
    product: Product;
    categories: Category[];
}>();

// ==================== Existing Images ====================

const existingImages = ref<ProductImage[]>([...props.product.images]);
const removedImageIds = ref<number[]>([]);

const imageInput = ref<HTMLInputElement | null>(null);

const newImages = ref<File[]>([]);
const newImagePreviews = ref<string[]>([]);

// ==================== Form ====================

const form = useForm({
    _method: "PUT",

    category_id: String(props.product.category_id),
    name: props.product.name,
    description: props.product.description ?? "",
    price: String(props.product.price),
    stock: String(props.product.stock ?? ""),
    unit: props.product.unit ?? "",
    is_active: props.product.is_active,

    images: [] as File[],
    removed_image_ids: [] as number[],

    options: [] as EditableOption[],
    variants: [] as EditableVariant[],
});

// ==================== Variant State ====================

const hasVariants = ref(props.product.variants.length > 0);

const options = ref<EditableOption[]>([]);

const variants = ref<EditableVariant[]>([]);

// ==================== Load Existing Options ====================

props.product.options.forEach((option) => {
    options.value.push({
        id: option.id,
        name: option.name,
        values: option.values.map((value) => {
            if (typeof value === "string") {
                return value;
            }

            return value.value;
        }),
    });
});

// ==================== Load Existing Variants ====================

//props.product.variants.forEach((variant) => {
//    variants.value.push({
//        id: variant.id,
//        values: variant.variant_values.map(
//            (variantValue) => variantValue.option_value.value,
//        ),
//        sku: variant.sku ?? "",
//        price: String(variant.price),
//        stock: String(variant.stock),
//    });
//});

props.product.variants.forEach((variant) => {
    variants.value.push({
        id: variant.id,
        values: variant.variant_values
            .map((variantValue) => variantValue.option_value?.value)
            .filter((value): value is string => Boolean(value)),
        sku: variant.sku ?? "",
        price: String(variant.price),
        stock: String(variant.stock),
    });
});

// ==================== Image Handling ====================

const handleImages = (event: Event) => {
    const target = event.target as HTMLInputElement;

    if (!target.files) {
        return;
    }

    const files = Array.from(target.files);

    newImages.value = [...newImages.value, ...files];

    newImagePreviews.value = [
        ...newImagePreviews.value,
        ...files.map((file) => URL.createObjectURL(file)),
    ];

    form.images = newImages.value;

    target.value = "";
};

// ==================== Remove New Image ====================

const removeNewImage = (index: number) => {
    URL.revokeObjectURL(newImagePreviews.value[index]);

    newImages.value.splice(index, 1);
    newImagePreviews.value.splice(index, 1);

    form.images = newImages.value;
};

// ==================== Remove Existing Image ====================

const removeExistingImage = (index: number) => {
    const image = existingImages.value[index];

    if (!image) {
        return;
    }

    removedImageIds.value.push(image.id);
    existingImages.value.splice(index, 1);
};

// ==================== Cleanup ====================

onBeforeUnmount(() => {
    newImagePreviews.value.forEach((preview) => {
        URL.revokeObjectURL(preview);
    });
});

// ==================== Variant Options ====================

const addOption = () => {
    options.value.push({
        name: "",
        values: [],
    });
};

const removeOption = (optionIndex: number) => {
    options.value.splice(optionIndex, 1);
};

const addOptionValue = (optionIndex: number) => {
    options.value[optionIndex].values.push("");
};

const removeOptionValue = (optionIndex: number, valueIndex: number) => {
    options.value[optionIndex].values.splice(valueIndex, 1);
};

// ==================== Valid Options ====================

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

// ==================== Total Variant Stock ====================

const totalVariantStock = computed(() => {
    return variants.value.reduce((total, variant) => {
        return total + (Number(variant.stock) || 0);
    }, 0);
});

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

    variants.value = combinations.map((values) => {
        // ==================== Preserve Existing Variant ====================

        const existingVariant = props.product.variants.find((variant) => {
            const existingValues = variant.variant_values.map(
                (variantValue) => variantValue.option_value.value,
            );

            return (
                existingValues.length === values.length &&
                existingValues.every((value, index) => value === values[index])
            );
        });

        if (existingVariant) {
            return {
                id: existingVariant.id,
                values,
                sku: existingVariant.sku ?? "",
                price: String(existingVariant.price),
                stock: String(existingVariant.stock),
            };
        }

        return {
            values,
            sku: "",
            price: form.price,
            stock: "0",
        };
    });
};

// ==================== Submit ====================

const submit = () => {
    form.options = hasVariants.value ? validOptions.value : [];

    form.variants = hasVariants.value ? variants.value : [];
    form.removed_image_ids = removedImageIds.value;

    form.post(`/seller/products/${props.product.id}`, {
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
                    Edit Product
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Update your product information.
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
                        class="mb-2 block text-sm font-semibold text-gray-900"
                    >
                        Product Images
                    </label>

                    <!-- ==================== Existing Images ==================== -->

                    <div
                        v-if="existingImages.length"
                        class="grid grid-cols-2 gap-3 sm:grid-cols-3"
                    >
                        <div
                            v-for="(image, index) in existingImages"
                            :key="image.id"
                            class="relative aspect-square overflow-hidden rounded-xl border border-gray-200"
                        >
                            <img
                                :src="`/storage/${image.image}`"
                                :alt="`${form.name} image ${index + 1}`"
                                class="h-full w-full object-cover"
                            />

                            <button
                                type="button"
                                class="absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-full bg-white/90 text-sm font-bold text-gray-700 shadow-sm transition hover:bg-white hover:text-red-600"
                                @click="removeExistingImage(index)"
                            >
                                ×
                            </button>
                        </div>
                    </div>

                    <!-- ==================== Add New Images ==================== -->

                    <input
                        id="product-images"
                        ref="imageInput"
                        type="file"
                        accept="image/*"
                        multiple
                        class="hidden"
                        @change="handleImages"
                    />

                    <label
                        for="product-images"
                        class="mt-3 flex min-h-28 w-full cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 px-4 py-5 text-center transition hover:border-gray-300 hover:bg-gray-100"
                    >
                        <span class="text-sm font-semibold text-gray-700">
                            Add More Images
                        </span>

                        <span class="mt-1 text-xs text-gray-400">
                            Select one or more images
                        </span>
                    </label>

                    <!-- ==================== New Image Previews ==================== -->

                    <div
                        v-if="newImagePreviews.length"
                        class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3"
                    >
                        <div
                            v-for="(preview, index) in newImagePreviews"
                            :key="preview"
                            class="relative aspect-square overflow-hidden rounded-xl border border-gray-200"
                        >
                            <img
                                :src="preview"
                                :alt="`New product image ${index + 1}`"
                                class="h-full w-full object-cover"
                            />

                            <button
                                type="button"
                                class="absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-full bg-white/90 text-sm font-bold text-gray-700 shadow-sm transition hover:bg-white hover:text-red-600"
                                @click="removeNewImage(index)"
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
                        <option
                            v-for="category in categories"
                            :key="category.id"
                            :value="String(category.id)"
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
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-gray-400 focus:bg-white"
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
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-gray-400 focus:bg-white"
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

                <!-- ==================== Options & Variants ==================== -->

                <div class="border-t border-gray-100 pt-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">
                                Options & Variants
                            </h3>

                            <p class="mt-1 text-xs text-gray-500">
                                Add options such as color or size when your
                                product has different versions.
                            </p>
                        </div>

                        <!-- ==================== Variant Toggle ==================== -->

                        <label
                            class="flex shrink-0 cursor-pointer items-center gap-2"
                        >
                            <input
                                v-model="hasVariants"
                                type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-400"
                            />

                            <span class="text-sm font-medium text-gray-700">
                                Has variants
                            </span>
                        </label>
                    </div>

                    <!-- ==================== Options ==================== -->

                    <div v-if="hasVariants" class="mt-5 space-y-4">
                        <div
                            v-for="(option, optionIndex) in options"
                            :key="option.id ?? `new-${optionIndex}`"
                            class="rounded-xl border border-gray-200 bg-gray-50 p-4"
                        >
                            <div class="flex items-center gap-3">
                                <input
                                    v-model="option.name"
                                    type="text"
                                    placeholder="Option name (e.g. Color)"
                                    class="min-w-0 flex-1 rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-400"
                                />

                                <button
                                    type="button"
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition hover:border-red-200 hover:text-red-600"
                                    @click="removeOption(optionIndex)"
                                >
                                    ×
                                </button>
                            </div>

                            <!-- ==================== Option Values ==================== -->

                            <div class="mt-3 space-y-2">
                                <div
                                    v-for="(value, valueIndex) in option.values"
                                    :key="valueIndex"
                                    class="flex items-center gap-2"
                                >
                                    <input
                                        v-model="option.values[valueIndex]"
                                        type="text"
                                        placeholder="Option value"
                                        class="min-w-0 flex-1 rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-400"
                                    />

                                    <button
                                        type="button"
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-gray-400 transition hover:bg-white hover:text-red-600"
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

                                <button
                                    type="button"
                                    class="text-sm font-medium text-gray-600 transition hover:text-gray-900"
                                    @click="addOptionValue(optionIndex)"
                                >
                                    + Add value
                                </button>
                            </div>
                        </div>

                        <!-- ==================== Add Option ==================== -->

                        <button
                            type="button"
                            class="w-full rounded-xl border border-dashed border-gray-300 px-4 py-3 text-sm font-semibold text-gray-600 transition hover:border-gray-400 hover:bg-gray-50 hover:text-gray-900"
                            @click="addOption"
                        >
                            + Add Option
                        </button>

                        <!-- ==================== Generate Variants ==================== -->

                        <button
                            v-if="validOptions.length"
                            type="button"
                            class="w-full rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-gray-800"
                            @click="generateCombinations"
                        >
                            Generate Variants
                        </button>
                    </div>

                    <!-- ==================== Generated Variants ==================== -->

                    <div
                        v-if="hasVariants && variants.length"
                        class="mt-5 space-y-3"
                    >
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900">
                                Variants
                            </h4>

                            <p class="mt-1 text-xs text-gray-500">
                                Set the price, stock, and SKU for each variant.
                            </p>
                        </div>

                        <div
                            v-for="(variant, variantIndex) in variants"
                            :key="variant.id ?? `new-variant-${variantIndex}`"
                            class="rounded-xl border border-gray-200 bg-white p-4"
                        >
                            <!-- ==================== Variant Name ==================== -->

                            <div
                                class="mb-4 rounded-lg bg-gray-50 px-3 py-2 text-sm font-medium text-gray-800"
                            >
                                {{ variant.values.join(" / ") }}
                            </div>

                            <!-- ==================== Variant Fields ==================== -->

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-medium text-gray-600"
                                    >
                                        SKU
                                    </label>

                                    <input
                                        v-model="variant.sku"
                                        type="text"
                                        placeholder="Optional"
                                        class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-400 focus:bg-white"
                                    />
                                </div>

                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-medium text-gray-600"
                                    >
                                        Price
                                    </label>

                                    <input
                                        v-model="variant.price"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-gray-400 focus:bg-white"
                                    />
                                </div>

                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-medium text-gray-600"
                                    >
                                        Stock
                                    </label>

                                    <input
                                        v-model="variant.stock"
                                        type="number"
                                        min="0"
                                        class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-gray-400 focus:bg-white"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- ==================== Total Variant Stock ==================== -->

                        <div
                            class="flex items-center justify-between rounded-xl bg-gray-50 px-4 py-3"
                        >
                            <span class="text-sm font-medium text-gray-600">
                                Total variant stock
                            </span>

                            <span class="text-sm font-bold text-gray-900">
                                {{ totalVariantStock }}
                            </span>
                        </div>
                    </div>
                </div>
                <!-- ==================== Form Actions ==================== -->

                <div
                    class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end"
                >
                    <button
                        type="button"
                        class="w-full rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 sm:w-auto"
                        @click="$inertia.visit('/seller/products')"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50 sm:w-auto"
                    >
                        {{ form.processing ? "Saving..." : "Save Changes" }}
                    </button>
                </div>
            </form>
        </div>
    </SellerLayout>
</template>
