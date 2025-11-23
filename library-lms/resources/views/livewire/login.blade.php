<div class="flex items-center justify-center min-h-screen bg-gray-100">
<script src="https://www.google.com/search?q=https://cdn.tailwindcss.com"></script>
<style>
@import url('https://www.google.com/search?q=https://fonts.googleapis.com/css2%3Ffamily%3DInter:wght%40400%3B600%3B700%26display%3Dswap');
body { font-family: 'Inter', sans-serif; }
</style>

<div class="w-full max-w-md bg-white p-8 rounded-xl shadow-2xl ring-1 ring-gray-100">
    <h2 class="text-3xl font-bold text-center text-indigo-700 mb-6">LMS Login</h2>
    <p class="text-center text-gray-500 mb-8">Sign in to manage the library.</p>

    <form wire:submit.prevent="login">
        <!-- Email Input -->
        <div class="mb-5">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <input wire:model.defer="email" type="email" id="email" 
                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 text-sm border transition duration-150"
                   placeholder="you@library.com">
            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Password Input -->
        <div class="mb-6">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input wire:model.defer="password" type="password" id="password" 
                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 text-sm border transition duration-150"
                   placeholder="••••••••">
            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        
        <!-- Remember Me Checkbox -->
        <div class="flex items-center mb-6">
            <input wire:model.defer="remember" id="remember" type="checkbox" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
            <label for="remember" class="ml-2 block text-sm text-gray-900">Remember Me</label>
        </div>


        <!-- Submit Button -->
        <button type="submit" 
                class="w-full rounded-lg bg-indigo-600 py-3 text-sm font-semibold text-white shadow-lg hover:bg-indigo-700 transition duration-300 transform hover:scale-[1.01] focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:ring-opacity-50">
            <span wire:loading.remove wire:target="login">Login</span>
            <span wire:loading wire:target="login">Authenticating...</span>
        </button>
    </form>
</div>


</div>