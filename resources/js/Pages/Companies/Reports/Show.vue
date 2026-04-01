<script setup>
import CompanyLayout from "@/Layouts/CompanyLayout.vue";
import BaseDashboardHeader from "@/Components/BaseDashboardHeader.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const MAX_DROP_FILES = 30;

defineOptions({ layout: CompanyLayout });

const props = defineProps({
    department: Object,
    report: Object,
    submittedForms: Array,
    generalReportStatus: String,
});

const navs = computed(() => [
    { name: "Dashboard", linkName: "companies.dashboard" },
    { name: "Departments", linkName: "companies.departments.index" },
    { name: props.department.name, linkName: "companies.departments.show", param: { department: props.department.id } },
    { name: "Reports", linkName: "companies.departments.reports.index", param: { department: props.department.id } },
    { name: props.report.title, linkName: "companies.departments.reports.show", param: { department: props.department.id, report: props.report.id } },
]);

const isFormSubmitted = (formId) => {
    return props.submittedForms && props.submittedForms.includes(formId);
};

const deleteReport = () => {
    if (confirm("Are you sure you want to delete this report?")) {
        router.delete(route("companies.departments.reports.destroy", { department: props.department.id, report: props.report.id }));
    }
};

const getStatusColor = (status) => {
    const colors = {
        draft: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        submitted: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        reviewed: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        approved: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        rejected: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    };
    return colors[status] || colors.draft;
};

const getGeneralStatusColor = (status) => {
    const colors = {
        complete: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        pending: 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200',
    };
    return colors[status] || colors.pending;
};

const fileInputRef = ref(null);
const fileForm = useForm({ file: null });

const isDragOver = ref(false);
const dropUploading = ref(false);
const dropError = ref("");

const filesStoreUrl = () =>
    route("companies.departments.reports.files.store", {
        department: props.department.id,
        report: props.report.id,
    });

const formatFileSize = (bytes) => {
    if (bytes == null || bytes === 0) return "—";
    const k = 1024;
    const units = ["B", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return `${parseFloat((bytes / Math.pow(k, i)).toFixed(1))} ${units[i]}`;
};

const onFileSelected = (e) => {
    const files = e.target.files;
    if (!files?.length) {
        fileForm.file = null;
        return;
    }
    if (files.length === 1) {
        fileForm.file = files[0];
        return;
    }
    uploadDroppedFiles(files);
    e.target.value = "";
    fileForm.file = null;
};

const submitFile = () => {
    if (!fileForm.file) return;
    dropError.value = "";
    fileForm.post(filesStoreUrl(), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            fileForm.reset("file");
            if (fileInputRef.value) fileInputRef.value.value = "";
        },
    });
};

const uploadDroppedFiles = (rawFileList) => {
    const list = Array.from(rawFileList ?? []).filter((f) => f && f.name);
    if (!list.length) return;
    dropError.value = "";
    if (list.length > MAX_DROP_FILES) {
        dropError.value = `You can upload at most ${MAX_DROP_FILES} files at once.`;
        return;
    }
    const fd = new FormData();
    list.forEach((f) => fd.append("files[]", f));
    dropUploading.value = true;
    router.post(filesStoreUrl(), fd, {
        forceFormData: true,
        preserveScroll: true,
        onFinish: () => {
            dropUploading.value = false;
        },
        onError: (errors) => {
            const first =
                errors.files ??
                errors.file ??
                errors["files.0"] ??
                Object.values(errors).find((v) => typeof v === "string");
            dropError.value = Array.isArray(first) ? first[0] : first || "Upload failed.";
        },
    });
};

const onDropZoneDragEnter = (e) => {
    e.preventDefault();
    e.stopPropagation();
    if (e.dataTransfer?.types?.includes("Files")) {
        isDragOver.value = true;
    }
};

const onDropZoneDragOver = (e) => {
    e.preventDefault();
    e.stopPropagation();
    if (e.dataTransfer) {
        e.dataTransfer.dropEffect = "copy";
    }
};

const onDropZoneDragLeave = (e) => {
    e.preventDefault();
    e.stopPropagation();
    const next = e.relatedTarget;
    if (next && e.currentTarget instanceof Element && e.currentTarget.contains(next)) {
        return;
    }
    isDragOver.value = false;
};

const onDropZoneDrop = (e) => {
    e.preventDefault();
    e.stopPropagation();
    isDragOver.value = false;
    if (dropUploading.value || fileForm.processing) return;
    const files = e.dataTransfer?.files;
    if (files?.length) {
        uploadDroppedFiles(files);
    }
};

const deleteFile = (reportFileId) => {
    if (!confirm("Remove this file from the report?")) return;
    router.delete(
        route("companies.departments.reports.files.destroy", {
            department: props.department.id,
            report: props.report.id,
            reportFile: reportFileId,
        }),
        { preserveScroll: true }
    );
};
</script>

<template>
    <Head title="Report Details" />

    <div class="p-6">
        <BaseDashboardHeader
            :navs="navs"
            :title="report.title"
            :showButton="false"
            :addSearchInput="false"
        />

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700 mt-6">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="space-y-3">
                        <div class="flex flex-wrap items-center gap-3">
                            <span :class="['px-3 py-1 text-sm font-medium rounded-md', getStatusColor(report.status)]">
                                {{ report.status.charAt(0).toUpperCase() + report.status.slice(1) }}
                            </span>
                            <span :class="['px-3 py-1 text-sm font-medium rounded-md', getGeneralStatusColor(generalReportStatus)]">
                                {{ generalReportStatus ? generalReportStatus.charAt(0).toUpperCase() + generalReportStatus.slice(1) : 'Pending' }}
                            </span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500 dark:text-gray-400 mb-0.5">Report Date</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ new Date(report.report_date).toLocaleDateString() }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400 mb-0.5">Created By</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ report.creator ? `${report.creator.fname} ${report.creator.lname}` : '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400 mb-0.5">Department</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ report.department?.name }}</p>
                            </div>
                        </div>
                        <p v-if="report.description" class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                            {{ report.description }}
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <Link
                            :href="route('companies.departments.reports.edit', { department: department.id, report: report.id })"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                        >
                            Edit
                        </Link>
                        <button
                            @click="deleteReport"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>

            <!-- Report documents (folder-style attachments) -->
            <div class="px-6 py-5 border-t border-gray-200 dark:border-gray-700">
                <div class="mb-4">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Documents &amp; files</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                        Upload supporting files for this report. They are stored under this report only.
                    </p>
                </div>

                <div
                    class="relative rounded-lg border-2 border-dashed transition-colors outline-none"
                    :class="[
                        isDragOver
                            ? 'border-blue-500 bg-blue-50/80 dark:bg-blue-950/30 dark:border-blue-400'
                            : 'border-gray-300 dark:border-gray-600 bg-gray-50/50 dark:bg-gray-900/20',
                    ]"
                    @dragenter="onDropZoneDragEnter"
                    @dragover="onDropZoneDragOver"
                    @dragleave="onDropZoneDragLeave"
                    @drop="onDropZoneDrop"
                >
                    <div class="px-6 py-10 text-center select-none">
                        <svg
                            class="mx-auto h-10 w-10 text-gray-400 dark:text-gray-500 mb-3"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                            />
                        </svg>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            Drag and drop files here
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Drops upload immediately · up to {{ MAX_DROP_FILES }} files · 25&nbsp;MB each
                        </p>
                    </div>
                    <div
                        v-if="dropUploading"
                        class="absolute inset-0 flex items-center justify-center rounded-lg bg-white/70 dark:bg-gray-900/70"
                    >
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Uploading…</span>
                    </div>
                </div>

                <div class="mt-4 flex flex-col sm:flex-row gap-3 sm:items-center">
                    <span class="text-sm text-gray-500 dark:text-gray-400 shrink-0">Or choose files:</span>
                    <div class="flex flex-col sm:flex-row gap-2 sm:items-center flex-1 min-w-0">
                        <input
                            ref="fileInputRef"
                            type="file"
                            multiple
                            class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border file:border-gray-300 file:text-sm file:font-medium file:bg-white file:text-gray-700 hover:file:bg-gray-50 dark:file:bg-gray-700 dark:file:text-gray-300 dark:file:border-gray-600 pointer-events-auto"
                            @change="onFileSelected"
                        />
                        <button
                            type="button"
                            :disabled="!fileForm.file || fileForm.processing || dropUploading"
                            class="inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-white bg-slate-700 border border-transparent rounded-md hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-slate-600 dark:hover:bg-slate-500 shrink-0"
                            @click="submitFile"
                        >
                            {{ fileForm.processing ? "Uploading…" : "Upload" }}
                        </button>
                    </div>
                </div>
                <p v-if="fileForm.errors.file" class="text-sm text-red-600 mt-2">{{ fileForm.errors.file }}</p>
                <p v-if="dropError" class="text-sm text-red-600 mt-2">{{ dropError }}</p>

                <div v-if="report.report_files && report.report_files.length > 0" class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden sm:table-cell">Size</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">Uploaded</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="rf in report.report_files" :key="rf.id">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ rf.original_name }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 hidden sm:table-cell">
                                    {{ formatFileSize(rf.size) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 hidden md:table-cell">
                                    <span v-if="rf.uploader">{{ rf.uploader.fname }} {{ rf.uploader.lname }}</span>
                                    <span v-else class="text-gray-400">—</span>
                                    <span class="text-gray-400"> · {{ new Date(rf.created_at).toLocaleString() }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-right whitespace-nowrap">
                                    <a
                                        :href="route('companies.departments.reports.files.download', { department: department.id, report: report.id, reportFile: rf.id })"
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium mr-3"
                                    >
                                        Download
                                    </a>
                                    <button
                                        type="button"
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 font-medium"
                                        @click="deleteFile(rf.id)"
                                    >
                                        Remove
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div
                    v-else
                    class="py-8 text-center text-sm text-gray-500 dark:text-gray-400 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg"
                >
                    No files uploaded yet.
                </div>
            </div>

            <!-- Attached Forms Section -->
            <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                <div v-if="report.forms && report.forms.length > 0">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Attached Forms</h3>
                        <div class="flex gap-2 text-sm">
                            <span class="text-gray-500 dark:text-gray-400">
                                {{ report.forms.filter(f => isFormSubmitted(f.id)).length }} of {{ report.forms.length }} completed
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div
                            v-for="form in report.forms"
                            :key="form.id"
                            class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700/30 hover:border-gray-300 dark:hover:border-gray-500 transition-colors"
                        >
                            <div class="flex items-start justify-between mb-3">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ form.name }}
                                </h4>
                                <span
                                    v-if="isFormSubmitted(form.id)"
                                    class="px-2 py-0.5 text-xs font-medium rounded bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200"
                                >
                                    Completed
                                </span>
                                <span
                                    v-else
                                    class="px-2 py-0.5 text-xs font-medium rounded bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200"
                                >
                                    Pending
                                </span>
                            </div>
                            <div class="flex gap-2">
                                <Link
                                    v-if="isFormSubmitted(form.id)"
                                    :href="route('companies.departments.reports.forms.show', { department: department.id, report: report.id, form: form.id })"
                                    class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                                >
                                    View Response
                                </Link>
                                <Link
                                    :href="route('companies.departments.reports.forms.create', { department: department.id, report: report.id, form: form.id })"
                                    :class="[
                                        'flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-md',
                                        isFormSubmitted(form.id)
                                            ? 'text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800 dark:hover:bg-blue-900/50'
                                            : 'text-white bg-blue-600 border border-transparent hover:bg-blue-700'
                                    ]"
                                >
                                    {{ isFormSubmitted(form.id) ? 'Update' : 'Fill Form' }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="py-12 text-center border border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                    <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400">No forms attached to this report</p>
                    <Link
                        :href="route('companies.departments.reports.edit', { department: department.id, report: report.id })"
                        class="mt-3 inline-block text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                    >
                        Attach forms from Edit
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

