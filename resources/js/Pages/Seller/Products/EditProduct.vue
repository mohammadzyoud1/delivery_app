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
                    Edit Product
                </h1>

                <p class="mt-2 text-sm leading-6 text-muted">
                    Update your product information, images, pricing, and
                    variants.
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
                            Manage your existing images or add new ones.
                        </p>
                    </div>

                    <!-- ==================== Existing Images ==================== -->

                    <div
                        v-if="existingImages.length"
                        class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4"
                    >
                        <div
                            v-for="(image, index) in existingImages"
                            :key="image.id"
                            class="group relative aspect-square overflow-hidden rounded-xl border border-border bg-background"
                        >
                            <img
                                :src="`/storage/${image.image}`"
                                :alt="`${form.name} image ${index + 1}`"
                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                            />

                            <button
                                type="button"
                                :aria-label="`Remove image ${index + 1}`"
                                class="absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-full border border-white/60 bg-white/95 text-lg font-medium text-slate-600 shadow-sm backdrop-blur transition hover:bg-white hover:text-red-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500"
                                @click="removeExistingImage(index)"
                            >
                                ×
                            </button>
                        </div>
                    </div>

                    <!-- ==================== No Existing Images ==================== -->

                    <div
                        v-else
                        class="rounded-xl border border-dashed border-border bg-background px-5 py-8 text-center"
                    >
                        <p class="text-sm font-medium text-muted">
                            No existing images.
                        </p>
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
                        class="mt-4 flex min-h-32 w-full cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-border bg-background px-5 py-7 text-center transition-all duration-200 hover:border-brand-300 hover:bg-brand-50/50"
                    >
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="21"
                                height="21"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M12 5v14" />
                                <path d="M5 12h14" />
                            </svg>
                        </div>

                        <span class="mt-3 text-sm font-bold text-heading">
                            Add More Images
                        </span>

                        <span class="mt-1 text-xs text-muted">
                            Select one or more images
                        </span>
                    </label>

                    <!-- ==================== New Image Previews ==================== -->

                    <div v-if="newImagePreviews.length" class="mt-5">
                        <p class="mb-3 text-sm font-bold text-heading">
                            New Images
                        </p>

                        <div
                            class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4"
                        >
                            <div
                                v-for="(preview, index) in newImagePreviews"
                                :key="preview"
                                class="group relative aspect-square overflow-hidden rounded-xl border border-brand-200 bg-brand-50"
                            >
                                <img
                                    :src="preview"
                                    :alt="`New product image ${index + 1}`"
                                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                />

                                <span
                                    class="absolute left-2 top-2 rounded-full bg-brand-600 px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-white shadow-sm"
                                >
                                    New
                                </span>

                                <button
                                    type="button"
                                    :aria-label="`Remove new image ${index + 1}`"
                                    class="absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-full border border-white/60 bg-white/95 text-lg font-medium text-slate-600 shadow-sm backdrop-blur transition hover:bg-white hover:text-red-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500"
                                    @click="removeNewImage(index)"
                                >
                                    ×
                                </button>
                            </div>
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
                            Update the information customers see.
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
                                    class="w-full rounded-xl border border-border bg-background px-4 py-3 text-sm text-heading outline-none transition focus:border-brand-500 focus:bg-surface focus:ring-2 focus:ring-brand-100"
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
                                    class="w-full rounded-xl border border-border bg-background px-4 py-3 text-sm text-heading outline-none transition focus:border-brand-500 focus:bg-surface focus:ring-2 focus:ring-brand-100"
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

                <!-- ==================== Options & Variants ==================== -->

                <section
                    class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6"
                >
                    <div
                        class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
                    >
                        <div>
                            <h2 class="text-base font-extrabold text-heading">
                                Options & Variants
                            </h2>

                            <p
                                class="mt-1 max-w-xl text-sm leading-6 text-muted"
                            >
                                Manage options such as color, size, or storage
                                and configure each product variant.
                            </p>
                        </div>

                        <!-- ==================== Variant Toggle ==================== -->

                        <label
                            class="inline-flex cursor-pointer items-center gap-3 rounded-xl border border-border bg-background px-4 py-3"
                        >
                            <input
                                v-model="hasVariants"
                                type="checkbox"
                                class="h-4 w-4 rounded border-slate-300 accent-blue-600 focus:ring-brand-500"
                            />

                            <span class="text-sm font-bold text-heading">
                                Has variants
                            </span>
                        </label>
                    </div>

                    <!-- ==================== Options ==================== -->

                    <div v-if="hasVariants" class="mt-6 space-y-4">
                        <div
                            v-for="(option, optionIndex) in options"
                            :key="option.id ?? `new-${optionIndex}`"
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
                                    class="mt-3 rounded-lg px-1 py-1 text-sm font-bold text-brand-600 transition hover:text-brand-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500"
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

                        <!-- ==================== Generate Variants ==================== -->

                        <button
                            v-if="validOptions.length"
                            type="button"
                            class="w-full rounded-xl bg-brand-600 px-4 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-brand-700 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2"
                            @click="generateCombinations"
                        >
                            Generate Variants
                        </button>
                    </div>

                    <!-- ==================== Generated Variants ==================== -->

                    <div
                        v-if="hasVariants && variants.length"
                        class="mt-6 space-y-4"
                    >
                        <div>
                            <h3 class="text-sm font-extrabold text-heading">
                                Generated Variants
                            </h3>

                            <p class="mt-1 text-sm text-muted">
                                Set the price, stock, and SKU for each
                                combination.
                            </p>
                        </div>

                        <!-- ==================== Variant Rows ==================== -->

                        <div
                            v-for="(variant, variantIndex) in variants"
                            :key="variant.id ?? `new-variant-${variantIndex}`"
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
                                        class="w-full rounded-xl border border-border bg-surface px-3 py-2.5 text-sm text-heading outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-100"
                                    />
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
                                        class="w-full rounded-xl border border-border bg-surface px-3 py-2.5 text-sm text-heading outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-100"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- ==================== Total Variant Stock ==================== -->

                        <div
                            class="flex items-center justify-between rounded-xl border border-brand-100 bg-brand-50 px-4 py-3"
                        >
                            <div>
                                <p class="text-sm font-bold text-brand-700">
                                    Total Variant Stock
                                </p>

                                <p class="mt-0.5 text-xs text-brand-600">
                                    Calculated from all variants
                                </p>
                            </div>

                            <span class="text-xl font-extrabold text-brand-700">
                                {{ totalVariantStock }}
                            </span>
                        </div>
                    </div>
                </section>

                <!-- ==================== Form Actions ==================== -->

                <div
                    class="flex flex-col-reverse gap-3 border-t border-border pt-6 sm:flex-row sm:justify-end"
                >
                    <button
                        type="button"
                        class="w-full rounded-xl border border-border bg-surface px-5 py-3 text-sm font-bold text-body transition hover:bg-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 sm:w-auto"
                        @click="$inertia.visit('/seller/products')"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full rounded-xl bg-brand-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-brand-700 hover:shadow-md disabled:cursor-not-allowed disabled:opacity-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 sm:w-auto"
                    >
                        {{ form.processing ? "Saving..." : "Save Changes" }}
                    </button>
                </div>
            </form>
        </div>
    </SellerLayout>
</template>
