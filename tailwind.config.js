import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Manrope', ...defaultTheme.fontFamily.sans],
                display: ['Cormorant Garamond', 'Georgia', 'serif'],
            },
            colors: {
                coffee: {
                    DEFAULT: '#2c1810',
                    deep: '#1a0f0a',
                    elevated: '#3c2820',
                    gold: '#e6c87c',
                    'gold-bright': '#f0d08a',
                    accent: '#D4A742',
                    muted: 'rgba(255,255,255,0.6)',
                },
            },
        },
    },

    plugins: [forms],
};
