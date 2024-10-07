<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<section>
    <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
        <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
            {{ __('Sign in to your account') }}
        </h1>
        <x-auth-session-status class="mb-4" :status="session('status')" />
        <form class="space-y-4 md:space-y-6" wire:submit="login">
            <div>
                <x-input-label for="email" :value="__('Your email')" />
                <x-text-input wire:model="form.email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" type="email" name="email" required autofocus autocomplete="username" placeholder="name@company.com" />
                <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input wire:model="form.password" id="password" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="remember" name="remember" type="checkbox" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-primary-600 dark:ring-offset-gray-800">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="remember" class="text-gray-500 dark:text-gray-300">{{ __('Remember me') }}</label>
                    </div>
                </div>
                @if (Route::has('password.request'))
                <a class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot password?') }}
                </a>
                @endif
            </div>
            <x-primary-button class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                {{ __('Sign in') }}
            </x-primary-button>
            @if (Route::has('register'))
            <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                {{ __("Don't have an account yet?") }} <a href="{{ route('register') }}" class="font-medium text-primary-600 hover:underline dark:text-primary-500" wire:navigate>{{ __('Sign up') }}</a>
            </p>
            @endif
        </form>
    </div>
</section>