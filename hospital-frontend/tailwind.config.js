/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{js,jsx}'],
  theme: {
    extend: {
      colors: {
        sidebar: {
          DEFAULT: '#0f3d38',
          hover:   '#1a5c55',
          active:  '#1a5c55',
        },
        brand: {
          DEFAULT: '#2a9d8f',
          dark:    '#0f3d38',
          light:   '#52b788',
        },
        status: {
          consulting: '#52b788',
          waiting:    '#f4a261',
          checkin:    '#2a9d8f',
          scheduled:  '#94a3b8',
        },
      },
      fontFamily: {
        sans: ['DM Sans', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
