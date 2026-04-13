<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({ partner: Object });

const form = useForm({
    name:        props.partner.name,
    website:     props.partner.website || '',
    order:       props.partner.order ?? 0,
    is_active:   props.partner.is_active,
    logo:        null,
    remove_logo: false,
});

const preview = ref(props.partner.logo_url || null);
const hasExistingLogo = ref(!!props.partner.logo_url);

const onLogo = (e) => {
    form.logo = e.target.files[0] || null;
    form.remove_logo = false;
    if (form.logo) {
        const reader = new FileReader();
        reader.onload = (r) => preview.value = r.target.result;
        reader.readAsDataURL(form.logo);
    } else {
        preview.value = hasExistingLogo.value ? props.partner.logo_url : null;
    }
};

const removeLogo = () => {
    form.logo        = null;
    form.remove_logo = true;
    preview.value    = null;
    hasExistingLogo.value = false;
};

const submit = () => form.post(route('partners.update', props.partner.id), {
    forceFormData: true,
    _method: 'PUT',
});
</script>

<template>
    <Head title="Edit Partner" />
    <div class="p-6">
        <div class="mb-6">
            <Link :href="route('partners.index')" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">← Back to Partners</Link>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 max-w-2xl">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Edit Partner</h1>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Partner Name <span class="text-red-500">*</span></label>
                    <input v-model="form.name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Website URL</label>
                    <input v-model="form.website" type="url" placeholder="https://partner.com" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    <div v-if="form.errors.website" class="mt-1 text-sm text-red-600">{{ form.errors.website }}</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Display Order</label>
                    <input v-model.number="form.order" type="number" min="0" class="w-32 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    <p class="text-xs text-gray-500 mt-1">Lower number = appears first</p>
                </div>

                <!-- Logo upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Logo</label>
                    <div class="flex items-start gap-4">
                        <label class="flex flex-col items-center justify-center w-48 h-28 border-2 border-dashed rounded-xl cursor-pointer transition-colors"
                            :class="form.errors.logo ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-gray-50 hover:bg-blue-50 hover:border-blue-400'">
                            <div class="text-center px-3" v-if="!preview">
                                <svg class="w-8 h-8 text-gray-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-xs text-gray-500">Click to upload<br/>PNG, JPG, SVG (max 2MB)</p>
                            </div>
                            <img v-else :src="preview" class="h-24 w-full object-contain rounded-lg p-1" />
                            <input type="file" class="hidden" accept="image/*" @change="onLogo" />
                        </label>
                        <button v-if="preview" type="button" @click="removeLogo" class="text-xs text-red-600 hover:text-red-800 mt-2">Remove</button>
                    </div>
                    <div v-if="form.errors.logo" class="mt-1 text-sm text-red-600">{{ form.errors.logo }}</div>
                </div>

                <div class="flex items-center gap-3">
                    <input v-model="form.is_active" type="checkbox" id="is_active" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" />
                    <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Active (show on website)</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <Link :href="route('partners.index')" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300">Cancel</Link>
                    <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50">
                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
