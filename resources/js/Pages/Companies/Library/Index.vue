<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import { Head, Link, router, useForm, usePage } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    documents: Object,
    categories: Array,
    uncategorized_count: Number,
    stats: Object,
    filters: Object,
    statuses: Array,
});

const page = usePage();
const authEmployeeId = page.props.authEmployee?.employee?.id;

const search = ref(props.filters?.search || "");
const selectedCategory = ref(props.filters?.category_id ?? "");
const selectedStatus = ref(props.filters?.status || "");
const favoritesOnly = ref(props.filters?.favorites || false);
const selectedDoc = ref(null);
const showAddCategory = ref(false);

const categoryForm = useForm({ name: "" });

const totalCategoryCount = computed(() => {
    const sum = (props.categories || []).reduce((a, c) => a + c.count, 0);
    return sum + (props.uncategorized_count || 0);
});

const applyFilters = () => {
    const params = {};
    if (search.value) params.search = search.value;
    if (selectedCategory.value !== "" && selectedCategory.value != null) params.category_id = selectedCategory.value;
    if (selectedStatus.value) params.status = selectedStatus.value;
    if (favoritesOnly.value) params.favorites = 1;
    router.get(route("companies.library.index"), params, { preserveState: true, replace: true });
};

const selectCategory = (id) => {
    selectedCategory.value = id;
    applyFilters();
};

watch([selectedStatus, favoritesOnly], () => applyFilters());

const submitCategory = () => {
    categoryForm.post(route("companies.library.categories.store"), {
        onSuccess: () => {
            categoryForm.reset();
            showAddCategory.value = false;
        },
    });
};

const toggleFavorite = (doc, e) => {
    e?.stopPropagation();
    router.post(route("companies.library.favorite", doc.id), {}, { preserveScroll: true });
};

const selectDocument = (doc) => {
    selectedDoc.value = selectedDoc.value?.id === doc.id ? null : doc;
};

const formatBytes = (bytes) => {
    if (!bytes) return "";
    const sizes = ["B", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    return (bytes / Math.pow(1024, i)).toFixed(1) + " " + sizes[i];
};

const formatDate = (dateStr) => {
    if (!dateStr) return "—";
    return new Date(dateStr).toLocaleDateString("en-GB", { day: "2-digit", month: "short", year: "numeric" });
};

const statusStyle = (status) => {
    if (status === "effective") return "bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300";
    if (status === "under_review") return "bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300";
    if (status === "draft") return "bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300";
    return "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300";
};

const statusLabel = (status) => {
    if (status === "effective") return "Effective";
    if (status === "under_review") return "Under Review";
    if (status === "draft") return "Draft";
    return status;
};

const ownerName = (doc) => {
    if (doc.owner) return `${doc.owner.fname} ${doc.owner.lname}`;
    if (doc.uploader) return `${doc.uploader.fname} ${doc.uploader.lname}`;
    return "—";
};

const canEdit = (doc) =>
    doc.uploaded_by === authEmployeeId || doc.owner_employee_id === authEmployeeId;

const confirmDelete = (doc) => {
    if (confirm(`Delete "${doc.title}"? This cannot be undone.`)) {
        router.delete(route("companies.library.destroy", doc.id));
    }
};
</script>

<template>
    <Head title="Library" />

    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Library</p>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3 mt-1">
                    <i class="fa-solid fa-book-open text-indigo-600"></i>
                    Manuals & Documents
                </h1>
            </div>
            <Link
                :href="route('companies.library.create')"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition shadow-sm"
            >
                <i class="fa-solid fa-plus"></i>
                Upload Document
            </Link>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <p class="text-xs text-gray-500 dark:text-gray-400">Total Documents</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.total }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <p class="text-xs text-gray-500 dark:text-gray-400">Drafts</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ stats.drafts }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <p class="text-xs text-gray-500 dark:text-gray-400">Under Review</p>
                <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ stats.under_review }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <p class="text-xs text-gray-500 dark:text-gray-400">Effective</p>
                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ stats.effective }}</p>
            </div>
        </div>

        <!-- Search & filters -->
        <div class="mb-4 flex flex-wrap gap-3 items-center">
            <div class="flex-1 min-w-[200px] max-w-md relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input
                    v-model="search"
                    type="search"
                    placeholder="Search title or document code..."
                    class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
                    @keyup.enter="applyFilters"
                />
            </div>
            <button
                type="button"
                class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700"
                @click="applyFilters"
            >
                Search
            </button>
            <select
                v-model="selectedStatus"
                class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
            >
                <option value="">All statuses</option>
                <option v-for="s in statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
            </select>
            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                <input v-model="favoritesOnly" type="checkbox" class="rounded border-gray-300 text-indigo-600" />
                <i class="fa-solid fa-star text-amber-500"></i> Favorites only
            </label>
        </div>

        <div class="flex flex-col xl:flex-row gap-6">
            <!-- Categories sidebar -->
            <aside class="xl:w-56 shrink-0">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                    <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase px-2 mb-2">Categories</h2>
                    <ul class="space-y-0.5">
                        <li>
                            <button
                                type="button"
                                class="w-full text-left px-3 py-2 rounded-md text-sm font-medium transition"
                                :class="selectedCategory === '' || selectedCategory === 'all'
                                    ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300'
                                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                @click="selectCategory('all')"
                            >
                                All Documents
                                <span class="float-right text-xs opacity-70">{{ totalCategoryCount }}</span>
                            </button>
                        </li>
                        <li v-for="cat in categories" :key="cat.id">
                            <button
                                type="button"
                                class="w-full text-left px-3 py-2 rounded-md text-sm transition"
                                :class="String(selectedCategory) === String(cat.id)
                                    ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 font-medium'
                                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                @click="selectCategory(cat.id)"
                            >
                                {{ cat.name }}
                                <span class="float-right text-xs opacity-70">{{ cat.count }}</span>
                            </button>
                        </li>
                        <li v-if="uncategorized_count > 0">
                            <button
                                type="button"
                                class="w-full text-left px-3 py-2 rounded-md text-sm transition"
                                :class="selectedCategory === 'uncategorized'
                                    ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 font-medium'
                                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                @click="selectCategory('uncategorized')"
                            >
                                Uncategorized
                                <span class="float-right text-xs opacity-70">{{ uncategorized_count }}</span>
                            </button>
                        </li>
                    </ul>
                    <button
                        type="button"
                        class="mt-3 w-full text-left px-3 py-2 text-sm text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-md"
                        @click="showAddCategory = !showAddCategory"
                    >
                        <i class="fa-solid fa-plus mr-1"></i> Add Category
                    </button>
                    <form v-if="showAddCategory" class="mt-2 px-2 space-y-2" @submit.prevent="submitCategory">
                        <input
                            v-model="categoryForm.name"
                            type="text"
                            placeholder="Category name"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
                        />
                        <button
                            type="submit"
                            :disabled="categoryForm.processing"
                            class="w-full py-1.5 text-xs font-semibold rounded-md bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-50"
                        >
                            Save
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Table + detail panel -->
            <div class="flex-1 min-w-0 flex flex-col lg:flex-row gap-4">
                <div class="flex-1 min-w-0">
                    <div
                        v-if="!documents.data || documents.data.length === 0"
                        class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-10 text-center"
                    >
                        <i class="fa-solid fa-folder-open text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">No documents found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try another category or upload a new document.</p>
                    </div>

                    <div v-else class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700/50 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                    <tr>
                                        <th class="px-4 py-3 w-8"></th>
                                        <th class="px-4 py-3">Document</th>
                                        <th class="px-4 py-3 hidden md:table-cell">Version</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3 hidden sm:table-cell">Effective</th>
                                        <th class="px-4 py-3 hidden lg:table-cell">Category</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr
                                        v-for="doc in documents.data"
                                        :key="doc.id"
                                        class="cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition"
                                        :class="selectedDoc?.id === doc.id ? 'bg-indigo-50/50 dark:bg-indigo-900/20' : ''"
                                        @click="selectDocument(doc)"
                                    >
                                        <td class="px-4 py-3" @click.stop>
                                            <button
                                                type="button"
                                                class="text-gray-400 hover:text-amber-500 transition"
                                                :title="doc.is_favorited ? 'Remove favorite' : 'Add favorite'"
                                                @click="toggleFavorite(doc, $event)"
                                            >
                                                <i :class="doc.is_favorited ? 'fa-solid fa-star text-amber-500' : 'fa-regular fa-star'"></i>
                                            </button>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="font-semibold text-gray-900 dark:text-white">{{ doc.title }}</div>
                                            <div v-if="doc.document_code" class="text-xs text-gray-500 dark:text-gray-400">{{ doc.document_code }}</div>
                                        </td>
                                        <td class="px-4 py-3 hidden md:table-cell text-gray-600 dark:text-gray-300">
                                            {{ doc.version_label || "—" }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium" :class="statusStyle(doc.status)">
                                                {{ statusLabel(doc.status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 hidden sm:table-cell text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                            {{ formatDate(doc.effective_date) }}
                                        </td>
                                        <td class="px-4 py-3 hidden lg:table-cell text-gray-600 dark:text-gray-300">
                                            {{ doc.category?.name || "—" }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div v-if="documents.links && documents.last_page > 1" class="mt-4 flex justify-center">
                        <nav class="inline-flex gap-1">
                            <template v-for="link in documents.links" :key="link.label">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    class="px-3 py-2 text-sm rounded-md border transition"
                                    :class="link.active
                                        ? 'bg-indigo-600 text-white border-indigo-600'
                                        : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                    v-html="link.label"
                                />
                                <span
                                    v-else
                                    class="px-3 py-2 text-sm rounded-md border border-gray-200 dark:border-gray-700 text-gray-400"
                                    v-html="link.label"
                                />
                            </template>
                        </nav>
                    </div>
                </div>

                <!-- Detail panel -->
                <aside
                    v-if="selectedDoc"
                    class="lg:w-80 shrink-0 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 shadow-sm"
                >
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">{{ selectedDoc.title }}</h3>
                        <button type="button" class="text-gray-400 hover:text-gray-600" @click="selectedDoc = null">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <p v-if="selectedDoc.document_code" class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ selectedDoc.document_code }}</p>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium mb-4" :class="statusStyle(selectedDoc.status)">
                        {{ statusLabel(selectedDoc.status) }}
                    </span>

                    <dl class="space-y-2 text-sm mb-4">
                        <div class="flex justify-between gap-2">
                            <dt class="text-gray-500 dark:text-gray-400">Version</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">{{ selectedDoc.version_label || "—" }}</dd>
                        </div>
                        <div class="flex justify-between gap-2">
                            <dt class="text-gray-500 dark:text-gray-400">Effective</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">{{ formatDate(selectedDoc.effective_date) }}</dd>
                        </div>
                        <div class="flex justify-between gap-2">
                            <dt class="text-gray-500 dark:text-gray-400">Owner</dt>
                            <dd class="font-medium text-gray-900 dark:text-white text-right">{{ ownerName(selectedDoc) }}</dd>
                        </div>
                        <div class="flex justify-between gap-2">
                            <dt class="text-gray-500 dark:text-gray-400">Category</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">{{ selectedDoc.category?.name || "—" }}</dd>
                        </div>
                        <div class="flex justify-between gap-2">
                            <dt class="text-gray-500 dark:text-gray-400">File</dt>
                            <dd class="text-gray-900 dark:text-white">{{ formatBytes(selectedDoc.file_size) }}</dd>
                        </div>
                    </dl>

                    <p v-if="selectedDoc.description" class="text-sm text-gray-600 dark:text-gray-300 mb-4 line-clamp-4">
                        {{ selectedDoc.description }}
                    </p>

                    <div class="space-y-2">
                        <a
                            :href="selectedDoc.view_url"
                            target="_blank"
                            rel="noopener"
                            class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700"
                        >
                            <i class="fa-solid fa-eye"></i> Open
                        </a>
                        <a
                            :href="selectedDoc.download_url"
                            class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm font-semibold hover:bg-gray-50 dark:hover:bg-gray-700"
                        >
                            <i class="fa-solid fa-download"></i> Download
                        </a>
                    </div>

                    <div v-if="canEdit(selectedDoc)" class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 flex gap-2">
                        <Link
                            :href="route('companies.library.edit', selectedDoc.id)"
                            class="flex-1 text-center px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-sm font-semibold hover:bg-gray-50 dark:hover:bg-gray-700"
                        >
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </Link>
                        <button
                            type="button"
                            class="px-3 py-2 rounded-md border border-red-300 text-red-600 text-sm hover:bg-red-50 dark:hover:bg-red-900/20"
                            @click="confirmDelete(selectedDoc)"
                        >
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</template>
