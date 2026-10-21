<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, useForm } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    sectors: { type: Array, default: () => [] },
    sectorId: { type: Number, default: null },
    departments: { type: Array, default: () => [] },
    libraryGroups: { type: Array, default: () => [] },
});

const editingId = ref(null);

const form = useForm({
    sector_id: props.sectorId || "",
    name: "",
    description: "",
    library_key: "",
});

const selectedSector = computed(() =>
    props.sectors.find((sector) => Number(sector.id) === Number(props.sectorId)) || null
);

const orphanLibraryKey = computed(() => {
    if (!form.library_key) {
        return false;
    }
    return !props.libraryGroups.some((group) => group.key === form.library_key);
});

watch(
    () => props.sectorId,
    () => {
        resetForm();
    }
);

function selectSector(id) {
    router.get(route("settings.index"), { sector_id: id }, { preserveScroll: true });
}

function resetForm() {
    editingId.value = null;
    form.sector_id = props.sectorId || "";
    form.name = "";
    form.description = "";
    form.library_key = "";
    form.clearErrors();
}

function startEdit(department) {
    editingId.value = department.id;
    form.sector_id = department.sector_id;
    form.name = department.name;
    form.description = department.description || "";
    form.library_key = department.library_key || "";
    form.clearErrors();
}

function submit() {
    form.sector_id = props.sectorId;
    const options = {
        preserveScroll: true,
        onSuccess: () => resetForm(),
    };

    if (editingId.value) {
        form.put(route("settings.default-departments.update", editingId.value), options);
        return;
    }

    form.post(route("settings.default-departments.store"), options);
}

function removeDepartment(department) {
    if (!confirm(`Remove "${department.name}" from the defaults for this sector?`)) {
        return;
    }

    router.delete(route("settings.default-departments.destroy", department.id), {
        preserveScroll: true,
        onSuccess: () => {
            if (editingId.value === department.id) {
                resetForm();
            }
        },
    });
}

function moveDepartment(department, direction) {
    router.patch(
        route("settings.default-departments.move", department.id),
        { direction },
        { preserveScroll: true }
    );
}
</script>

<template>
    <Head title="Settings" />

    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Settings</h1>
            <p class="mt-2 max-w-3xl text-sm text-gray-600 dark:text-gray-400">
                Manage the default departments for each sector. When a company uses
                <span class="font-medium">Seed Default Departments</span>, it receives this list.
                A department that already exists with the same name is kept, and its forms are updated to match the form group.
                Renaming a default creates a new department the next time a company seeds.
            </p>
        </div>

        <div
            v-if="$page.props.flash?.success"
            class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300"
        >
            {{ $page.props.flash.success }}
        </div>
        <div
            v-if="$page.props.flash?.error"
            class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300"
        >
            {{ $page.props.flash.error }}
        </div>

        <div v-if="sectors.length === 0" class="rounded-lg border border-gray-200 bg-white p-6 text-sm text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
            Add a sector before configuring default departments.
        </div>

        <div v-else class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <aside class="lg:col-span-4 xl:col-span-3">
                <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800">
                    <p class="px-2 pb-2 text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                        Sectors
                    </p>
                    <ul class="space-y-1">
                        <li v-for="sector in sectors" :key="sector.id">
                            <button
                                type="button"
                                class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-sm transition-colors"
                                :class="Number(sector.id) === Number(sectorId)
                                    ? 'bg-blue-600 text-white'
                                    : 'text-gray-800 hover:bg-gray-100 dark:text-gray-100 dark:hover:bg-gray-700'"
                                @click="selectSector(sector.id)"
                            >
                                <span>
                                    <span class="block font-medium">{{ sector.name }}</span>
                                    <span
                                        class="block text-xs"
                                        :class="Number(sector.id) === Number(sectorId) ? 'text-blue-100' : 'text-gray-500 dark:text-gray-400'"
                                    >
                                        {{ sector.industry || "No industry" }}
                                    </span>
                                </span>
                                <span
                                    class="ml-3 inline-flex min-w-6 justify-center rounded-full px-2 py-0.5 text-xs font-semibold"
                                    :class="Number(sector.id) === Number(sectorId)
                                        ? 'bg-blue-500 text-white'
                                        : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'"
                                >
                                    {{ sector.departments_count }}
                                </span>
                            </button>
                        </li>
                    </ul>
                </div>
            </aside>

            <section class="lg:col-span-8 xl:col-span-9 space-y-6">
                <form class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800" @submit.prevent="submit">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ editingId ? "Edit department" : "Add department" }}
                        <span v-if="selectedSector" class="font-normal text-gray-500 dark:text-gray-400">— {{ selectedSector.name }}</span>
                    </h2>

                    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="default-name" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Name
                            </label>
                            <input
                                id="default-name"
                                v-model="form.name"
                                type="text"
                                required
                                maxlength="255"
                                placeholder="Safety"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>
                        <div>
                            <label for="default-library" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Form group
                            </label>
                            <select
                                id="default-library"
                                v-model="form.library_key"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            >
                                <option value="">No form group</option>
                                <option v-for="group in libraryGroups" :key="group.key" :value="group.key">
                                    {{ group.label }} ({{ group.templates_count }} {{ group.templates_count === 1 ? "form" : "forms" }})
                                </option>
                                <option v-if="orphanLibraryKey" :value="form.library_key">
                                    {{ form.library_key }}
                                </option>
                            </select>
                            <p v-if="form.errors.library_key" class="mt-1 text-sm text-red-600">{{ form.errors.library_key }}</p>
                            <p v-else-if="libraryGroups.length === 0" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                This sector has no form templates yet. You can still add the department and attach a form group later.
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <label for="default-description" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Description
                            </label>
                            <textarea
                                id="default-description"
                                v-model="form.description"
                                rows="3"
                                maxlength="2000"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            />
                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center gap-3">
                        <button
                            type="submit"
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-60"
                            :disabled="form.processing"
                        >
                            {{ editingId ? "Save changes" : "Add department" }}
                        </button>
                        <button
                            v-if="editingId"
                            type="button"
                            class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                            @click="resetForm"
                        >
                            Cancel
                        </button>
                    </div>
                </form>

                <div class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                    <div v-if="departments.length === 0" class="p-6 text-sm text-gray-600 dark:text-gray-300">
                        No default departments for this sector yet.
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Order</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Description</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Form group</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="(department, index) in departments" :key="department.id">
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">
                                        <div class="flex items-center gap-1">
                                            <button
                                                type="button"
                                                class="rounded px-2 py-1 hover:bg-gray-100 disabled:opacity-30 dark:hover:bg-gray-700"
                                                :disabled="index === 0"
                                                aria-label="Move up"
                                                @click="moveDepartment(department, 'up')"
                                            >
                                                ↑
                                            </button>
                                            <button
                                                type="button"
                                                class="rounded px-2 py-1 hover:bg-gray-100 disabled:opacity-30 dark:hover:bg-gray-700"
                                                :disabled="index === departments.length - 1"
                                                aria-label="Move down"
                                                @click="moveDepartment(department, 'down')"
                                            >
                                                ↓
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ department.name }}</td>
                                    <td class="max-w-xs px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ department.description || "—" }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ department.library_label }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                        <button
                                            type="button"
                                            class="font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400"
                                            @click="startEdit(department)"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            type="button"
                                            class="ml-3 font-medium text-red-600 hover:text-red-800 dark:text-red-400"
                                            @click="removeDepartment(department)"
                                        >
                                            Remove
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
