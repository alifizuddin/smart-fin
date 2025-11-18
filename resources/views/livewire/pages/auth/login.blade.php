<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        <div class="mb-4">
            <label class="block font-medium text-sm text-gray-700 mb-1" for="email">Email address</label>
            <x-text-input wire:model="form.email" id="email" class="block w-full px-4 py-3 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" type="email" name="email" required autofocus autocomplete="username" placeholder="Enter your email" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <div class="mb-4">
            <label class="block font-medium text-sm text-gray-700 mb-1" for="password">Password</label>
            <x-text-input wire:model="form.password" id="password" class="block w-full px-4 py-3 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                            type="password"
                            name="password"
                            required autocomplete="current-password" 
                            placeholder="Enter your password" />
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <div class="pt-1 mb-4">
            <button type="submit" class="w-full bg-indigo-900 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-900 transition ease-in-out duration-150 text-lg">
                {{ __('Login') }}
            </button>
        </div>

        <div class="flex flex-col gap-2">
            @if (Route::has('password.request'))
                <a class="text-sm text-gray-500 hover:text-gray-900" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot password?') }}
                </a>
            @endif

            @if (Route::has('register'))
                <p class="text-gray-600" style="color: #393f81;">
                    Don't have an account? 
                    <a href="{{ route('register') }}" wire:navigate class="font-bold hover:underline" style="color: #393f81;">
                        Register here
                    </a>
                </p>
            @endif
        </div>
    </form>
</div>