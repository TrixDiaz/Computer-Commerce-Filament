<div x-data="{ cartCount: @entangle('cartCount'), open: false, toggle() { this.open = !this.open }, close() { this.open = false }}" wire:poll.15000ms @keydown.escape.prevent.stop="close()" @click.away="close()" class="relative">
    <nav class="bg-white dark:bg-gray-800 antialiased">
        <div class="max-w-screen-xl px-4 mx-auto 2xl:px-0 py-4">
            <div class="flex items-center justify-between">

                <div class="flex items-center space-x-8">
                    <div class="shrink-0">
                        <a href="{{ route('home') }}" title="" class="text-xl font-bold">
                           GamerGo
                        </a>
                    </div>

                    <ul class="hidden lg:flex items-center justify-start gap-6 md:gap-8 py-3 sm:justify-center">
                        <li>
                            <a href="{{ route('home') }}" title="" class="flex text-sm font-medium text-gray-900 hover:text-blue-700 dark:text-white dark:hover:text-blue-500">
                                Home
                            </a>
                        </li>
                        <li class="shrink-0">
                            <a href="{{ route('catalog') }}" title="" class="flex text-sm font-medium text-gray-900 hover:text-blue-700 dark:text-white dark:hover:text-blue-500">
                                Products
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Updated search bar -->
                <div class="flex-1 max-w-xl mx-4">
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live="search"
                            class="w-full px-4 py-2 text-gray-900 bg-white border border-gray-300 rounded-md dark:bg-gray-800 dark:text-white dark:border-gray-600 focus:border-blue-500 focus:outline-none focus:ring"
                            placeholder="Search products..."
                        >
                        @if(!empty($searchResults))
                            <div class="absolute z-10 w-full mt-1 bg-white rounded-md shadow-lg dark:bg-gray-800 max-h-96 overflow-y-auto">
                                @forelse($searchResults as $product)
                                    <a href="{{ route('product-profile', ['slug' => $product->slug]) }}" class="flex items-center px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <img src="{{ $product->images_url[0] ?? asset('path/to/placeholder-image.jpg') }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded-md mr-4">
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->name }}</h3>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">₱{{ number_format($product->price, 2) }}</p>
                                        </div>
                                    </a>
                                @empty
                                    <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">No results found</div>
                                @endforelse
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-end lg:space-x-2">
                    @auth
                    <div x-data="{ 
                        open: false, 
                        toggle() { this.open = !this.open },
                        close() { this.open = false }
                    }" 
                        @keydown.escape.prevent.stop="close()"
                        @click.away="close()"
                        class="relative inline-block">
                        
                        <button @click="toggle()" 
                            class="flex items-center space-x-2 text-gray-600 hover:text-gray-700 focus:outline-none"
                            x-ref="button">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            Notifications
                            <span class="sr-only">Notifications</span>
                            @if($unreadNotificationsCount > 0)
                                <span class="relative top-2 right-0 -mt-4 -mr-2 bg-red-500 text-white text-xs font-bold rounded-full px-2 py-1">
                                    {{ $unreadNotificationsCount }}
                                </span>
                            @endif
                        </button>

                        <div x-show="open" 
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg overflow-hidden z-20"
                            x-ref="panel"
                            x-cloak
                            @click.away="close()"
                            :style="{ top: $refs.button.offsetHeight + 'px', right: '0px' }">
                            
                            <div class="py-2">
                                <div class="px-4 py-2 bg-gray-100 border-b flex justify-between items-center">
                                    <h3 class="text-sm font-semibold text-gray-800">Notifications</h3>
                                    <div>
                                        @if($unreadNotificationsCount > 0)
                                            <button wire:click="markAllAsRead" class="text-xs text-blue-600 hover:text-blue-800 mr-2">
                                                Mark all as read
                                            </button>
                                        @endif
                                        @if(count($notifications) > 0)
                                            <button wire:click="clearAllNotifications" class="text-xs text-red-600 hover:text-red-800">
                                                Clear all
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                
                                @forelse($notifications as $notification)
                                    <div class="px-4 py-3 hover:bg-gray-50 {{ $notification->read_at ? 'opacity-50' : '' }}">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $notification->data['title'] ?? 'Order Placed Successfully' }}
                                                </p>
                                                @php
                                                    $body = $notification->data['body'] ?? '';
                                                    preg_match('/Order #([\w-]+)/', $body, $matches);
                                                    $orderNumber = $matches[1] ?? '';
                                                @endphp
                                                @if($orderNumber)
                                                    <p class="mt-1 text-sm text-gray-600">
                                                        Order #{{ $orderNumber }}
                                                    </p>
                                                @endif
                                                <p class="mt-1 text-xs text-gray-400">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                            <div>
                                                @if(!$notification->read_at)
                                                    <button wire:click="markAsRead('{{ $notification->id }}')" class="text-xs text-blue-600 hover:text-blue-800 mr-2">
                                                        Mark as read
                                                    </button>
                                                @endif
                                                <button wire:click="clearNotification('{{ $notification->id }}')" class="text-xs text-red-600 hover:text-red-800">
                                                    Clear
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="px-4 py-3 text-sm text-gray-500">
                                        No notifications
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <button id="myCartDropdownButton1" data-dropdown-toggle="myCartDropdown1" type="button" class="inline-flex items-center rounded-lg justify-center p-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm font-medium leading-none text-gray-900 dark:text-white">
                        <span class="sr-only">
                            Cart
                        </span>
                        <svg class="w-5 h-5 lg:me-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7H7.312" />
                        </svg>
                        <span class="hidden sm:flex">My Cart</span>
                        <span x-text="cartCount" class="ml-1 text-xs font-semibold"></span>
                        <svg class="hidden sm:flex w-4 h-4 text-gray-900 dark:text-white ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="myCartDropdown1" class="hidden z-10 mx-auto max-w-sm space-y-4 overflow-hidden rounded-lg bg-white p-4 antialiased shadow-lg dark:bg-gray-800">
                        @if(session()->has('cart') && is_array(session('cart')))
                            @foreach(session('cart') as $id => $details)
                                @if(is_array($details) && isset($details['name'], $details['price'], $details['quantity'], $details['slug']))
                                    <div class="grid grid-cols-2">
                                        <div>
                                            <a href="{{ route('product-profile', ['slug' => $details['slug']]) }}" class="truncate text-sm font-semibold leading-none text-gray-900 dark:text-white hover:underline">{{ $details['name'] }}</a>
                                            <p class="mt-0.5 truncate text-sm font-normal text-gray-500 dark:text-gray-400">₱{{ number_format($details['price'], 2) }}</p>
                                        </div>

                                        <div class="flex items-center justify-end gap-6">
                                            <p class="text-sm font-normal leading-none text-gray-500 dark:text-gray-400">Qty: {{ $details['quantity'] }}</p>

                                            <button wire:click="removeFromCart({{ $id }})" class="text-red-600 hover:text-red-700 dark:text-red-500 dark:hover:text-red-600">
                                                <span class="sr-only">Remove</span>
                                                <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                    <path fill-rule="evenodd" d="M2 12a10 10 0 1 1 20 0 10 10 0 0 1-20 0Zm7.7-3.7a1 1 0 0 0-1.4 1.4l2.3 2.3-2.3 2.3a1 1 0 1 0 1.4 1.4l2.3-2.3 2.3 2.3a1 1 0 0 0 1.4-1.4L13.4 12l2.3-2.3 2.3 2.3a1 1 0 0 0 1.4-1.4L12 10.6 9.7 8.3Z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <p class="text-center text-gray-500 dark:text-gray-400">Your cart is empty</p>
                        @endif

                        <a href="{{ route('cart') }}" title="" class="mb-2 me-2 inline-flex w-full items-center justify-center rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" role="button">Proceed to Cart</a>
                    </div>

                    <button id="userDropdownButton1" data-dropdown-toggle="userDropdown1" type="button" class="inline-flex items-center rounded-lg justify-center p-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm font-medium leading-none text-gray-900 dark:text-white">
                        <svg class="w-5 h-5 me-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-width="2" d="M7 17v1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1a3 3 0 0 0-3-3h-4a3 3 0 0 0-3 3Zm8-9a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        {{ Auth::user()->name }}
                        <svg class="w-4 h-4 text-gray-900 dark:text-white ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="userDropdown1" class="hidden z-10 w-56 divide-y divide-gray-100 overflow-hidden overflow-y-auto rounded-lg bg-white antialiased shadow dark:divide-gray-600 dark:bg-gray-700">
                        <ul class="p-2 text-start text-sm font-medium text-gray-900 dark:text-white">
                            <li><a href="{{ route('profile') }}" title="" class="inline-flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600"> My Account </a></li>
                            <li><a href="{{ route('orders') }}" title="" class="inline-flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600"> My Orders </a></li>
                            <li><a href="{{ route('address') }}" title="" class="inline-flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600">My Addresses </a></li>
                        </ul>

                        <div class="p-2 text-sm font-medium text-gray-900 dark:text-white">
                            <form wire:submit="logout">
                                @csrf
                                <button type="submit" class="inline-flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600">
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                    <a href="{{ route('login') }}" class="inline-flex items-center rounded-lg justify-center p-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm font-medium leading-none text-gray-900 dark:text-white">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center rounded-lg justify-center p-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm font-medium leading-none text-gray-900 dark:text-white">
                        Register
                    </a>
                    @endauth

                    <button type="button" data-collapse-toggle="ecommerce-navbar-menu-1" aria-controls="ecommerce-navbar-menu-1" aria-expanded="false" class="inline-flex lg:hidden items-center justify-center hover:bg-gray-100 rounded-md dark:hover:bg-gray-700 p-2 text-gray-900 dark:text-white">
                        <span class="sr-only">
                            Open Menu
                        </span>
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h14" />
                        </svg>
                    </button>
                </div>
            </div>

            <div id="ecommerce-navbar-menu-1" class="bg-gray-50 dark:bg-gray-700 dark:border-gray-600 border border-gray-200 rounded-lg py-3 hidden px-4 mt-4">
                <ul class="text-gray-900 dark:text-white text-sm font-medium space-y-3">
                    <li><a href="{{ route('profile') }}" title="" class="inline-flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600"> My Account </a></li>
                    <li><a href="{{ route('orders') }}" title="" class="inline-flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600"> My Orders </a></li>
                    <li><a href="{{ route('address') }}" title="" class="inline-flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600">My Addresses </a></li>
                </ul>
            </div>
        </div>
    </nav>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('livewire:initialized', function() {
        Livewire.on('showAlert', (data) => {
            Swal.fire({
                title: data[0].type === 'success' ? 'Success!' : 'Info',
                text: data[0].message,
                icon: data[0].type,
                confirmButtonText: 'OK'
            });
        });

        Livewire.on('cartCountUpdated', (newCount) => {
            Alpine.store('cartCount', newCount);
        });
    });
</script>
@endpush
