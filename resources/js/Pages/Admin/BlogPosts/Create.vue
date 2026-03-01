<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineOptions({ layout: AuthenticatedLayout });

const form = useForm({
    title: "",
    content: "",
    excerpt: "",
    featured_image: "",
    is_published: false,
    published_at: "",
});

const submit = () => {
    form.post(route("blog-posts.store"));
};
</script>

<template>
    <Head title="Create Blog Post" />

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
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Create New Blog Post</h1>

            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="title"
                        v-model="form.title"
                        type="text"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                    <div v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</div>
                </div>

                <div class="mb-4">
                    <label for="excerpt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Excerpt
                    </label>
                    <textarea
                        id="excerpt"
                        v-model="form.excerpt"
                        rows="3"
                        maxlength="500"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Brief summary of the post (max 500 characters)"
                    ></textarea>
                    <div v-if="form.errors.excerpt" class="mt-1 text-sm text-red-600">{{ form.errors.excerpt }}</div>
                </div>

                <div class="mb-4">
                    <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Content <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        id="content"
                        v-model="form.content"
                        rows="15"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Write your blog post content here..."
                    ></textarea>
                    <div v-if="form.errors.content" class="mt-1 text-sm text-red-600">{{ form.errors.content }}</div>
                </div>

                <div class="mb-4">
                    <label for="featured_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Featured Image URL
                    </label>
                    <input
                        id="featured_image"
                        v-model="form.featured_image"
                        type="url"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="https://example.com/image.jpg"
                    />
                    <div v-if="form.errors.featured_image" class="mt-1 text-sm text-red-600">{{ form.errors.featured_image }}</div>
                </div>

                <div class="mb-4">
                    <label class="flex items-center">
                        <input
                            v-model="form.is_published"
                            type="checkbox"
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        />
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Publish immediately</span>
                    </label>
                </div>

                <div v-if="form.is_published" class="mb-4">
                    <label for="published_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Published At
                    </label>
                    <input
                        id="published_at"
                        v-model="form.published_at"
                        type="datetime-local"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                    <div v-if="form.errors.published_at" class="mt-1 text-sm text-red-600">{{ form.errors.published_at }}</div>
                </div>

                <div class="flex items-center justify-end space-x-3">
                    <Link
                        :href="route('blog-posts.index')"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                    >
                        {{ form.processing ? "Creating..." : "Create Post" }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
