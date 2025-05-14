import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'dorasia-bg-dark': '#141414',
                'dorasia-red': '#E50914',
                'dorasia-red-dark': '#B81D24',
                'dorasia-gray': '#808080',
                'dorasia-gray-dark': '#2D2D2D'
            },
            screens: {
                'xs': '375px',
                ...defaultTheme.screens,
            },
        },
    },

    plugins: [forms],
};
