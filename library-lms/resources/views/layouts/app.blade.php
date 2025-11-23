<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Manager | TALL Stack</title>
    
    <!-- Livewire Styles -->
    @livewireStyles

    <!-- CRITICAL: Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f3f4f6; 
        }
    </style>

</head>
<body class="antialiased">

    <!-- Global Navigation Bar -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo / App Name -->
                    <div class="flex-shrink-0 flex items-center">
                        <span class="text-xl font-bold text-indigo-700">LMS</span>
                    </div>
                    
                    <!-- Navigation Links -->
                    @auth
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('library.books') }}" 
                           class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out 
                           {{ request()->routeIs('library.books') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                            Inventory
                        </a>
                        <a href="{{ route('library.loans') }}" 
                           class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out 
                           {{ request()->routeIs('library.loans') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                            Loan Desk
                        </a>
                        <a href="{{ route('library.users') }}" 
                           class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out 
                           {{ request()->routeIs('library.users') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                            Members
                        </a>
                    </div>
                    @endauth
                </div>

                <!-- Right Side: Auth Status -->
                <div class="flex items-center">
                    @auth
                        <span class="text-sm font-semibold text-gray-700 mr-4 hidden sm:block">
                            Welcome, {{ Auth::user()->name }}
                        </span>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="rounded-md bg-indigo-500 px-3 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-600 transition duration-150">
                                Logout
                            </button>
                        </form>
                    @else
                        <!-- Unauthenticated state: Only show Login button -->
                        <a href="{{ route('login') }}" 
                           class="rounded-md bg-green-500 px-3 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-green-600 transition duration-150">
                            Login
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content Slot -->
    <main>
        {{ $slot }}
    </main>

    <!-- Livewire Scripts -->
    @livewireScripts
</body>
</html>