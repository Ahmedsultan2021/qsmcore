import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
const colors = require("tailwindcss/colors");

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        "./node_modules/flowbite/**/*.js",
        // "./node_modules/vue-tailwind-datepicker/**/*.js",
        // "./index.html",
        // "./src/**/*.{vue,js,ts,jsx,tsx}",
        "./index.html",
        "./src/**/*.{vue,js,ts,jsx,tsx}",
        "./node_modules/vue-tailwind-datepicker/**/*.js",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    navy: '#0B2B45',
                    blue: '#1F5E88',
                    sky: '#2F8BC9',
                    bg: '#F6F8FB',
                    border: '#E5EAF0',
                    text: '#0F172A',
                    muted: '#475569',
                },
                "vtd-primary": colors.sky, // Light mode Datepicker color
                "vtd-secondary": colors.gray, // Dark mode Datepicker color
              },
        },
    },

    plugins: [
        forms,
        require('flowbite/plugin'),
        require("@tailwindcss/forms")

    ],
};
