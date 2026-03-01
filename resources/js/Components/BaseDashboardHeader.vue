<template>
  <div
    class="p-5 bg-white mb-5 border border-gray-200 rounded shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800"
  >
    <nav class="flex mb-4" aria-label="Breadcrumb">
      <ol class="inline-flex items-center">
        <svg
          class="w-3 h-3 me-2.5"
          aria-hidden="true"
          xmlns="http://www.w3.org/2000/svg"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path
            d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"
          />
        </svg>
        <li v-for="(nav, index) in navs" :key="index">
          <div class="flex items-center">
            <Link
              :href="nav.param ? route(nav.linkName, nav.param) : route(nav.linkName)"
              class="text-sm capitalize font-medium mr-2 text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white"
            >
              {{ nav.name }}</Link
            >
            <svg
              v-if="index < navs.length - 1"
              class="w-3 h-3 mr-2 text-gray-400 mx-1 rtl:rotate-180"
              aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 6 10"
            >
              <path
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="m1 9 4-4-4-4"
              />
            </svg>
          </div>
        </li>
      </ol>
    </nav>

    <div class="flex justify-between items-center">
      <h2
        class="mb-4 text-3xl capitalize font-extrabold leading-none tracking-tight text-gray-900 md:text-4xl dark:text-white"
      >
        {{ title }}
      </h2>

      <component
        :is="IsLink ? 'Link' : 'a'"
        :href="navLinkValue ? route(navLinkName, navLinkValue) : route(navLinkName)"
        :data="data"
        v-if="showButton"
        class="text-white bg-blue-600 hover:bg-blue-700 duration-200 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
      >
        {{ NavLinkText }}
      </component>
    </div>

    <form class="max-w-md">
      <label
        for="default-search"
        class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white"
        >Search</label
      >
      <div class="relative" v-if="addSearchInput">
        <div
          class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none"
        >
          <svg
            class="w-4 h-4 text-gray-500 dark:text-gray-400"
            aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 20 20"
          >
            <path
              stroke="currentColor"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"
            />
          </svg>
        </div>
        <input
          ref="searchInput"
          type="search"
          id="default-search"
          class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
          placeholder="Search ...."
          v-model="search"
        />
      </div>
    </form>
  </div>
</template>

<script setup>
import throttle from "lodash/throttle";
import { ref, watch } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
  navs: [Object],
  data: Object,
  title: String,
  navLinkName: String,
  navLinkValue: String,
  NavLinkText: String,
  filters: Object,
  IsLink: {
    type: Boolean,
    default: true,
  },
  addSearchInput: {
    type: Boolean,
    default: false,
  },
  showButton: {
    type: Boolean,
    default: true,
  },
});
const emit = defineEmits({
  search: String,
});
const searchInput = ref(null);

let search = ref(props.filters?.search);
watch(
  search,
  throttle(function (value) {
    emit("search", searchInput.value.value);
  }, 800)
);
</script>