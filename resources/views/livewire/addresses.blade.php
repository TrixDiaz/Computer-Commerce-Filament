<div class="max-w-screen-xl px-4 mx-auto 2xl:px-0 py-4">
    <h2 class="text-2xl font-semibold mb-4">Your Addresses</h2>

    @foreach($addresses as $address)
    <div class="bg-white shadow-md rounded-lg p-4 mb-4 flex justify-between items-start">
        <div>
            <p>{{ $address->address_line_1 }}</p>
            @if($address->address_line_2)
            <p>{{ $address->address_line_2 }}</p>
            @endif
            <p>{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
            <p>{{ $address->country }}</p>
        </div>
        <button wire:click="confirmDeleteAddress({{ $address->id }})" class="bg-red-500 text-white px-3 py-1 rounded-lg text-sm">
            Delete
        </button>
    </div>
    @endforeach

    <button wire:click="showAddressForm" class="bg-blue-500 text-white px-4 py-2 rounded-lg">
        Add New Address
    </button>

    @if($showForm)
    <form wire:submit.prevent="addAddress" class="mt-4">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="address_line_1" class="block text-sm font-medium text-gray-700" required>Address Line 1</label>
                <input type="text" wire:model="address_line_1" id="address_line_1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('address_line_1') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="address_line_2" class="block text-sm font-medium text-gray-700">Address Line 2</label>
                <input type="text" wire:model="address_line_2" id="address_line_2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('address_line_2') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                <input type="text" wire:model="city" id="city" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                <input type="text" wire:model="state" id="state" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('state') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                <input type="text" wire:model="postal_code" id="postal_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('postal_code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                <input type="text" wire:model="country" id="country" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('country') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg">Save Address</button>
        </div>
    </form>
    @endif

    <!-- Delete Confirmation Modal -->
    <div x-data="{ show: @entangle('showDeleteModal') }" x-show="show" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Delete Address
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Are you sure you want to delete this address? This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="deleteAddress" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Delete
                    </button>
                    <button wire:click="cancelDelete" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
