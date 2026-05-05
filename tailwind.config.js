import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './node_modules/flowbite/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Palet warna BBPMP
                'primary': {
                    50: '#e6eef5',
                    100: '#ccdcea',
                    200: '#99b9d6',
                    300: '#6696c1',
                    400: '#3373ad',
                    500: '#2d5a87', // Biru medium
                    600: '#1e3a5f', // Biru tua (primary)
                    700: '#1a3252',
                    800: '#152946',
                    900: '#0f1f33',
                    950: '#0a1522',
                },
                'secondary': {
                    50: '#f0f9ff',
                    100: '#e0f2fe',
                    200: '#bae6fd',
                    300: '#7dd3fc',
                    400: '#38bdf8',
                    500: '#0ea5e9',
                    600: '#0284c7',
                    700: '#0369a1',
                    800: '#075985',
                    900: '#0c4a6e',
                },
            },
        },
    },

    plugins: [forms],
};
