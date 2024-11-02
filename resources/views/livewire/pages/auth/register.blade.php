<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $terms = false;

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'terms' => ['accepted'],
        ]);

        $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false));
    }
}; ?>

<section>
    <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
        <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
            Create an account
        </h1>
        <form wire:submit="register" class="space-y-4 md:space-y-6">
            <div>
                <label for="first_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">First name</label>
                <input wire:model="first_name" type="text" name="first_name" id="first_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="John" required autofocus autocomplete="given-name">
                <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
            </div>
            <div>
                <label for="last_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Last name</label>
                <input wire:model="last_name" type="text" name="last_name" id="last_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Doe" required autocomplete="family-name">
                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
            </div>
            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your email</label>
                <input wire:model="email" type="email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="name@company.com" required autocomplete="username">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div x-data="{ showPassword: false }">
                <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                <div class="relative">
                    <input wire:model="password" :type="showPassword ? 'text' : 'password'" name="password" id="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required autocomplete="new-password">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <svg x-on:click="showPassword = !showPassword" :class="{'hidden': showPassword, 'block': !showPassword }" class="h-5 w-5 mr-2 text-gray-500 cursor-pointer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                        </svg>
                        <svg x-on:click="showPassword = !showPassword" :class="{'block': showPassword, 'hidden': !showPassword }" class="h-5 w-5 mr-2 text-gray-500 cursor-pointer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                            <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                        </svg>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div x-data="{ showConfirmPassword: false }">
                <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Confirm password</label>
                <div class="relative">
                    <input wire:model="password_confirmation" :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required autocomplete="new-password">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg x-on:click="showConfirmPassword = !showConfirmPassword" :class="{'hidden': showConfirmPassword, 'block': !showConfirmPassword }" class="h-5 w-5 mr-2 text-gray-500 cursor-pointer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                            </svg>
                            <svg x-on:click="showConfirmPassword = !showConfirmPassword" :class="{'block': showConfirmPassword, 'hidden': !showConfirmPassword }" class="h-5 w-5 mr-2  text-gray-500 cursor-pointer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                                <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                            </svg>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input wire:model="terms" id="terms" aria-describedby="terms" type="checkbox" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600 dark:ring-offset-gray-800" required>
                </div>
                <div class="ml-3 text-sm">
                    <label for="terms" class="font-light text-gray-500 dark:text-gray-300">I accept the <a class="font-medium text-blue-600 hover:underline dark:text-blue-500" href="{{ route('privacy') }}">Privacy Policy</a>
                        and
                        <a class="font-medium text-blue-600 hover:underline dark:text-blue-500" href="{{ route('terms') }}">Terms Conditions</a></label>
                </div>
            </div>
            <x-input-error :messages="$errors->get('terms')" class="mt-2" />
            <x-primary-button
                type="submit"
                id="submit-button"
                class="w-full text-white font-medium rounded-lg text-sm px-5 py-2.5 text-center focus:outline-none transition-colors duration-200"
                :class="$terms 
                    ? 'bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 w-full' 
                    : 'bg-gray-400 cursor-not-allowed w-full'"
                :disabled="!$terms"
                wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('Create an account') }}</span>
                <span wire:loading>Processing...</span>
            </x-primary-button>
            <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                Already have an account? <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:underline dark:text-blue-500">Login here</a>
            </p>
        </form>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const termsCheckbox = document.getElementById('terms');
        const submitButton = document.getElementById('submit-button');

        termsCheckbox.addEventListener('change', function() {
            submitButton.disabled = !this.checked;
            if (this.checked) {
                submitButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
                submitButton.classList.add('bg-blue-600', 'hover:bg-blue-700');
            } else {
                submitButton.classList.add('bg-gray-400', 'cursor-not-allowed');
                submitButton.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            }
        });
    });
</script>