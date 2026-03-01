<template>
    <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">

    <nav class="bg-white flex items-center flex-column flex-wrap md:flex-row justify-between p-4" aria-label="Table navigation">
        <span
            class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-4 md:mb-0 block w-full md:inline md:w-auto">Showing
            <span class="font-semibold text-gray-900 dark:text-white">{{ items?.from }}-{{ items?.to }}</span> of <span
                class="font-semibold text-gray-900 dark:text-white">{{ items?.total }}</span></span>
        <ul class="flex flex-wrap justify-center -space-x-px rtl:space-x-reverse text-sm h-8">
            <li v-for="(link, index) in items?.links" :key="index">
                <Link :href="link.url" preserve-scroll :data="{'patch_id': patch?.slug}"
                    class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                    > <p v-html="link.label" ></p> </Link>
            </li>
        </ul>
    </nav>
</template>

<script setup>
defineProps({items:Object,patch:Object})
</script>

<style lang="scss" scoped></style>