<script setup>
import { computed, ref } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import { onMounted } from "vue";
import { initFlowbite } from "flowbite";
import { useDark, useToggle } from "@vueuse/core";

const isDark = useDark();
const toggleDark = useToggle(isDark);
const page = usePage();
const employee = computed(() => page.props.authEmployee?.employee);
const impersonating = computed(() => page.props.impersonating);
const mobileOpen = ref(false);

const leaveImpersonation = () => {
    router.post(route("companies.leave-impersonation"));
};

onMounted(() => {
    initFlowbite();
});

const logout = () => {
    router.post(route("companies.logout"));
};

const navLinks = [
    { label: "Dashboard",       href: route("companies.dashboard"),          match: "dashboard" },
    { label: "Reports",         href: route("companies.reports.index"),      match: "reports" },
    { label: "Risk Register",   href: route("companies.risks.index"),        match: "risks" },
    { label: "Audit Tracker",   href: route("companies.audits.index"),       match: "audits" },
    { label: "Forms",           href: route("companies.forms.index"),        match: "forms" },
    { label: "CAPA",            href: route("companies.capa.index"),         match: "capa" },
    { label: "Departments",     href: route("companies.departments.index"),  match: "departments" },
    { label: "Settings",        href: route("companies.settings"),           match: "settings" },
    { label: "Library",         href: "/companies/library",                  match: "library" },
    { label: "Help",            href: route("companies.help"),               match: "help" },
];

const isActive = (match) => {
    const url = page.url || "";
    if (match === "dashboard") return url === "/companies/dashboard" || url === "/companies/dashboard/";
    return url.includes(`/companies/${match}`);
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

        <nav class="sticky top-0 z-50 w-full bg-white/95 backdrop-blur border-b border-gray-200 dark:bg-gray-800/95 dark:border-gray-700">
            <div class="mx-auto px-3 xl:px-5">
                <div class="h-12 flex items-center justify-between gap-2">
                    <!-- Brand + user -->
                    <div class="flex items-center gap-2 shrink-0">
                        <img src="/logos/lo.png" class="h-7 w-auto" alt="QSMCore Logo" />
                        <span class="text-xs font-bold text-gray-900 dark:text-white leading-none">QSMCore</span>
                        <span class="hidden sm:inline text-[10px] text-gray-400 dark:text-gray-500">|</span>
                        <span class="hidden sm:inline text-[11px] font-semibold text-gray-600 dark:text-gray-300 truncate max-w-[120px]">
                            {{ employee?.fname }} {{ employee?.lname }}
                        </span>
                    </div>

                    <!-- Nav links -->
                    <div class="hidden lg:flex items-center gap-0.5">
                        <Link
                            v-for="nav in navLinks" :key="nav.label"
                            :href="nav.href"
                            class="px-2.5 py-1.5 rounded text-[13px] font-semibold whitespace-nowrap transition-colors"
                            :class="isActive(nav.match)
                                ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
                                : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white'"
                        >
                            {{ nav.label }}
                        </Link>
                    </div>

                    <!-- Right: theme toggle + user dropdown -->
                    <div class="flex items-center gap-1 shrink-0">
                        <button
                            @click="toggleDark()"
                            type="button"
                            class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded p-1.5"
                            aria-label="Toggle theme"
                        >
                            <svg v-if="isDark" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"/></svg>
                            <svg v-else class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/></svg>
                        </button>

                        <button
                            type="button"
                            class="flex items-center gap-1.5 rounded-full px-1.5 py-1 hover:bg-gray-100 dark:hover:bg-gray-700 focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-600"
                            aria-expanded="false"
                            data-dropdown-toggle="dropdown-user"
                        >
                            <img class="w-7 h-7 rounded-full" src="https://flowbite.com/docs/images/people/profile-picture-5.jpg" alt="user photo" />
                            <svg class="w-3 h-3 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        </button>

                        <!-- Hamburger for mobile -->
                        <button
                            type="button"
                            class="lg:hidden p-1.5 rounded text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700"
                            @click="mobileOpen = !mobileOpen"
                            aria-label="Open menu"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>

                        <!-- User dropdown -->
                        <div
                            class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow-lg border border-gray-200 dark:bg-gray-700 dark:divide-gray-600 dark:border-gray-600"
                            id="dropdown-user"
                        >
                            <div class="px-4 py-3" role="none">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ employee?.fname }} {{ employee?.lname }}
                                </p>
                                <p class="text-xs text-gray-500 truncate dark:text-gray-400">
                                    {{ employee?.email }}
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ employee?.company?.name }}
                                </p>
                            </div>
                            <ul class="py-1" role="none">
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

            <!-- Mobile nav drawer -->
            <div v-if="mobileOpen" class="lg:hidden border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 pb-3 pt-2">
                <Link
                    v-for="nav in navLinks" :key="'m-' + nav.label"
                    :href="nav.href"
                    class="block px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                    :class="isActive(nav.match)
                        ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
                        : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700'"
                    @click="mobileOpen = false"
                >
                    {{ nav.label }}
                </Link>
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

