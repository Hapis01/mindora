/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        // Satu aksen lilac untuk seluruh situs: purple dan cyan dipetakan ke skala yang sama
        purple: {
          50: '#f4f2ff', 100: '#e9e6ff', 200: '#d6d0ff', 300: '#c3baff', 400: '#a99bff',
          500: '#8e7df0', 600: '#7565d6', 700: '#5a4cb0', 800: '#3b3580', 900: '#1d1859', 950: '#100e36',
        },
        cyan: {
          50: '#f4f2ff', 100: '#e9e6ff', 200: '#d6d0ff', 300: '#c3baff', 400: '#a99bff',
          500: '#8e7df0', 600: '#7565d6', 700: '#5a4cb0', 800: '#3b3580', 900: '#1d1859', 950: '#100e36',
        },
        // Mindora Brand Palette — extracted from logo
        brand: {
          50:  '#f0eeff',
          100: '#e3deff',
          200: '#c9c0ff',
          300: '#a894ff',
          400: '#8b6bfa',
          500: '#7c3aed',
          600: '#6d28d9',
          700: '#5b21b6',
          800: '#4c1d95',
          900: '#2d1b69',
          950: '#1a1048',
        },
        navy: {
          50:  '#eef2ff',
          100: '#e0e7ff',
          200: '#c7d2fe',
          300: '#a5b4fc',
          400: '#818cf8',
          500: '#4f46e5',
          600: '#3730a3',
          700: '#2d2480',
          800: '#1e1b5e',
          900: '#14124a',
          950: '#0d0b33',
        },
        accent: {
          50:  '#f0f9ff',
          100: '#e0f2fe',
          200: '#bae6fd',
          300: '#7dd3fc',
          400: '#38bdf8',
          500: '#0ea5e9',
        }
      },
      borderRadius: {
        xl: '14px',
        '2xl': '24px',
        '3xl': '24px',
      },
      fontFamily: {
        sans: ['Plus Jakarta Sans Variable', 'Plus Jakarta Sans', 'system-ui', 'sans-serif'],
        display: ['Bricolage Grotesque Variable', 'Plus Jakarta Sans Variable', 'system-ui', 'sans-serif'],
        mono: ['Geist Mono Variable', 'ui-monospace', 'monospace'],
      },
      animation: {
        'float': 'float 6s ease-in-out infinite',
        'glow-pulse': 'glow-pulse 3s ease-in-out infinite',
        'slide-up': 'slide-up 0.6s ease-out',
      },
      keyframes: {
        float: {
          '0%, 100%': { transform: 'translateY(0px)' },
          '50%': { transform: 'translateY(-8px)' },
        },
        'glow-pulse': {
          '0%, 100%': { opacity: '0.6' },
          '50%': { opacity: '1' },
        },
        'slide-up': {
          '0%': { opacity: '0', transform: 'translateY(20px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
      },
    },
  },
  plugins: [],
}
