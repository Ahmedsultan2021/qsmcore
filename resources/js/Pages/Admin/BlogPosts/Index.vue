<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import BaseDashboardHeader from "@/Components/BaseDashboardHeader.vue";
import { Head, Link, router } from "@inertiajs/vue3";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    blogPosts: Object,
});

const deleteBlogPost = (id) => {
    if (confirm("Are you sure you want to delete this blog post?")) {
        router.delete(route("blog-posts.destroy", id));
    }
};

const navs = [
    { name: "Dashboard", linkName: "dashboard" },
    { name: "Blog Posts", linkName: "blog-posts.index" },
];
</script>

<template>
    <Head title="Blog Posts" />

    <div class="p-6 pt-20">
        <BaseDashboardHeader
            :navs="navs"
            title="Blog Posts"
            navLinkName="blog-posts.create"
            NavLinkText="Add New Post"
            :showButton="true"
            :addSearchInput="false"
        />

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mt-6">
            <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Title
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Author
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Published At
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="post in blogPosts.data" :key="post.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                            {{ post.title }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ post.user?.name || "-" }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span
                                :class="[
                                    'px-2 py-1 rounded-full text-xs font-semibold',
                                    post.is_published
                                        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                        : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                                ]"
                            >
                                {{ post.is_published ? "Published" : "Draft" }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ post.published_at ? new Date(post.published_at).toLocaleDateString() : "-" }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <Link
                                :href="route('blog-posts.show', post.id)"
                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3"
                            >
                                View
                            </Link>
                            <Link
                                :href="route('blog-posts.edit', post.id)"
                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3"
                            >
                                Edit
                            </Link>
                            <button
                                @click="deleteBlogPost(post.id)"
                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>

            <div v-if="blogPosts.links && blogPosts.links.length > 3" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <Link
                            v-if="blogPosts.prev_page_url"
                            :href="blogPosts.prev_page_url"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Previous
                        </Link>
                        <Link
                            v-if="blogPosts.next_page_url"
                            :href="blogPosts.next_page_url"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Next
                        </Link>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Showing
                                <span class="font-medium">{{ blogPosts.from }}</span>
                                to
                                <span class="font-medium">{{ blogPosts.to }}</span>
                                of
                                <span class="font-medium">{{ blogPosts.total }}</span>
                                results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <Link
                                    v-for="link in blogPosts.links"
                                    :key="link.label"
                                    :href="link.url || '#'"
                                    v-html="link.label"
                                    :class="[
                                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                        link.active
                                            ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                        link.url ? '' : 'cursor-not-allowed opacity-50'
                                    ]"
                                ></Link>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
