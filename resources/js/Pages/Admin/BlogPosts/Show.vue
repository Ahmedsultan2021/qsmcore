<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    blogPost: Object,
});

const deleteBlogPost = () => {
    if (confirm("Are you sure you want to delete this blog post?")) {
        router.delete(route("blog-posts.destroy", props.blogPost.id));
    }
};
</script>

<template>
    <Head :title="blogPost.title" />

    <div class="p-6">
        <div class="mb-6">
            <Link
                :href="route('blog-posts.index')"
                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
            >
                ← Back to Blog Posts
            </Link>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex justify-between items-start mb-6">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ blogPost.title }}</h1>
                    <div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400 mb-4">
                        <span>By {{ blogPost.user?.name || "Unknown" }}</span>
                        <span v-if="blogPost.published_at">
                            • {{ new Date(blogPost.published_at).toLocaleDateString() }}
                        </span>
                        <span
                            :class="[
                                'px-2 py-1 rounded-full text-xs font-semibold',
                                blogPost.is_published
                                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                    : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                            ]"
                        >
                            {{ blogPost.is_published ? "Published" : "Draft" }}
                        </span>
                    </div>
                    <p v-if="blogPost.excerpt" class="text-lg text-gray-700 dark:text-gray-300 italic mb-4">
                        {{ blogPost.excerpt }}
                    </p>
                </div>
                <div class="flex space-x-3 ml-4">
                    <Link
                        :href="route('blog-posts.edit', blogPost.id)"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                    >
                        Edit
                    </Link>
                    <button
                        @click="deleteBlogPost"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                    >
                        Delete
                    </button>
                </div>
            </div>

            <div v-if="blogPost.featured_image" class="mb-6">
                <img
                    :src="blogPost.featured_image"
                    :alt="blogPost.title"
                    class="w-full h-64 object-cover rounded-lg"
                />
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <div class="prose max-w-none dark:prose-invert">
                    <div class="whitespace-pre-wrap text-gray-700 dark:text-gray-300 leading-relaxed">
                        {{ blogPost.content }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
