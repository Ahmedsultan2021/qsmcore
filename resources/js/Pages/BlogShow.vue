<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import SiteContactInfo from '@/Components/SiteContactInfo.vue'

const props = defineProps({
    blogPost: Object,
})

const mobileMenuOpen = ref(false)

const authorName = computed(() => {
    const user = props.blogPost.user || {}
    const full = [user.fname, user.lname].filter(Boolean).join(' ').trim()
    return full || user.name || 'Admin'
})

const formatDate = (dateString) => {
    if (!dateString) return ''
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
}

const isHtml = computed(() => /<\/?[a-z][\s\S]*>/i.test(props.blogPost.content || ''))
</script>

<template>
    <Head :title="blogPost.title" />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
        <nav class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-200/50 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16 sm:h-20">
                    <div class="flex items-center space-x-2 sm:space-x-3 min-w-0">
                        <img src="/logos/lo.png" class="h-16 sm:h-20 w-auto flex-shrink-0" alt="QSMCore Logo" />
                        <div class="min-w-0">
                            <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent truncate">
                                QSMCore
                            </h1>
                            <p class="text-xs text-gray-500 hidden sm:block">Quality & Safety Management</p>
                        </div>
                    </div>
                    <div class="hidden lg:flex items-center space-x-4 xl:space-x-6">
                        <Link href="/" class="text-gray-700 hover:text-blue-600 font-medium transition-colors whitespace-nowrap">
                            Home
                        </Link>
                        <Link
                            :href="route('blog.index')"
                            class="text-blue-600 font-semibold transition-colors whitespace-nowrap"
                        >
                            Blog
                        </Link>
                        <Link
                            :href="route('login')"
                            class="text-gray-700 hover:text-blue-600 font-medium transition-colors whitespace-nowrap"
                        >
                            Admin Portal
                        </Link>
                        <Link
                            :href="route('companies.login')"
                            class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 xl:px-6 py-2 xl:py-2.5 rounded-lg hover:from-blue-700 hover:to-indigo-700 font-semibold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5 whitespace-nowrap text-sm xl:text-base"
                        >
                            Company Portal
                        </Link>
                    </div>
                    <button
                        type="button"
                        class="lg:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        aria-label="Toggle menu"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                    >
                        <svg v-if="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 -translate-y-2"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 -translate-y-2"
                >
                    <div v-show="mobileMenuOpen" class="lg:hidden border-t border-gray-200 py-4 space-y-1">
                        <Link href="/" class="block py-2 px-3 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg font-medium" @click="mobileMenuOpen = false">Home</Link>
                        <Link :href="route('blog.index')" class="block py-2 px-3 text-blue-600 bg-blue-50 rounded-lg font-semibold" @click="mobileMenuOpen = false">Blog</Link>
                        <Link :href="route('login')" class="block py-2 px-3 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg font-medium" @click="mobileMenuOpen = false">Admin Portal</Link>
                        <Link :href="route('companies.login')" class="block py-3 px-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-semibold text-center hover:from-blue-700 hover:to-indigo-700" @click="mobileMenuOpen = false">Company Portal</Link>
                    </div>
                </Transition>
            </div>
        </nav>

        <article class="py-10 sm:py-14">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <Link
                    :href="route('blog.index')"
                    class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-700 mb-6"
                >
                    <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Blog
                </Link>

                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <img
                        v-if="blogPost.featured_image"
                        :src="blogPost.featured_image"
                        :alt="blogPost.title"
                        class="w-full h-56 sm:h-80 object-cover"
                    />
                    <div class="p-6 sm:p-10">
                        <div class="flex items-center text-sm text-gray-500 mb-4">
                            <span>{{ formatDate(blogPost.published_at) }}</span>
                            <span class="mx-2">•</span>
                            <span>By {{ authorName }}</span>
                        </div>
                        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                            {{ blogPost.title }}
                        </h1>
                        <p v-if="blogPost.excerpt" class="text-lg text-gray-600 mb-8">
                            {{ blogPost.excerpt }}
                        </p>
                        <div
                            v-if="isHtml"
                            class="prose max-w-none text-gray-700 leading-relaxed"
                            v-html="blogPost.content"
                        />
                        <div v-else class="whitespace-pre-wrap text-gray-700 leading-relaxed">
                            {{ blogPost.content }}
                        </div>
                    </div>
                </div>
            </div>
        </article>

        <footer class="bg-gray-900 text-white py-10 sm:py-12 mt-12 sm:mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 sm:gap-8 mb-6 sm:mb-8">
                    <div class="sm:col-span-2 md:col-span-1">
                        <div class="flex items-center space-x-2 sm:space-x-3 mb-3 sm:mb-4">
                            <div class="bg-gradient-to-br from-blue-600 to-indigo-600 p-1.5 sm:p-2 rounded-lg flex-shrink-0">
                                <img src="/logos/lo.png" class="h-6 sm:h-8 w-auto" alt="QSMCore Logo" />
                            </div>
                            <h3 class="text-lg sm:text-xl font-bold">QSMCore</h3>
                        </div>
                        <p class="text-gray-400 text-sm sm:text-base">
                            From Reporting to Insight – One Platform for Safety & Quality across industries.
                        </p>
                    </div>
                    <div>
                        <h4 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4">Quick Links</h4>
                        <ul class="space-y-1.5 sm:space-y-2 text-gray-400 text-sm sm:text-base">
                            <li><Link href="/" class="hover:text-white transition-colors">Home</Link></li>
                            <li><Link :href="route('blog.index')" class="hover:text-white transition-colors">Blog</Link></li>
                            <li><Link :href="route('login')" class="hover:text-white transition-colors">Admin Portal</Link></li>
                            <li><Link :href="route('companies.login')" class="hover:text-white transition-colors">Company Portal</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4">Contact</h4>
                        <ul class="space-y-1.5 sm:space-y-2 text-gray-400 text-sm sm:text-base break-words">
                            <SiteContactInfo variant="footer" />
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-800 pt-6 sm:pt-8 text-center text-gray-400 text-sm sm:text-base">
                    <p>&copy; 2025 QSMCore. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>
