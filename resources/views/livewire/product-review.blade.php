<div>
    <section class="bg-white p-8 antialiased dark:bg-gray-900">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <div class="mx-auto max-w-5xl">
                <div class="gap-4 sm:flex sm:items-center sm:justify-between">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Product Reviews</h2>
                    <!-- <div class="mt-6 sm:mt-0">
                        <label for="order-type" class="sr-only mb-2 block text-sm font-medium text-gray-900 dark:text-white">Select review type</label>
                        <select id="order-type" class="block w-full min-w-[8rem] rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500">
                            <option selected>All reviews</option>
                            <option value="5">5 stars</option>
                            <option value="4">4 stars</option>
                            <option value="3">3 stars</option>
                            <option value="2">2 stars</option>
                            <option value="1">1 star</option>
                        </select>
                    </div> -->
                </div>

                <div class="mt-6 flow-root sm:mt-8">
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($reviews as $review)
                            <div class="grid md:grid-cols-12 gap-4 md:gap-6 pb-4 md:pb-6">
                                <dl class="md:col-span-3 order-3 md:order-1">
                                    <dt class="sr-only">User:</dt>
                                    <dd class="text-base font-semibold text-gray-900 dark:text-white">
                                        {{ $review->user->name }}
                                    </dd>
                                </dl>

                                <dl class="md:col-span-6 order-4 md:order-2">
                                    <dt class="sr-only">Comment:</dt>
                                    <dd class="text-gray-500 dark:text-gray-400">{{ $review->comment }}</dd>
                                </dl>

                                <div class="md:col-span-3 content-center order-1 md:order-3 flex items-center justify-between">
                                    <dl>
                                        <dt class="sr-only">Rating:</dt>
                                        <dd class="flex items-center space-x-1">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z"></path>
                                                </svg>
                                            @endfor
                                        </dd>
                                    </dl>
                                    <!-- <button id="actionsMenuDropdown1" data-dropdown-toggle="dropdownOrder1" type="button" class="inline-flex h-7 w-7 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                        <span class="sr-only"> Actions </span>
                                        <svg class="h-6 w-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-width="4" d="M6 12h.01m6 0h.01m5.99 0h.01"></path>
                                        </svg>
                                    </button>
                                    <div id="dropdownOrder1" class="z-10 hidden w-40 divide-y divide-gray-100 rounded-lg bg-white shadow dark:bg-gray-700" data-popper-placement="bottom">
                                        <ul class="p-2 text-left text-sm font-medium text-gray-500 dark:text-gray-400" aria-labelledby="actionsMenuDropdown1">
                                            <li>
                                                <button type="button" data-modal-target="editReviewModal" data-modal-toggle="editReviewModal" class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                                                    <svg class="me-1.5 h-4 w-4 text-gray-400 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />
                                                    </svg>
                                                    <span>Edit review</span>
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" data-modal-target="deleteReviewModal" data-modal-toggle="deleteReviewModal" class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-red-500">
                                                    <svg class="me-1.5 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"></path>
                                                    </svg>
                                                    Delete review
                                                </button>
                                            </li>
                                        </ul>
                                    </div> -->
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400">No reviews yet for this product.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
