<script setup>
defineProps({
    company: {
        type: Object,
        default: null,
    },
});
</script>

<template>
    <div class="flex items-center justify-between gap-4 mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
        <div v-if="company && (company.logo_url || company.name)" class="flex items-center gap-3 min-w-0">
            <img
                v-if="company.logo_url"
                :src="company.logo_url"
                :alt="company.name ? `${company.name} logo` : 'Company logo'"
                class="h-12 max-h-14 w-auto max-w-[220px] object-contain object-left"
            />
            <div class="min-w-0">
                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Company</p>
                <p v-if="company.name" class="font-semibold text-gray-900 dark:text-white truncate">
                    {{ company.name }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <img src="/logos/lo.png" class="h-10 w-auto" alt="QSMCore Logo" />
            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">QSMCore</span>
        </div>
    </div>
</template>
