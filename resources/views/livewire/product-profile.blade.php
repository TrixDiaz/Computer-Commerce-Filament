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
            <p class="text-xl font-semibold mb-2">${{ number_format($product->price, 2) }}</p>
            <p class="text-gray-600 mb-4">{{ $product->description }}</p>
        </div>
    </div>
</div>