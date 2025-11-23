import './bootstrap'; // Standard Laravel setup

// If you want Alpine.js (used by Livewire) to run globally:
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Or simply ensure it imports your CSS (which is handled by Vite directly in the HTML)
// import '../css/app.css'; // This line is sometimes included, but usually not necessary with @vite in Blade