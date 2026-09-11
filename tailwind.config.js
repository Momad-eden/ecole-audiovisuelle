import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {

        extend: {

            colors: {

                primary: '#C15C03',

                secondary: '#310181',

                background: '#F8FAFC',

                border: '#E5E7EB',

                muted: '#6B7280',

            },

            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'Inter', ...defaultTheme.fontFamily.sans],
                serif: ['"Outfit"', '"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                heading: ['"Outfit"', '"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                display: ['"Outfit"', '"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                cinzel: ['Cinzel', ...defaultTheme.fontFamily.serif],
                cormorant: ['"Cormorant Garamond"', ...defaultTheme.fontFamily.serif],
            },

            boxShadow: {

                sidebar: '0 0 40px rgba(0,0,0,.15)',

                card: '0 10px 25px rgba(0,0,0,.05)',

            }

        },

    },

    plugins: [forms],

};