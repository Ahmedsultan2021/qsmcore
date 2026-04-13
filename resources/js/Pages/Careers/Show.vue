<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    vacancy:       Object,
    apply_success: Boolean,
})

const typeColors = {
    'full-time': 'bg-blue-100 text-blue-700',
    'part-time': 'bg-purple-100 text-purple-700',
    'contract':  'bg-yellow-100 text-yellow-700',
    'remote':    'bg-green-100 text-green-700',
}

const form = useForm({
    name:         '',
    email:        '',
    phone:        '',
    cover_letter: '',
    cv:           null,
})

const cvName = ref('')

const onCvChange = (e) => {
    form.cv = e.target.files[0] || null
    cvName.value = form.cv?.name || ''
}

const submit = () => {
    form.post(route('careers.apply', props.vacancy.id), {
        forceFormData: true,
        onSuccess: () => { form.reset(); cvName.value = '' },
    })
}

const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : null
</script>

<template>
    <Head :title="`${vacancy.title} — Careers`" />

    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-900 to-blue-700 text-white">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <Link :href="route('careers.index')" class="text-blue-200 hover:text-white text-sm mb-4 inline-block">← All Positions</Link>
                <h1 class="text-3xl sm:text-4xl font-bold">{{ vacancy.title }}</h1>
                <div class="flex flex-wrap gap-2 mt-3">
                    <span :class="typeColors[vacancy.type]" class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize">{{ vacancy.type }}</span>
                    <span v-if="vacancy.department" class="bg-white/20 text-white px-2.5 py-0.5 rounded-full text-xs font-medium">{{ vacancy.department }}</span>
                    <span v-if="vacancy.location" class="bg-white/20 text-white px-2.5 py-0.5 rounded-full text-xs font-medium">📍 {{ vacancy.location }}</span>
                    <span v-if="vacancy.salary_range" class="bg-white/20 text-white px-2.5 py-0.5 rounded-full text-xs font-medium">💰 {{ vacancy.salary_range }}</span>
                    <span v-if="vacancy.closing_date" class="bg-white/20 text-white px-2.5 py-0.5 rounded-full text-xs font-medium">Closes {{ formatDate(vacancy.closing_date) }}</span>
                </div>
            </div>
        </div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Details -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-6">
                    <div>
                        <h2 class="text-base font-bold text-gray-900 mb-2">Job Description</h2>
                        <p class="text-gray-600 whitespace-pre-wrap leading-relaxed text-sm">{{ vacancy.description }}</p>
                    </div>
                    <div v-if="vacancy.requirements">
                        <h2 class="text-base font-bold text-gray-900 mb-2">Requirements & Qualifications</h2>
                        <p class="text-gray-600 whitespace-pre-wrap leading-relaxed text-sm">{{ vacancy.requirements }}</p>
                    </div>
                    <div v-if="vacancy.benefits">
                        <h2 class="text-base font-bold text-gray-900 mb-2">Benefits & Perks</h2>
                        <p class="text-gray-600 whitespace-pre-wrap leading-relaxed text-sm">{{ vacancy.benefits }}</p>
                    </div>
                </div>
            </div>

            <!-- Right: Apply Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl border border-gray-200 p-6 sticky top-6">
                    <!-- Success -->
                    <div v-if="apply_success" class="flex items-start gap-3 bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 mb-5">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <div>
                            <p class="font-semibold">Application Submitted!</p>
                            <p class="text-sm mt-0.5">We'll review it and get back to you soon.</p>
                        </div>
                    </div>

                    <h3 class="text-lg font-bold text-gray-900 mb-5">Apply for this Position</h3>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input v-model="form.name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" :class="{ 'border-red-400': form.errors.name }" />
                            <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input v-model="form.email" type="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" :class="{ 'border-red-400': form.errors.email }" />
                            <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input v-model="form.phone" type="tel" placeholder="+1 234 567 8900" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cover Letter</label>
                            <textarea v-model="form.cover_letter" rows="4" placeholder="Tell us why you're a great fit..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CV / Resume <span class="text-red-500">*</span></label>
                            <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed rounded-lg cursor-pointer transition-colors"
                                :class="form.errors.cv ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-gray-50 hover:bg-blue-50 hover:border-blue-400'">
                                <div class="text-center px-3">
                                    <i class="fa-solid fa-file-arrow-up text-2xl mb-1" :class="cvName ? 'text-blue-600' : 'text-gray-400'"></i>
                                    <p class="text-xs text-gray-500" v-if="!cvName">Click to upload PDF, DOC or DOCX (max 5MB)</p>
                                    <p class="text-xs text-blue-600 font-medium truncate max-w-full" v-else>{{ cvName }}</p>
                                </div>
                                <input type="file" class="hidden" accept=".pdf,.doc,.docx" @change="onCvChange" />
                            </label>
                            <p v-if="form.errors.cv" class="text-red-500 text-xs mt-1">{{ form.errors.cv }}</p>
                        </div>

                        <button type="submit" :disabled="form.processing"
                            class="w-full py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-60 disabled:cursor-not-allowed text-sm">
                            {{ form.processing ? 'Submitting...' : 'Submit Application' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
