<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Manager</title>
    <!-- Livewire Styles -->
    @livewireStyles
    <!-- Vite/Tailwind Assets (assumes default Laravel/Breeze setup) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 antialiased">
    <!-- Main Content Slot -->
    {{ $slot }}

    <!-- Alpine.js is assumed to be included in resources/js/app.js -->
    <!-- Livewire Scripts -->
    @livewireScripts
</body>
</html>