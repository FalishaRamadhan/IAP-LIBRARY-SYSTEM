/** @type {import('tailwindcss').Config} */
module.exports = {
  // CRITICAL: This content array tells Tailwind what files to scan for class names.
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./app/Livewire/**/*.php",
  ],
  theme: {
    extend: {
        fontFamily: {
            // Setting the default font family for the entire app
            sans: ['Inter', 'sans-serif'], 
        },
    },
  },
  plugins: [],
}