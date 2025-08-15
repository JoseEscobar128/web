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
                sans: ['Roboto', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'mostaza': '#D78D16',
                'login-azul': '#0F38A1',
                'input-rect': '#DDDDDD',
                // --- Colores adicionales de tu paleta para futuro uso ---
                'ocre': '#7b510f',
                'terracota': '#922f06',
                'cafe-rustico': '#a34f20',
                'azul-profundo': '#064e68',
            },
        },
    },

    plugins: [forms],
};
