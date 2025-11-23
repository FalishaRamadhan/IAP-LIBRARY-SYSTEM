<div class="p-4 sm:p-6 md:p-10 bg-gray-100 min-h-screen">
<!-- FIX: Corrected CDN and Font imports -->
<script src="https://www.google.com/search?q=https://cdn.tailwindcss.com"></script>
<style>
@import url('https://www.google.com/search?q=https://fonts.googleapis.com/css2%3Ffamily%3DInter:wght%40400%3B600%3B700%26display%3Dswap');
body { font-family: 'Inter', sans-serif; }
.responsive-table::-webkit-scrollbar { height: 8px; }
.responsive-table::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
</style>

<div class="max-w-7xl mx-auto">
    <header class="text-center md:text-left mb-8 pb-4 border-b-2 border-indigo-200">
        <h1 class="text-4xl font-extrabold text-gray-900 leading-tight">Member <span class="text-indigo-600">Registry</span></h1>
        <p class="text-gray-500 mt-1">Manage library members and their access details.</p>
    </header>

    <!-- Session Message Display (Floating) -->
    @if (session()->has('success') || session()->has('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:leave="transition ease-in duration-200"
            class="fixed bottom-4 right-4 z-50 p-4 rounded-xl shadow-2xl transition duration-300
            {{ session()->has('success') ? 'bg-indigo-600' : 'bg-red-600' }} text-white font-semibold"
            role="alert">
            <span class="block sm:inline">{{ session('success') ?? session('error') }}</span>
        </div>
    @endif
    
    <!-- 1. User Management Form (Only visible to Admin) -->
    @auth
        @if (Auth::user()->isAdmin())
        <div class="bg-white p-6 md:p-8 rounded-2xl shadow-xl ring-1 ring-gray-100 mb-10">
            <h2 class="text-2xl font-bold text-indigo-700 mb-6 border-b pb-2">
                {{ $editingUserId ? 'Edit Member Record' : 'Register New Member' }}
            </h2>

            <form wire:submit.prevent="save">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input wire:model.defer="name" type="text" id="name" placeholder="Member Name"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 text-sm border transition duration-150">
                        @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input wire:model.defer="email" type="email" id="email" placeholder="member@email.com"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 text-sm border transition duration-150">
                        @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 gap-6 mt-6 sm:grid-cols-3">
                    <!-- Password -->
                    <div class="sm:col-span-2">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ $editingUserId ? 'New Password (Leave blank to keep old)' : 'Password' }}
                        </label>
                        <input wire:model.defer="password" type="password" id="password"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 text-sm border transition duration-150">
                        @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Role Dropdown -->
                    <div class="sm:col-span-1">
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select wire:model.defer="role" id="role"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 text-sm border transition duration-150">
                            <option value="{{ App\Models\User::ROLE_MEMBER }}">Member</option>
                            <option value="{{ App\Models\User::ROLE_ADMIN }}">Admin</option>
                        </select>
                        @error('role') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-8 flex flex-col sm:flex-row sm:space-x-4 space-y-3 sm:space-y-0">
                    <!-- Primary Save/Update Button -->
                    <button type="submit" class="w-full sm:w-auto rounded-lg bg-indigo-600 py-2.5 px-6 text-sm font-semibold text-white shadow-md hover:bg-indigo-700 transition duration-300 transform hover:scale-[1.01] focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:ring-opacity-50">
                        <span wire:loading.remove wire:target="save">{{ $editingUserId ? 'Update Member' : 'Register Member' }}</span>
                        <span wire:loading wire:target="save">Processing...</span>
                    </button>
                    <!-- Cancel Edit Button -->
                    @if ($editingUserId)
                        <button wire:click="resetForm" type="button" class="w-full sm:w-auto rounded-lg border border-gray-300 bg-white py-2.5 px-6 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                            Cancel
                        </button>
                    @endif
                </div>
            </form>
        </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg mb-8">
                <p class="font-semibold">Unauthorized</p>
                <p class="text-sm">You do not have permission to manage the Member Registry.</p>
            </div>
        @endif
    @endauth


    <!-- 2. Member List -->
    <div class="bg-white p-6 md:p-8 rounded-2xl shadow-xl ring-1 ring-gray-100">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Registered Members</h2>

        <!-- Search Input -->
        <div class="mb-6">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by ID, Name, or Email..."
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 text-sm border transition duration-150">
        </div>

        <div class="overflow-x-auto responsive-table">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-indigo-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider min-w-[50px]">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider min-w-[150px]">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider min-w-[200px]">Email</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider min-w-[100px]">Role</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider min-w-[120px]">
                            @auth @if (Auth::user()->isAdmin()) Actions @endif @endauth
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($users as $user)
                        <tr wire:key="user-{{ $user->id }}" class="even:bg-gray-50 hover:bg-indigo-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-extrabold text-indigo-700">{{ $user->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold 
                                      {{ $user->isAdmin() ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                            @auth @if (Auth::user()->isAdmin())
                                <button wire:click="edit({{ $user->id }})" class="text-indigo-600 hover:text-indigo-900 font-semibold transition duration-150 p-1 rounded-md hover:bg-indigo-100">
                                    Edit
                                </button>
                                <button wire:click="delete({{ $user->id }})"
                                        wire:confirm="Are you sure you want to delete member {{ $user->id }}?"
                                        class="text-red-600 hover:text-red-900 font-semibold transition duration-150 p-1 rounded-md hover:bg-red-100">
                                    Delete
                                </button>
                            @else
                                <span class="text-gray-400">View Only</span>
                            @endif @endauth
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500 bg-gray-50/50">
                                <p class="font-medium text-lg">No members registered yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="mt-8">
            {{ $users->links() }}
        </div>
    </div>
</div>


</div>