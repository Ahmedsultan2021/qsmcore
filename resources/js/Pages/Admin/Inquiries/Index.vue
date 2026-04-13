<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import BaseDashboardHeader from "@/Components/BaseDashboardHeader.vue";
import { Head, router, useForm } from "@inertiajs/vue3";
import { computed, ref } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    inquiries: Object,
    unreadCount: Number,
    filters: Object,
});

const navs = computed(() => [
    { name: "Dashboard", linkName: "dashboard" },
    { name: "Inquiries", linkName: "inquiries.index" },
]);

const filterForm = useForm({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
});

const applyFilters = () => {
    filterForm.get(route('inquiries.index'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    filterForm.search = '';
    filterForm.status = '';
    applyFilters();
};

const markRead = (id) => {
    router.post(route('inquiries.mark-read', id), {}, { preserveScroll: true });
};

const markUnread = (id) => {
    router.post(route('inquiries.mark-unread', id), {}, { preserveScroll: true });
};

const deleteInquiry = (id) => {
    if (confirm('Are you sure you want to delete this inquiry?')) {
        router.delete(route('inquiries.destroy', id), { preserveScroll: true });
    }
};

const expanded = ref(null);
const toggle = (id) => {
    expanded.value = expanded.value === id ? null : id;
    // auto mark as read when opened
    const inquiry = props.inquiries.data.find(i => i.id === id);
    if (inquiry && !inquiry.is_read) markRead(id);
};

const formatDate = (date) => new Date(date).toLocaleDateString('en-GB', {
    day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
});
</script>

<template>
    <Head title="Inquiries" />

    <div class="p-6">
        <BaseDashboardHeader
            :navs="navs"
            title="Inquiries"
            :showButton="false"
            :addSearchInput="false"
        />

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                    <i class="fa-solid fa-envelope text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ inquiries.total }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Inquiries</p>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-100 dark:bg-red-900 flex items-center justify-center">
                    <i class="fa-solid fa-envelope-open text-red-600 dark:text-red-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ unreadCount }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Unread</p>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900 flex items-center justify-center">
                    <i class="fa-solid fa-check-double text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ inquiries.total - unreadCount }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Read</p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5 mt-6">
            <div class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Search</label>
                    <input
                        v-model="filterForm.search"
                        @keyup.enter="applyFilters"
                        type="text"
                        placeholder="Name, email, message..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status</label>
                    <select
                        v-model="filterForm.status"
                        @change="applyFilters"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                        <option value="">All</option>
                        <option value="unread">Unread</option>
                        <option value="read">Read</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button @click="applyFilters" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Filter</button>
                    <button @click="clearFilters" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 dark:bg-gray-600 dark:text-white">Clear</button>
                </div>
            </div>
        </div>

        <!-- List -->
        <div class="mt-6 space-y-3">
            <div
                v-if="inquiries.data.length === 0"
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center text-gray-500 dark:text-gray-400"
            >
                <i class="fa-solid fa-inbox text-4xl mb-3 block opacity-30"></i>
                No inquiries found.
            </div>

            <div
                v-for="inquiry in inquiries.data"
                :key="inquiry.id"
                :class="[
                    'bg-white dark:bg-gray-800 rounded-xl border transition-all duration-200',
                    !inquiry.is_read
                        ? 'border-blue-300 dark:border-blue-600 shadow-sm'
                        : 'border-gray-200 dark:border-gray-700'
                ]"
            >
                <!-- Row header -->
                <div
                    class="flex items-center gap-4 px-5 py-4 cursor-pointer select-none"
                    @click="toggle(inquiry.id)"
                >
                    <!-- Unread dot -->
                    <div class="flex-shrink-0 w-2.5 h-2.5 rounded-full mt-0.5" :class="!inquiry.is_read ? 'bg-blue-500' : 'bg-transparent'"></div>

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                            <span class="font-semibold text-gray-900 dark:text-white">{{ inquiry.name }}</span>
                            <a :href="`mailto:${inquiry.email}`" class="text-sm text-blue-600 dark:text-blue-400 hover:underline" @click.stop>{{ inquiry.email }}</a>
                            <span v-if="inquiry.phone" class="text-sm text-gray-500 dark:text-gray-400">{{ inquiry.phone }}</span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate mt-0.5">{{ inquiry.message }}</p>
                    </div>

                    <div class="flex items-center gap-3 flex-shrink-0">
                        <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">{{ formatDate(inquiry.created_at) }}</span>
                        <span
                            :class="!inquiry.is_read ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'"
                            class="text-xs font-medium px-2 py-0.5 rounded-full"
                        >
                            {{ inquiry.is_read ? 'Read' : 'New' }}
                        </span>
                        <svg
                            class="w-5 h-5 text-gray-400 transition-transform duration-200"
                            :class="{ 'rotate-180': expanded === inquiry.id }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>

                <!-- Expanded body -->
                <div v-if="expanded === inquiry.id" class="px-5 pb-5 border-t border-gray-100 dark:border-gray-700 pt-4">
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed">{{ inquiry.message }}</p>
                    <div class="flex flex-wrap gap-2 mt-5">
                        <a
                            :href="`mailto:${inquiry.email}?subject=Re: Your Inquiry&body=Hi ${inquiry.name},%0D%0A%0D%0A`"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
                        >
                            <i class="fa-solid fa-reply"></i> Reply via Email
                        </a>
                        <button
                            v-if="!inquiry.is_read"
                            @click="markRead(inquiry.id)"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-green-100 text-green-700 text-sm font-medium rounded-lg hover:bg-green-200 dark:bg-green-900 dark:text-green-300 transition-colors"
                        >
                            <i class="fa-solid fa-check"></i> Mark as Read
                        </button>
                        <button
                            v-else
                            @click="markUnread(inquiry.id)"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 transition-colors"
                        >
                            <i class="fa-solid fa-envelope"></i> Mark as Unread
                        </button>
                        <button
                            @click="deleteInquiry(inquiry.id)"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 text-red-700 text-sm font-medium rounded-lg hover:bg-red-200 dark:bg-red-900 dark:text-red-300 transition-colors"
                        >
                            <i class="fa-solid fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="inquiries.links && inquiries.links.length > 3" class="mt-6 flex justify-center">
            <nav class="inline-flex rounded-md shadow-sm -space-x-px">
                <a
                    v-for="link in inquiries.links"
                    :key="link.label"
                    :href="link.url || '#'"
                    v-html="link.label"
                    :class="[
                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                        link.active ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                        !link.url ? 'cursor-not-allowed opacity-50' : ''
                    ]"
                ></a>
            </nav>
        </div>
    </div>
</template>
