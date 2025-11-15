/** @type {import('tailwindcss').Config} */
module.exports = {
  prefix: 'nin-',
  content: [
    './assets/src/**/*.{js,jsx,ts,tsx}',
    './includes/**/*.php'
  ],
  theme: {
    extend: {
      colors: {
        'wp-blue': '#2271b1',
        'wp-border': '#c3c4c7',
        'wp-text': '#1d2327',
        'wp-text-light': '#50575e',
      }
    },
  },
  plugins: [],
  corePlugins: {
    preflight: false,
  }
};
