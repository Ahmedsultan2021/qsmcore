<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    blogPosts: Object,
})

const formatDate = (dateString) => {
    if (!dateString) return ''
    return new Date(dateString).toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    })
}

const getPaginationLabel = (label) => {
    // Extract text from HTML entities
    const tempDiv = document.createElement('div')
    tempDiv.innerHTML = label
    return tempDiv.textContent || tempDiv.innerText || label
}
</script>

<template>
    <Head title="Blog - Quality & Safety Management" />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
        <!-- Navigation -->
        <nav class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-200/50 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <div class="flex items-center space-x-3">
                        <div class="bg-gradient-to-br from-blue-600 to-indigo-600 p-2 rounded-xl shadow-lg">
                            <img src="/logos/logo-width.png" class="h-10 w-auto" alt="QSM Logo" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                                QSM
                            </h1>
                            <p class="text-xs text-gray-500">Quality & Safety Management</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-6">
                        <Link href="/" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">
                            Home
                        </Link>
                        <Link 
                            :href="route('blog.index')" 
                            class="text-blue-600 font-semibold transition-colors"
                        >
                            Blog
                        </Link>
                        <Link 
                            :href="route('login')" 
                            class="text-gray-700 hover:text-blue-600 font-medium transition-colors"
                        >
                            Admin Portal
                        </Link>
                        <Link 
                            :href="route('companies.login')" 
                            class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-2.5 rounded-lg hover:from-blue-700 hover:to-indigo-700 font-semibold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5"
                        >
                            Company Portal
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Blog Header -->
        <section class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">QSM Blog</h1>
                <p class="text-xl text-blue-100 max-w-2xl mx-auto">
                    Stay updated with the latest insights, news, and best practices in quality and safety management
                </p>
            </div>
        </section>

        <!-- Blog Posts Grid -->
        <section class="py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div v-if="blogPosts.data && blogPosts.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <article
                        v-for="post in blogPosts.data"
                        :key="post.id"
                        class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden"
                    >
                        <div v-if="post.featured_image" class="relative h-48 overflow-hidden">
                            <img
                                :src="post.featured_image"
                                :alt="post.title"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                            />
                        </div>
                        <div v-else class="h-48 bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                            <svg class="w-16 h-16 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center text-sm text-gray-500 mb-3">
                                <span>{{ formatDate(post.published_at) }}</span>
                                <span class="mx-2">•</span>
                                <span>{{ post.user?.name || 'Admin' }}</span>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors line-clamp-2">
                                {{ post.title }}
                            </h2>
                            <p v-if="post.excerpt" class="text-gray-600 mb-4 line-clamp-3">
                                {{ post.excerpt }}
                            </p>
                            <p v-else class="text-gray-600 mb-4 line-clamp-3">
                                {{ post.content.substring(0, 150) }}{{ post.content.length > 150 ? '...' : '' }}
                            </p>
                            <Link
                                href="#"
                                class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-700 group-hover:translate-x-1 transition-transform"
                            >
                                Read More
                                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                        </div>
                    </article>
                </div>
                <div v-else class="text-center py-16">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">No blog posts yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Check back soon for updates!</p>
                </div>

                <!-- Pagination -->
                <div v-if="blogPosts.links && blogPosts.links.length > 3" class="mt-12 flex items-center justify-center">
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <template v-for="link in blogPosts.links" :key="link.label">
                            <a
                                v-if="link.url"
                                :href="link.url"
                                :class="[
                                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors',
                                    link.active
                                        ? 'z-10 bg-blue-600 border-blue-600 text-white'
                                        : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50',
                                    link.label.includes('Previous') ? 'rounded-l-md' : '',
                                    link.label.includes('Next') ? 'rounded-r-md' : ''
                                ]"
                            >
                                <span v-html="link.label"></span>
                            </a>
                            <span
                                v-else
                                :class="[
                                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium cursor-not-allowed opacity-50',
                                    'bg-white border-gray-300 text-gray-700',
                                    link.label.includes('Previous') ? 'rounded-l-md' : '',
                                    link.label.includes('Next') ? 'rounded-r-md' : ''
                                ]"
                            >
                                <span v-html="link.label"></span>
                            </span>
                        </template>
                    </nav>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    <div>
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="bg-gradient-to-br from-blue-600 to-indigo-600 p-2 rounded-lg">
                                <img src="/logos/logo-width.png" class="h-8 w-auto" alt="QSM Logo" />
                            </div>
                            <h3 class="text-xl font-bold">QSM</h3>
                        </div>
                        <p class="text-gray-400">
                            Quality & Safety Management System - Empowering organizations to achieve excellence in quality and safety standards.
                        </p>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><Link href="/" class="hover:text-white transition-colors">Home</Link></li>
                            <li><Link :href="route('blog.index')" class="hover:text-white transition-colors">Blog</Link></li>
                            <li><Link :href="route('login')" class="hover:text-white transition-colors">Admin Portal</Link></li>
                            <li><Link :href="route('companies.login')" class="hover:text-white transition-colors">Company Portal</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Contact</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li>Email: support@qsm.com</li>
                            <li>Phone: +1 (555) 123-4567</li>
                            <li>Address: 123 Business St, Suite 100</li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
                    <p>&copy; 2025 Quality & Safety Management System. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
