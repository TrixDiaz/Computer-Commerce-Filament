<div class="py-8 px-4 mx-auto max-w-screen-xl">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">{{ $product->name }}</h1>
    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
        <!-- Image gallery -->
        <div class="space-y-4">
            @if(is_array($product->images_url) && count($product->images_url) > 0)
                <!-- Main image -->
                <div x-data="{ mainImage: '{{ $product->images_url[0] }}' }" class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg shadow-lg">
                    <img :src="mainImage" alt="{{ $product->name }}" class="h-full w-full object-cover object-center transition-opacity duration-300">
                </div>
                
                <!-- Thumbnail gallery -->
                <div class="grid grid-cols-4 gap-4">
                    @foreach($product->images_url as $index => $image)
                        <button 
                            @click="mainImage = '{{ $image }}'"
                            class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg shadow hover:shadow-md transition-shadow duration-300"
                        >
                            <img src="{{ $image }}" alt="{{ $product->name }}" class="h-full w-full object-cover object-center">
                        </button>
                    @endforeach
                </div>
            @elseif(is_string($product->images_url))
                <!-- Fallback for single image -->
                <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg shadow-lg">
                    <img src="{{ $product->images_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover object-center">
                </div>
            @else
                <!-- Fallback for no image -->
                <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg bg-gray-200 flex items-center justify-center">
                    <p class="text-center text-gray-500">No image available</p>
                </div>
            @endif
        </div>

        <!-- Product details -->
        <div class="space-y-6 bg-white p-6 rounded-lg shadow-md">
            <div class="flex justify-between items-center">
                <p class="text-3xl font-semibold text-gray-800">₱{{ number_format($product->price, 2) }}</p>
                <span class="px-3 py-1 text-sm font-semibold text-white bg-green-500 rounded-full">In Stock</span>
            </div>
            
            <div class="prose max-w-none">
                <h3 class="text-xl font-semibold text-gray-700">Description</h3>
                <p class="text-gray-600">{{ $product->description }}</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="font-semibold text-gray-700">SKU:</p>
                    <p class="text-gray-600">{{ $product->sku }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-700">Category:</p>
                    <p class="text-gray-600">{{ $product->category->name }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-700">Brand:</p>
                    <p class="text-gray-600">{{ $product->brand->name }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-700">Stock:</p>
                    <p class="text-gray-600">{{ $product->stock_quantity }}</p>
                </div>
            </div>
            
            <div class="flex flex-wrap gap-2">
                @foreach(['featured', 'active', 'sale', 'new', 'best_seller', 'top_rated', 'on_sale'] as $tag)
                    @if($product->{"is_$tag"})
                        <span class="px-2 py-1 text-xs font-semibold text-white bg-blue-500 rounded-full">{{ ucwords(str_replace('_', ' ', $tag)) }}</span>
                    @endif
                @endforeach
            </div>
            
            <div class="border-t pt-4">
                <p class="text-sm text-gray-500">Added on {{ $product->created_at->format('F j, Y') }}</p>
                <p class="text-sm text-gray-500">Last updated {{ $product->updated_at->format('F j, Y') }}</p>
            </div>

            <!-- Add to Cart and Pay with GCash buttons -->
            <div class="flex space-x-4 mt-6">
                @auth
                    <button wire:click="addToCart({{ $product->id }})" 
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300 ease-in-out transform hover:scale-105">
                        <span wire:loading.remove wire:target="addToCart">Add to Cart</span>
                        <span wire:loading wire:target="addToCart">Adding...</span>
                    </button>
                    <button wire:click="payWithGCash({{ $product->id }})" 
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300 ease-in-out transform hover:scale-105">
                        <span wire:loading.remove wire:target="payWithGCash">Pay with GCash</span>
                        <span wire:loading wire:target="payWithGCash">Processing...</span>
                    </button>
                @else
                    <a href="{{ route('login') }}" 
                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg text-center transition duration-300 ease-in-out transform hover:scale-105">
                        Login to Add to Cart
                    </a>
                    <a href="{{ route('login') }}" 
                       class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg text-center transition duration-300 ease-in-out transform hover:scale-105">
                        Login to Pay with GCash
                    </a>
                @endauth
            </div>

            @guest
                <p class="mt-4 text-red-500 text-center">Please <a href="{{ route('login') }}" class="underline">login</a> to add items to your cart or make a purchase.</p>
            @endguest
        </div>
    </div>
</div>
