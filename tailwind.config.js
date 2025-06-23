import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Primary brand colors - exact hex values from blade files
                primary: {
                    50: '#f3f6fa',   // Main light background
                    100: '#e8edf5',  // Hover background
                    200: '#DCE4ED',  // Secondary button hover
                    300: '#EBF0F6',  // Secondary button background
                    500: '#315A92',  // Primary action color
                    600: '#2d437c',  // Search bar background
                    700: '#244164',  // Icon color
                    800: '#223464',  // Primary dark navy
                },
            },
        },
    },

    plugins: [forms],
};
