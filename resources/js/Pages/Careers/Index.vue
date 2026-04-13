<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({ vacancies: Array })

const search = ref('')
const selectedType = ref('')

const typeColors = {
    'full-time': 'bg-blue-100 text-blue-700',
    'part-time': 'bg-purple-100 text-purple-700',
    'contract':  'bg-yellow-100 text-yellow-700',
    'remote':    'bg-green-100 text-green-700',
}

const filtered = computed(() => {
    return (props.vacancies || []).filter(v => {
        const matchSearch = !search.value || v.title.toLowerCase().includes(search.value.toLowerCase())
            || (v.department || '').toLowerCase().includes(search.value.toLowerCase())
            || (v.location || '').toLowerCase().includes(search.value.toLowerCase())
        const matchType = !selectedType.value || v.type === selectedType.value
        return matchSearch && matchType
    })
})

const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : null
</script>

<template>
    <Head title="Careers — QSMCore" />

    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-900 to-blue-700 text-white">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
                <h1 class="text-4xl sm:text-5xl font-bold mb-4">Join Our Team</h1>
                <p class="text-blue-100 text-lg max-w-xl mx-auto">Help us build the future of Quality & Safety Management. Explore open positions below.</p>
                <div class="mt-4">
                    <Link href="/" class="text-blue-200 hover:text-white text-sm">← Back to Home</Link>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex flex-wrap gap-4">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search by title, department, location..."
                    class="flex-1 min-w-[200px] px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                />
                <select v-model="selectedType" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    <option value="">All Types</option>
                    <option value="full-time">Full-Time</option>
                    <option value="part-time">Part-Time</option>
                    <option value="contract">Contract</option>
                    <option value="remote">Remote</option>
                </select>
            </div>
        </div>

        <!-- Vacancies list -->
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <p class="text-sm text-gray-500 mb-6">{{ filtered.length }} open position{{ filtered.length !== 1 ? 's' : '' }}</p>

            <div v-if="filtered.length === 0" class="text-center py-20 text-gray-400">
                <i class="fa-solid fa-briefcase text-5xl mb-4 block opacity-30"></i>
                <p class="text-lg">No open positions match your search.</p>
            </div>

            <div class="space-y-4">
                <Link
                    v-for="v in filtered"
                    :key="v.id"
                    :href="route('careers.show', v.id)"
                    class="block bg-white rounded-xl border border-gray-200 p-6 hover:shadow-md hover:border-blue-300 transition-all duration-200 group"
                >
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <h2 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ v.title }}</h2>
                            <div class="flex flex-wrap gap-2 mt-2">
                                <span :class="typeColors[v.type]" class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize">{{ v.type }}</span>
                                <span v-if="v.department" class="bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-full text-xs font-medium">{{ v.department }}</span>
                                <span v-if="v.location" class="bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-full text-xs font-medium">📍 {{ v.location }}</span>
                                <span v-if="v.salary_range" class="bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-full text-xs font-medium">💰 {{ v.salary_range }}</span>
                            </div>
                            <p v-if="v.description" class="text-sm text-gray-500 mt-3 line-clamp-2">{{ v.description }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-2 flex-shrink-0">
                            <span v-if="v.closing_date" class="text-xs text-gray-400">Closes {{ formatDate(v.closing_date) }}</span>
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg group-hover:bg-blue-700 transition-colors">
                                Apply Now →
                            </span>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </div>
</template>
