<div class="py-8 px-4 mx-auto max-w-screen-xl">
    <h1 class="text-3xl font-bold mb-4">{{ $product->name }}</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
            <img src="{{ $product->image_url ?? 'https://placehold.co/600x400' }}" alt="{{ $product->name }}" class="w-full h-auto rounded-lg shadow-md">
        </div>
        <div>
            <p class="text-xl font-semibold mb-2">${{ number_format($product->price, 2) }}</p>
            <p class="text-gray-600 mb-4">{{ $product->description }}</p>
        </div>
    </div>
</div>