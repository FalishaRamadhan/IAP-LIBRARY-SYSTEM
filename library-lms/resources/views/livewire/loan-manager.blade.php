<div class="p-4 sm:p-6 md:p-10 bg-gray-100 min-h-screen">
<script src="https://www.google.com/search?q=https://cdn.tailwindcss.com"></script>
<style>
@import url('https://www.google.com/search?q=https://fonts.googleapis.com/css2%3Ffamily%3DInter:wght%40400%3B600%3B700%26display%3Dswap');
body { font-family: 'Inter', sans-serif; }
.responsive-table::-webkit-scrollbar { height: 8px; }
.responsive-table::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
</style>

<div class="max-w-7xl mx-auto">
    <header class="text-center md:text-left mb-8 pb-4 border-b-2 border-indigo-600">
        <h1 class="text-4xl font-extrabold text-gray-900 leading-tight">Loan Management <span class="text-green-600">Desk</span></h1>
        <p class="text-gray-500 mt-1">Issue books to members and process returns.</p>
    </header>

    <!-- Session Message Display (Floating) -->
    @if (session()->has('success') || session()->has('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:leave="transition ease-in duration-200"
            class="fixed bottom-4 right-4 z-50 p-4 rounded-xl shadow-2xl transition duration-300
            {{ session()->has('success') ? 'bg-green-600' : 'bg-red-600' }} text-white font-semibold"
            role="alert">
            <span class="block sm:inline">{{ session('success') ?? session('error') }}</span>
        </div>
    @endif

    <!-- Grid Layout: Issuance Form & Loan List -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Issuance Form Card (Only visible to Admin) -->
        @auth @if (Auth::user()->isAdmin())
        <div class="lg:col-span-1 bg-white p-6 md:p-8 rounded-2xl shadow-xl ring-1 ring-gray-100 h-fit">
            <h2 class="text-2xl font-bold text-green-700 mb-6 border-b pb-2">Issue New Book</h2>

            <form wire:submit.prevent="issueLoan">
                <!-- Member ID Input -->
                <div class="mb-4">
                    <label for="memberId" class="block text-sm font-medium text-gray-700 mb-1">Member ID (User ID)</label>
                    <input wire:model.defer="memberId" type="number" id="memberId" placeholder="Enter User ID (e.g., 1)"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2.5 text-sm border transition duration-150">
                    @error('memberId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Book Selection (Search Dropdown) -->
                <div class="mb-4 relative">
                    <label for="bookSearch" class="block text-sm font-medium text-gray-700 mb-1">Book Title (Available)</label>
                    <input wire:model.live.debounce.300ms="bookSearch" type="text" id="bookSearch" placeholder="Search for book title..."
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2.5 text-sm border transition duration-150"
                           autocomplete="off">

                    @if ($bookSearch && !$selectedBookId)
                        <div class="absolute z-10 w-full mt-1 bg-white rounded-lg shadow-lg border border-gray-200">
                            @forelse ($availableBooks as $book)
                                <button type="button" wire:click="selectBook({{ $book->id }})"
                                        class="block w-full text-left px-4 py-2 hover:bg-green-50 transition duration-100">
                                    {{ $book->title }} (Available: {{ $book->available_copies }})
                                </button>
                            @empty
                                <div class="px-4 py-2 text-gray-500">No available books found.</div>
                            @endforelse
                        </div>
                    @endif

                    @if ($selectedBookId)
                        <p class="mt-2 p-2 text-sm bg-green-50 rounded-lg text-green-700">Selected: **{{ $bookSearch }}**</p>
                    @endif

                    @error('selectedBookId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <!-- Due Date Days -->
                <div class="mb-6">
                    <label for="dueDateDays" class="block text-sm font-medium text-gray-700 mb-1">Loan Period (Days)</label>
                    <input wire:model.defer="dueDateDays" type="number" id="dueDateDays" min="1" max="60"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2.5 text-sm border transition duration-150">
                    @error('dueDateDays') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full rounded-lg bg-green-600 py-2.5 px-6 text-sm font-semibold text-white shadow-md hover:bg-green-700 transition duration-300 transform hover:scale-[1.01] focus:outline-none focus:ring-4 focus:ring-green-500 focus:ring-opacity-50">
                    <span wire:loading.remove wire:target="issueLoan">Confirm and Issue Book</span>
                    <span wire:loading wire:target="issueLoan">Issuing...</span>
                </button>
            </form>
        </div>
        @endif @endauth

        <!-- Loan List Table -->
        <div class="{{ Auth::check() && Auth::user()->isAdmin() ? 'lg:col-span-2' : 'lg:col-span-3' }} bg-white p-6 md:p-8 rounded-2xl shadow-xl ring-1 ring-gray-100">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">{{ Auth::check() && Auth::user()->isAdmin() ? 'Active & History' : 'My Loan History' }}</h2>
            
            <!-- Search & Filters -->
            <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 mb-6">
                <div class="flex-grow">
                    <input wire:model.live.debounce.300ms="loanSearch" type="text" placeholder="Search by Book Title or Member ID..."
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 text-sm border transition duration-150">
                </div>
                <div>
                    <select wire:model.live="filterStatus" class="w-full sm:w-auto rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 text-sm border transition duration-150">
                        <option value="on_loan">Active Loans</option>
                        <option value="returned">Returned History</option>
                        <option value="all">View All</option>
                    </select>
                </div>
            </div>

            <!-- Responsive Table Wrapper -->
            <div class="overflow-x-auto responsive-table">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-indigo-50/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider min-w-[150px]">Book Title</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider min-w-[100px]">Member ID</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider min-w-[100px]">Issued / Due</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider min-w-[100px]">Status</th>
                            @auth @if (Auth::user()->isAdmin())
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider min-w-[100px]">Action</th>
                            @endif @endauth
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($loans as $loan)
                            <tr wire:key="loan-{{ $loan->id }}" class="even:bg-gray-50 hover:bg-indigo-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $loan->book->title ?? 'Book Deleted' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $loan->user_id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    Issued: {{ $loan->issued_at->format('M d, Y') }}<br>
                                    <span class="font-semibold text-{{ $loan->due_date->isPast() && !$loan->returned_at ? 'red' : 'gray' }}-700">Due: {{ $loan->due_date->format('M d, Y') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    @if ($loan->returned_at)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-green-100 text-green-700">Returned</span>
                                    @elseif ($loan->due_date->isPast())
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-red-100 text-red-700">OVERDUE</span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">Active</span>
                                    @endif
                                </td>
                                @auth @if (Auth::user()->isAdmin())
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if (!$loan->returned_at)
                                        <button wire:click="returnLoan({{ $loan->id }})"
                                                wire:confirm="Confirm return of '{{ $loan->book->title ?? 'Book' }}'?"
                                                class="text-sm rounded-lg bg-indigo-500 text-white px-3 py-1 hover:bg-indigo-600 transition duration-150">
                                            Process Return
                                        </button>
                                    @else
                                        <span class="text-gray-500 text-xs">Closed</span>
                                    @endif
                                </td>
                                @endif @endauth
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ Auth::check() && Auth::user()->isAdmin() ? '5' : '4' }}" class="px-6 py-10 text-center text-gray-500 bg-gray-50/50">
                                    <p class="font-medium text-lg">No loans match your criteria.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="mt-8">
                {{ $loans->links(data: ['pageName' => 'loanPage']) }}
            </div>
        </div>
    </div>
</div>


</div>