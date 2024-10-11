<div class="py-8 px-4 mx-auto max-w-screen-xl">
    <h1 class="text-3xl font-bold mb-4">{{ $product->name }}</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="flex justify-center items-center">
            @php
                $images = json_decode($product->image_url, true) ?? [];
                $defaultImage = count($images) > 0 ? $images[0] : '/images/laptop-image.png';
                $hoverImage = count($images) > 1 ? $images[array_rand($images, 1)] : '/images/hover-image.png';
            @endphp
            <img src="{{ $defaultImage }}" 
                 alt="{{ $product->name }}" 
                 class="max-w-full h-72 rounded-lg shadow-md object-contain"
                 x-data="{}"
                 @mouseenter="$event.target.src = '{{ $hoverImage }}'"
                 @mouseleave="$event.target.src = '{{ $defaultImage }}'">
        </div>
        <div>
            <p class="text-xl font-semibold mb-2">₱{{ number_format($product->price, 2) }}</p>
            <p class="text-gray-600 mb-4">Description: {{ $product->description }}</p>
            <p class="text-gray-600 mb-4">Stock: {{ $product->stock_quantity }}</p>
            <p class="text-gray-600 mb-4">SKU: {{ $product->sku }}</p>
            <p class="text-gray-600 mb-4">Category: {{ $product->category->name }}</p>
            <p class="text-gray-600 mb-4">Brand: {{ $product->brand->name }}</p>
            <p class="text-gray-600 mb-4">Featured: {{ $product->is_featured ? 'Yes' : 'No' }}</p>
            <p class="text-gray-600 mb-4">Active: {{ $product->is_active ? 'Yes' : 'No' }}</p>
            <p class="text-gray-600 mb-4">Sale: {{ $product->is_sale ? 'Yes' : 'No' }}</p>
            <p class="text-gray-600 mb-4">New: {{ $product->is_new ? 'Yes' : 'No' }}</p>
            <p class="text-gray-600 mb-4">Best Seller: {{ $product->is_best_seller ? 'Yes' : 'No' }}</p>
            <p class="text-gray-600 mb-4">Top Rated: {{ $product->is_top_rated ? 'Yes' : 'No' }}</p>
            <p class="text-gray-600 mb-4">On Sale: {{ $product->is_on_sale ? 'Yes' : 'No' }}</p>
            <p class="text-gray-600 mb-4">Created At: {{ $product->created_at->format('F j, Y') }}</p>
            <p class="text-gray-600 mb-4">Updated At: {{ $product->updated_at->format('F j, Y') }}</p>

            <!-- Add to Cart and Pay with GCash buttons -->
            <div class="flex space-x-4">
                @auth
                    <button wire:click="addToCart({{ $product->id }})" 
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        <span wire:loading.remove wire:target="addToCart">Add to Cart</span>
                        <span wire:loading wire:target="addToCart">Adding...</span>
                    </button>
                    <button wire:click="payWithGCash({{ $product->id }})" 
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        <span wire:loading.remove wire:target="payWithGCash">Pay with GCash</span>
                        <span wire:loading wire:target="payWithGCash">Processing...</span>
                    </button>
                @else
                    <a href="{{ route('login') }}" 
                       class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Login to Add to Cart
                    </a>
                    <a href="{{ route('login') }}" 
                       class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Login to Pay with GCash
                    </a>
                @endauth
            </div>

            @guest
                <p class="mt-4 text-red-500">Please <a href="{{ route('login') }}" class="underline">login</a> to add items to your cart or make a purchase.</p>
            @endguest
        </div>
    </div>
</div>
