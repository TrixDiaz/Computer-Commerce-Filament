<div class="py-8 px-4 mx-auto max-w-screen-lg relative">
    <h1 class="text-2xl font-bold mb-4">Featured Products</h1>
    <div x-data="carousel()"
        x-init="init()"
        class="relative w-full overflow-hidden">
        <div class="flex transition-transform duration-300 ease-in-out"
            :style="{ transform: `translateX(-${currentIndex * 100}%)` }">
            @foreach($products->chunk(4) as $chunk)
            <div class="w-full flex-shrink-0">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 h-full">
                    @foreach($chunk as $index => $product)
                    <div class="relative group {{ $index >= 2 ? 'hidden lg:block' : '' }} flex justify-center items-center">
                        <a href="{{ route('product-profile', ['slug' => $product->slug]) }}" class="w-full h-full flex justify-center items-center">
                            <div class="h-56 w-full relative group">
                                @php
                                $defaultImage = '/images/laptop-image.png';
                                $defaultHoverImage = '/images/hover-image.png';
                                $images = $product->images ?? [];
                                $mainImage = !empty($images) ? $images[array_rand($images)] : $defaultImage;
                                $hoverImage = !empty($images) ? $images[array_rand($images)] : $defaultHoverImage;
                                // Ensure hover image is different from main image if possible
                                while (!empty($images) && count($images) > 1 && $hoverImage === $mainImage) {
                                    $hoverImage = $images[array_rand($images)];
                                }
                                @endphp
                                <img
                                    class="mx-auto h-full w-full object-cover transition-opacity duration-300 ease-in-out group-hover:opacity-0"
                                    src="{{ !empty($images) ? Storage::url($mainImage) : asset($defaultImage) }}"
                                    alt="{{ $product->name }}" />
                                <img
                                    class="mx-auto h-full w-full object-cover absolute top-0 left-0 opacity-0 transition-opacity duration-300 ease-in-out group-hover:opacity-100"
                                    src="{{ !empty($images) ? Storage::url($hoverImage) : asset($defaultHoverImage) }}"
                                    alt="{{ $product->name }} - Hover" />
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white p-2">
                                <p class="text-sm font-bold">{{ $product->name }}</p>
                                <p class="text-xs">₱{{ number_format($product->price, 2) }}</p>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        <!-- Slider controls -->
        <button @click="prev()" class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-slate-400 dark:bg-gray-800 group-hover:bg-slate-400/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-slate-500 dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                <svg class="w-4 h-4 text-white dark:text-gray-800 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4" />
                </svg>
                <span class="sr-only">Previous</span>
            </span>
        </button>
        <button @click="next()" class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-slate-400 dark:bg-gray-800 group-hover:bg-slate-400/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-slate-500 dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                <svg class="w-4 h-4 text-white dark:text-gray-800 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                </svg>
                <span class="sr-only">Next</span>
            </span>
        </button>
    </div>
</div>

<script>
    function carousel() {
        return {
            currentIndex: 0,
            items: [],
            init() {
                this.items = Array.from(this.$el.querySelectorAll('.flex-shrink-0'));
            },
            next() {
                this.currentIndex = (this.currentIndex + 1) % this.items.length;
            },
            prev() {
                this.currentIndex = (this.currentIndex - 1 + this.items.length) % this.items.length;
            }
        }
    }
</script>
