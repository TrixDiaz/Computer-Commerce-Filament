<div>
    <div x-data="{ 
    cartItems: @entangle('cartItems'),
    total: @entangle('total'),
    subtotal: @entangle('subtotal'),
    tax: @entangle('tax'),
    deliveryFee: @entangle('deliveryFee'),
    paymentMethod: @entangle('paymentMethod'),
    shippingOption: @entangle('shippingOption'),
    discount: @entangle('discount')
}" wire:poll.5s="[getUpdatedCart,loadUserAddresses]">
        <section class="bg-white py-8 antialiased dark:bg-gray-900 md:py-16">
            <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Shopping Cart</h2>

                <div class="mt-6 sm:mt-8 md:gap-6 lg:flex lg:items-start xl:gap-8">
                    <div class="mx-auto w-full flex-none lg:max-w-2xl xl:max-w-4xl">
                        <div class="space-y-6">
                            @if(empty($cartItems))
                            <p class="text-gray-500 dark:text-gray-400">Your cart is empty.</p>
                            @else
                            <template x-for="(item, productId) in cartItems" :key="productId">
                                <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 md:p-6">
                                    <div class="space-y-4 md:flex md:items-center md:justify-between md:gap-6 md:space-y-0">
                                        <a href="#" class="w-20 shrink-0 md:order-1">
                                            <img class="h-20 w-20 dark:hidden"
                                                :src="item.image || '/images/laptop-image.png'"
                                                :alt="item.name"
                                                @mouseover="$event.target.src='/images/hover-image.png'"
                                                @mouseout="$event.target.src=item.image || '/images/laptop-image.png'" />
                                            <img class="hidden h-20 w-20 dark:block"
                                                :src="item.image || '/images/laptop-image.png'"
                                                :alt="item.name"
                                                @mouseover="$event.target.src='/images/hover-image.png'"
                                                @mouseout="$event.target.src=item.image || '/images/laptop-image.png'" />
                                        </a>

                                        <label :for="'counter-input-' + productId" class="sr-only">Choose quantity:</label>
                                        <div class="flex items-center justify-between md:order-3 md:justify-end">
                                            <div class="flex items-center">
                                                <button type="button" @click="$wire.updateQuantity(productId, item.quantity - 1)" class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                                                    <svg class="h-2.5 w-2.5 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                                    </svg>
                                                </button>
                                                <input type="text" :id="'counter-input-' + productId" x-model="item.quantity" @change="$wire.updateQuantity(productId, item.quantity)" class="w-10 shrink-0 border-0 bg-transparent text-center text-sm font-medium text-gray-900 focus:outline-none focus:ring-0 dark:text-white" required />
                                                <button type="button" @click="$wire.updateQuantity(productId, item.quantity + 1)" class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                                                    <svg class="h-2.5 w-2.5 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="text-end md:order-4 md:w-32">
                                                <p class="text-base font-bold text-gray-900 dark:text-white" x-text="'₱' + item.price"></p>
                                            </div>
                                        </div>

                                        <div class="w-full min-w-0 flex-1 space-y-4 md:order-2 md:max-w-md">
                                            <a href="#" class="text-base font-medium text-gray-900 hover:underline dark:text-white" x-text="item.name"></a>

                                            <div class="flex items-center gap-4">
                                                <button type="button" @click="$wire.removeItem(productId)" class="inline-flex items-center text-sm font-medium text-red-600 hover:underline dark:text-red-500">
                                                    <svg class="me-1.5 h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                                                    </svg>
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            @endif
                        </div>

                        <!-- Related Products section -->
                        <div class="mt-8">
                            <h3 class="text-2xl font-semibold text-gray-900 dark:text-white">Related Products</h3>
                            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                @forelse($relatedProducts as $product)
                                <div class="space-y-6 overflow-hidden rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                    <a href="#" class="overflow-hidden rounded">
                                        <img class="mx-auto h-44 w-44 dark:hidden"
                                            src="{{ $product['images'] ?? '/images/laptop-image.png' }}"
                                            alt="{{ $product['name'] }}"
                                            onmouseover="this.src='/images/hover-image.png'"
                                            onmouseout="this.src='{{ $product['images'] ?? '/images/laptop-image.png' }}'" />
                                        <img class="mx-auto h-44 w-44 hidden dark:block"
                                            src="{{ $product['images'] ?? '/images/laptop-image.png' }}"
                                            alt="{{ $product['name'] }}"
                                            onmouseover="this.src='/images/hover-image.png'"
                                            onmouseout="this.src='{{ $product['images'] ?? '/images/laptop-image.png' }}'" />
                                    </a>
                                    <div>
                                        <a href="#" class="text-lg font-semibold leading-tight text-gray-900 hover:underline dark:text-white">{{ $product['name'] }}</a>
                                        <p class="mt-2 text-base font-normal text-gray-500 dark:text-gray-400">{{ $product['description'] }}</p>
                                    </div>
                                    <div>
                                        <p class="text-lg font-bold leading-tight text-gray-900 dark:text-white">₱{{ number_format($product['price'], 2) }}</p>
                                    </div>
                                    <div class="mt-6">
                                        <button type="button" wire:click="addToCart({{ $product['id'] }})" class="inline-flex w-full items-center justify-center rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                            <svg class="-ms-2 me-2 h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7h-1M8 7h-.688M13 5v4m-2-2h4" />
                                            </svg>
                                            Add to cart
                                        </button>
                                    </div>
                                </div>
                                @empty
                                <p class="text-gray-500 dark:text-gray-400">No related products found.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="mx-auto mt-6 max-w-4xl flex-1 space-y-6 lg:mt-0 lg:w-full">
                        <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6">
                            <p class="text-xl font-semibold text-gray-900 dark:text-white">Order summary</p>

                            <!-- Payment Method Selection -->
                            <div class="mt-4">
                                <p class="mb-2 text-base font-semibold text-gray-900 dark:text-white">Payment Method</p>
                                <div class="flex space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" wire:model="paymentMethod" wire:change="updatePaymentMethod('cod')" value="cod" class="form-radio">
                                        <span class="ml-2">Cash on Delivery</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" wire:model="paymentMethod" wire:change="updatePaymentMethod('gcash')" value="gcash" class="form-radio">
                                        <span class="ml-2">GCash</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Shipping Option Selection -->
                            <div class="mt-4">
                                <p class="mb-2 text-base font-semibold text-gray-900 dark:text-white">Shipping Option</p>
                                <div class="flex space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" wire:model="shippingOption" wire:change="updateShippingOption('normal')" value="normal" class="form-radio">
                                        <span class="ml-2">Normal Shipping (Free)</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" wire:model="shippingOption" wire:change="updateShippingOption('rush')" value="rush" class="form-radio">
                                        <span class="ml-2">Rush Shipping (₱100)</span>
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <dl class="flex items-center justify-between gap-4 border-t border-gray-200 pt-2 dark:border-gray-700">
                                    <dt class="text-base font-bold text-gray-900 dark:text-white">Subtotal</dt>
                                    <dd class="text-base font-bold text-gray-900 dark:text-white" x-text="'₱' + subtotal.toFixed(2)"></dd>
                                </dl>
                                <dl class="flex items-center justify-between gap-4 border-t border-gray-200 pt-2 dark:border-gray-700">
                                    <dt class="text-base font-bold text-gray-900 dark:text-white">Tax</dt>
                                    <dd class="text-base font-bold text-gray-900 dark:text-white" x-text="'₱' + tax.toFixed(2)"></dd>
                                </dl>
                                <dl class="flex items-center justify-between gap-4 border-t border-gray-200 pt-2 dark:border-gray-700">
                                    <dt class="text-base font-bold text-gray-900 dark:text-white">Delivery Fee</dt>
                                    <dd class="text-base font-bold text-gray-900 dark:text-white" x-text="'₱' + deliveryFee.toFixed(2)"></dd>
                                </dl>
                                <dl class="flex items-center justify-between gap-4 border-t border-gray-200 pt-2 dark:border-gray-700">
                                    <dt class="text-base font-bold text-gray-900 dark:text-white">Discount</dt>
                                    <dd class="text-base font-bold text-gray-900 dark:text-white" x-text="'₱' + discount.toFixed(2)"></dd>
                                </dl>
                                <dl class="flex items-center justify-between gap-4 border-t border-gray-200 pt-2 dark:border-gray-700">
                                    <dt class="text-base font-bold text-gray-900 dark:text-white">Total</dt>
                                    <dd class="text-base font-bold text-gray-900 dark:text-white" x-text="'₱' + total.toFixed(2)"></dd>
                                </dl>
                            </div>

                            <button
                                wire:click="proceedToCheckout"
                                class="flex w-full items-center justify-center rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="Object.keys(cartItems).length === 0">
                                Proceed to Checkout
                            </button>

                            <div class="flex items-center justify-center gap-2">
                                <span class="text-sm font-normal text-gray-500 dark:text-gray-400"> or </span>
                                <a href="{{ route('catalog') }}" title="" class="inline-flex items-center gap-2 text-sm font-medium text-blue-700 underline hover:no-underline dark:text-blue-500">
                                    Continue Shopping
                                    <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <!-- Address Selection -->
                        <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6">
                            <p class="text-xl font-semibold text-gray-900 dark:text-white">Shipping Address</p>

                            @if($addresses->isNotEmpty())
                            <div class="space-y-2">
                                @foreach($addresses as $address)
                                <label class="flex items-center space-x-3">
                                    <input type="radio" wire:model="selectedAddressId" value="{{ $address->id }}" class="form-radio">
                                    <span class="text-sm">
                                        {{ $address->address_line_1 }}, 
                                        @if($address->address_line_2)
                                            {{ $address->address_line_2 }},
                                        @endif
                                        {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}, {{ $address->country }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                            @else
                            <p class="text-sm text-gray-500">No addresses found. Please add a new address.</p>
                            @endif

                            <button type="button" x-data="{}" x-on:click="$dispatch('open-modal', 'add-address')" class="text-blue-600 hover:underline">
                                Add New Address
                            </button>
                        </div>

                        <!-- Keep the Apply Code section as is -->
                        <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6"
                            x-data="{ couponCode: '' }">
                            <form class="space-y-4" @submit.prevent="$wire.applyCoupon(couponCode)">
                                <div>
                                    <label for="voucher" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                                        Do you have a voucher or gift card?
                                    </label>
                                    <input type="text" id="voucher"
                                        x-model="couponCode"
                                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                                        placeholder="Enter coupon code" required />
                                </div>
                                <button type="submit"
                                    class="flex w-full items-center justify-center rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                    Apply Code
                                </button>
                            </form>
                            @error('coupon')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                            @if (session()->has('coupon_message'))
                            <p class="mt-2 text-sm {{ session('coupon_success') ? 'text-green-600 dark:text-green-500' : 'text-red-600 dark:text-red-500' }}">
                                {{ session('coupon_message') }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Add Address Modal -->
    <x-modal name="add-address" focusable>
        <form wire:submit.prevent="addNewAddress" class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Add New Address') }}
            </h2>

            <div class="mt-6 space-y-6">
                <div>
                    <x-input-label for="address_line_1" :value="__('Address Line 1')" />
                    <x-text-input id="address_line_1" type="text" class="mt-1 block w-full" wire:model.defer="newAddress.address_line_1" />
                    <x-input-error :messages="$errors->get('newAddress.address_line_1')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="address_line_2" :value="__('Address Line 2 (Optional)')" />
                    <x-text-input id="address_line_2" type="text" class="mt-1 block w-full" wire:model.defer="newAddress.address_line_2" />
                </div>

                <div>
                    <x-input-label for="city" :value="__('City')" />
                    <x-text-input id="city" type="text" class="mt-1 block w-full" wire:model.defer="newAddress.city" />
                    <x-input-error :messages="$errors->get('newAddress.city')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="state" :value="__('State')" />
                    <x-text-input id="state" type="text" class="mt-1 block w-full" wire:model.defer="newAddress.state" />
                    <x-input-error :messages="$errors->get('newAddress.state')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="postal_code" :value="__('Postal Code')" />
                    <x-text-input id="postal_code" type="text" class="mt-1 block w-full" wire:model.defer="newAddress.postal_code" />
                    <x-input-error :messages="$errors->get('newAddress.postal_code')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="country" :value="__('Country')" />
                    <x-text-input id="country" type="text" class="mt-1 block w-full" wire:model.defer="newAddress.country" />
                    <x-input-error :messages="$errors->get('newAddress.country')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    {{ __('Add Address') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</div>