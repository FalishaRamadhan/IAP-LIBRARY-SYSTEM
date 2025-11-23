<div class="p-4 sm:p-6 md:p-10 bg-gray-100 min-h-screen">
<script src="https://www.google.com/search?q=https://cdn.tailwindcss.com"></script>
<style>
@import url('https://www.google.com/search?q=https://fonts.googleapis.com/css2%3Ffamily%3DInter:wght%40400%3B600%3B700%26display%3Dswap');
body { font-family: 'Inter', sans-serif; }
/* Custom scrollbar for table overflow */
.responsive-table::-webkit-scrollbar { height: 8px; }
.responsive-table::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
</style>

<div class="max-w-7xl mx-auto">
    <header class="text-center md:text-left mb-8 pb-4 border-b-2 border-indigo-200">
        <h1 class="text-4xl font-extrabold text-gray-900 leading-tight">Library Hub <span class="text-indigo-600">Inventory</span></h1>
        <p class="text-gray-500 mt-1">Manage, search, and track all books in the library collection.</p>
    </header>

    <!-- Session Message Display (Modernized) -->
    @if (session()->has('success') || session()->has('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="fixed bottom-4 right-4 z-50 p-4 rounded-xl shadow-2xl transition duration-300
            {{ session()->has('success') ? 'bg-green-600' : 'bg-red-600' }} text-white font-semibold"
            role="alert">
            <span class="block sm:inline">{{ session('success') ?? session('error') }}</span>
        </div>
    @endif

    @auth
        <!-- 1. Book Management Form (Only visible to Admin) -->
        @if (Auth::user()->isAdmin())
        <div class="bg-white p-6 md:p-8 rounded-2xl shadow-xl ring-1 ring-gray-100 mb-10">
            <h2 class="text-2xl font-bold text-indigo-700 mb-6 border-b pb-2">
                {{ $editingBookId ? 'Edit Book Record' : 'Add New Book' }}
            </h2>

            <form wire:submit.prevent="save">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Title -->
                    <div class="lg:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input wire:model.defer="title" type="text" id="title" placeholder="e.g., The Silent Patient"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 text-sm border transition duration-150">
                        @error('title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Author -->
                    <div class="sm:col-span-1">
                        <label for="author" class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                        <input wire:model.defer="author" type="text" id="author" placeholder="e.g., Alex Michaelides"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 text-sm border transition duration-150">
                        @error('author') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- ISBN -->
                    <div class="sm:col-span-1">
                        <label for="isbn" class="block text-sm font-medium text-gray-700 mb-1">ISBN</label>
                        <input wire:model.defer="isbn" type="text" id="isbn" placeholder="e.g., 978-0123456789"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 text-sm border transition duration-150">
                        @error('isbn') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Copies (Now only takes one column space) -->
                    <div class="sm:col-span-1">
                        <label for="copies" class="block text-sm font-medium text-gray-700 mb-1">Total Copies</label>
                        <input wire:model.defer="copies" type="number" id="copies" min="1"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 text-sm border transition duration-150">
                        @error('copies') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-8 flex flex-col sm:flex-row sm:space-x-4 space-y-3 sm:space-y-0">
                    <!-- Primary Save/Update Button -->
                    <button type="submit" class="w-full sm:w-auto rounded-lg bg-indigo-600 py-2.5 px-6 text-sm font-semibold text-white shadow-md hover:bg-indigo-700 transition duration-300 transform hover:scale-[1.01] focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:ring-opacity-50">
                        <span wire:loading.remove wire:target="save">{{ $editingBookId ? 'Update Record' : 'Add Book to Inventory' }}</span>
                        <span wire:loading wire:target="save">Processing...</span>
                    </button>
                    <!-- Cancel Edit Button -->
                    @if ($editingBookId)
                        <button wire:click="resetForm" type="button" class="w-full sm:w-auto rounded-lg border border-gray-300 bg-white py-2.5 px-6 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition duration-300">
                            Cancel Edit
                        </button>
                    @endif
                </div>
            </form>
        </div>
        @else 
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg mb-8">
            <p class="font-semibold">Viewing Catalog Only</p>
        </div>
        @endif
    @endauth


    <!-- 2. Book List & Search -->
    <div class="bg-white p-6 md:p-8 rounded-2xl shadow-xl ring-1 ring-gray-100">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Current Book Inventory</h2>

        <!-- Search Input -->
        <div class="mb-6">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <!-- Search Icon (Lucide-react equivalent) -->
                    <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg>
                </div>
                <input wire:model.live="search" type="text" placeholder="Search by Title, Author, or ISBN..."
                    class="pl-10 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 text-sm border transition duration-150">
            </div>
        </div>

        <!-- Responsive Table Wrapper -->
        <div class="overflow-x-auto responsive-table">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-indigo-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider min-w-[150px]">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider min-w-[150px]">Author</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider min-w-[120px]">ISBN</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Available</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider min-w-[120px]">@auth @if (Auth::user()->isAdmin()) Actions @endif @endauth</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($books as $book)
                        <tr wire:key="book-{{ $book->id }}" class="even:bg-gray-50 hover:bg-indigo-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $book->title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $book->author }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $book->isbn }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-medium">{{ $book->total_copies }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold leading-none
                                      {{ $book->available_copies > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $book->available_copies }}
                                </span>
                            </td>
                            
                            @auth @if (Auth::user()->isAdmin())
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                <button wire:click="edit({{ $book->id }})" class="text-indigo-600 hover:text-indigo-900 font-semibold transition duration-150 p-1 rounded-md hover:bg-indigo-100">
                                    Edit
                                </button>
                                <button wire:click="delete({{ $book->id }})"
                                        wire:confirm="Are you sure you want to delete '{{ $book->title }}'?"
                                        class="text-red-600 hover:text-red-900 font-semibold transition duration-150 p-1 rounded-md hover:bg-red-100">
                                    Delete
                                </button>
                            </td>
                            @endif @endauth
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 bg-gray-50/50">
                                <p class="font-medium text-lg">Inventory is empty.</p>
                                <p class="text-sm">Use the form above to add your first book!</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="mt-8">
            {{ $books->links() }}
        </div>
    </div>
</div>


</div>