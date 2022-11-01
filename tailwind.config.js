const colors = require('tailwindcss/colors')

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.vue',
  ],

  safelist: [
    // some safe classes here
  ],

  theme: {
    extend: {
      colors: {
        primary: colors.blue,
        success: colors.green,
        error: colors.red,
      },
    },
  },

  plugins: [require('@tailwindcss/forms')],
}
