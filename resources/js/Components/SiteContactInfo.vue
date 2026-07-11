<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

defineProps({
    variant: {
        type: String,
        default: 'footer',
        validator: (value) => ['footer', 'sidebar'].includes(value),
    },
})

const settings = computed(() => usePage().props.siteSettings ?? {})
</script>

<template>
    <template v-if="variant === 'sidebar'">
        <div class="space-y-6">
            <div v-if="settings.email">
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Email</div>
                <a
                    :href="`mailto:${settings.email}`"
                    class="text-sm text-gray-700 hover:text-brand-navy transition-colors"
                >{{ settings.email }}</a>
            </div>
            <div v-if="settings.phone">
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Phone</div>
                <a
                    :href="`tel:${settings.phone}`"
                    class="text-sm text-gray-700 hover:text-brand-navy transition-colors"
                >{{ settings.phone }}</a>
            </div>
            <div v-if="settings.address">
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Address</div>
                <p class="text-sm text-gray-500">{{ settings.address }}</p>
            </div>
        </div>
    </template>

    <template v-else>
        <li v-if="settings.email">Email: {{ settings.email }}</li>
        <li v-if="settings.phone">Phone: {{ settings.phone }}</li>
        <li v-if="settings.address">Address: {{ settings.address }}</li>
    </template>
</template>
