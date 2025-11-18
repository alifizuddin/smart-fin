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
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <form wire:submit="register">
        <div class="mb-4">
            <label class="block font-medium text-sm text-gray-700 mb-1" for="name">Name</label>
            <x-text-input wire:model="name" id="name" class="block w-full px-4 py-3 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mb-4">
            <label class="block font-medium text-sm text-gray-700 mb-1" for="email">Email</label>
            <x-text-input wire:model="email" id="email" class="block w-full px-4 py-3 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-4">
            <label class="block font-medium text-sm text-gray-700 mb-1" for="password">Password</label>
            <x-text-input wire:model="password" id="password" class="block w-full px-4 py-3 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mb-4">
            <label class="block font-medium text-sm text-gray-700 mb-1" for="password_confirmation">Confirm Password</label>
            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block w-full px-4 py-3 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-1 mb-4">
            <button type="submit" class="w-full bg-indigo-900 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-900 transition ease-in-out duration-150 text-lg">
                {{ __('Register') }}
            </button>
        </div>

        <div class="flex items-center justify-start pb-4">
            <p class="mb-0 mr-2 text-gray-600" style="color: #393f81;">Already have an account?</p>
            <a href="{{ route('login') }}" wire:navigate class="font-bold hover:underline" style="color: #393f81;">
                {{ __('Login here') }}
            </a>
        </div>
    </form>
</div>