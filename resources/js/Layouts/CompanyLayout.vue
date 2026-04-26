<script setup>
import { computed } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import { onMounted } from "vue";
import { initFlowbite } from "flowbite";
import { useDark, useToggle } from "@vueuse/core";

const isDark = useDark();
const toggleDark = useToggle(isDark);
const page = usePage();
const employee = computed(() => page.props.authEmployee?.employee);
const impersonating = computed(() => page.props.impersonating);

const leaveImpersonation = () => {
    router.post(route("companies.leave-impersonation"));
};

onMounted(() => {
    initFlowbite();
});

const logout = () => {
    router.post(route("companies.logout"));
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 dark:bg-gray-900">
        <!-- Admin Impersonation Banner -->
        <div
            v-if="impersonating"
            class="w-full bg-amber-500 text-white text-sm px-4 py-2 flex items-center justify-between"
        >
            <span>
                <i class="fa-solid fa-eye mr-2"></i>
                You are viewing as <strong>{{ employee?.company?.name }}</strong> ({{ employee?.fname }} {{ employee?.lname }})
            </span>
            <button
                @click="leaveImpersonation"
                class="ml-4 px-3 py-1 bg-white text-amber-600 font-semibold rounded hover:bg-amber-50 transition-colors text-xs"
            >
                <i class="fa-solid fa-arrow-left mr-1"></i>Return to Admin
            </button>
        </div>

        <nav class="sticky top-0 z-50 w-full bg-white/90 backdrop-blur border-b border-gray-200 dark:bg-gray-800/90 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="h-16 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <img src="/logos/lo.png" class="h-10 w-auto" alt="QSMCore Logo" />
                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">
                                QSMCore
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ employee?.company?.name }}
                            </div>
                        </div>
                    </div>

                    <div class="hidden md:flex items-center gap-1">
                        <Link :href="route('companies.dashboard')" class="px-3 py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-200 dark:hover:bg-gray-700">
                            Dashboard
                        </Link>
                        <Link :href="route('companies.reports.index')" class="px-3 py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-200 dark:hover:bg-gray-700">
                            Reports
                        </Link>
                        <Link :href="route('companies.risks.index')" class="px-3 py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-200 dark:hover:bg-gray-700">
                            Risk Register
                        </Link>
                        <Link :href="route('companies.audits.index')" class="px-3 py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-200 dark:hover:bg-gray-700">
                            Audit Tracker
                        </Link>
                        <Link :href="route('companies.forms.index')" class="px-3 py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-200 dark:hover:bg-gray-700">
                            Forms
                        </Link>
                        <Link :href="route('companies.capa.index')" class="px-3 py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-200 dark:hover:bg-gray-700">
                            CAPA Management
                        </Link>
                        <Link :href="route('companies.departments.index')" class="px-3 py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-200 dark:hover:bg-gray-700">
                            Departments
                        </Link>
                        <Link :href="route('companies.settings')" class="px-3 py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-200 dark:hover:bg-gray-700">
                            Settings
                        </Link>
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            @click="toggleDark()"
                            type="button"
                            class="text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5"
                            aria-label="Toggle theme"
                        >
                            <i class="fa-solid fa-sun fa-lg"></i>
                        </button>

                        <button
                            type="button"
                            class="flex items-center gap-2 text-sm bg-white dark:bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 px-2 py-1"
                            aria-expanded="false"
                            data-dropdown-toggle="dropdown-user"
                        >
                            <img
                                class="w-8 h-8 rounded-full"
                                src="https://flowbite.com/docs/images/people/profile-picture-5.jpg"
                                alt="user photo"
                            />
                            <span class="hidden sm:block font-semibold text-gray-700 dark:text-gray-200">
                                {{ employee?.fname }}
                            </span>
                        </button>

                        <div
                            class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow dark:bg-gray-700 dark:divide-gray-600"
                            id="dropdown-user"
                        >
                            <div class="px-4 py-3" role="none">
                                <p class="text-sm text-gray-900 dark:text-white" role="none">
                                    {{ employee?.fname }} {{ employee?.lname }}
                                </p>
                                <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                                    {{ employee?.email }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400" role="none">
                                    {{ employee?.company?.name }}
                                </p>
                            </div>
                            <ul class="py-1" role="none">
                                <li class="md:hidden">
                                    <Link :href="route('companies.dashboard')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600" role="menuitem">
                                        Dashboard
                                    </Link>
                                </li>
                                <li class="md:hidden">
                                    <Link :href="route('companies.reports.index')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600" role="menuitem">
                                        Reports
                                    </Link>
                                </li>
                                <li class="md:hidden">
                                    <Link :href="route('companies.risks.index')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600" role="menuitem">
                                        Risk Register
                                    </Link>
                                </li>
                                <li class="md:hidden">
                                    <Link :href="route('companies.audits.index')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600" role="menuitem">
                                        Audit Tracker
                                    </Link>
                                </li>
                                <li class="md:hidden">
                                    <Link :href="route('companies.forms.index')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600" role="menuitem">
                                        Forms
                                    </Link>
                                </li>
                                <li class="md:hidden">
                                    <Link :href="route('companies.capa.index')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600" role="menuitem">
                                        CAPA Management
                                    </Link>
                                </li>
                                <li class="md:hidden">
                                    <Link :href="route('companies.departments.index')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600" role="menuitem">
                                        Departments
                                    </Link>
                                </li>
                                <li class="md:hidden">
                                    <Link :href="route('companies.settings')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600" role="menuitem">
                                        Settings
                                    </Link>
                                </li>
                                <li>
                                    <Link :href="route('companies.employees.index')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600" role="menuitem">
                                        Employees
                                    </Link>
                                </li>
                                <li>
                                    <button
                                        @click="logout"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600"
                                        role="menuitem"
                                    >
                                        Sign out
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto">
            <!-- Flash messages -->
            <div v-if="$page.props.flash?.error" class="mx-6 mt-4 p-4 bg-red-50 border border-red-300 text-red-800 rounded-lg text-sm dark:bg-red-900/30 dark:border-red-700 dark:text-red-300">
                <i class="fa-solid fa-triangle-exclamation mr-2"></i>{{ $page.props.flash.error }}
            </div>
            <div v-if="$page.props.flash?.success" class="mx-6 mt-4 p-4 bg-green-50 border border-green-300 text-green-800 rounded-lg text-sm dark:bg-green-900/30 dark:border-green-700 dark:text-green-300">
                <i class="fa-solid fa-circle-check mr-2"></i>{{ $page.props.flash.success }}
            </div>
            <slot />
        </main>
    </div>
</template>

