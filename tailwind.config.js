/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
  ],
  darkMode: 'class', // 
  theme: {
    extend: {
      colors: {
        'f1-red': '#dc2626',
        'f1-dark': '#0d0d0d',
      },
      fontFamily: {
        'digital': ['Orbitron', 'sans-serif'],
      },
    },
  },
  plugins: [],
}