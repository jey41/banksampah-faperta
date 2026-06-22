import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.jsx',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: '#154212',
                secondary: '#3e6a00',
                tertiary: '#303c34',
                background: '#f8f9ff',
                surface: '#f8f9ff',
                'primary-container': '#2d5a27',
                'secondary-container': '#b9f474',
                'tertiary-container': '#47534a',
                'on-primary': '#ffffff',
                'on-secondary': '#ffffff',
                'on-tertiary': '#ffffff',
                'on-surface': '#121c2a',
                'on-background': '#121c2a',
                'on-primary-container': '#9dd090',
                'on-secondary-container': '#437000',
                'on-tertiary-container': '#b9c6bb',
                outline: '#72796e',
                'outline-variant': '#c2c9bb',
                error: '#ba1a1a',
                'error-container': '#ffdad6',
                'on-error': '#ffffff',
                'on-error-container': '#93000a',
                'surface-container-low': '#eff4ff',
                'surface-container': '#e6eeff',
                'surface-container-high': '#dee9fc',
                'surface-container-highest': '#d9e3f6',
                'surface-bright': '#f8f9ff',
                'surface-dim': '#d0dbed',
                'surface-tint': '#3b6934',
                'inverse-surface': '#27313f',
                'inverse-on-surface': '#eaf1ff',
                'inverse-primary': '#a1d494',
            },
            spacing: {
                xs: '4px',
                sm: '8px',
                md: '16px',
                lg: '24px',
                xl: '32px',
                '2xl': '48px',
                '3xl': '64px',
                gutter: '24px',
                'container-max': '1280px',
            },
            borderRadius: {
                '3xl': '1.5rem',
            },
            keyframes: {
                'infinite-scroll': {
                    from: { transform: 'translateX(0)' },
                    to: { transform: 'translateX(-100%)' },
                }
            },
            animation: {
                'infinite-scroll': 'infinite-scroll 25s linear infinite',
            }
        },
    },

    plugins: [forms],
};
