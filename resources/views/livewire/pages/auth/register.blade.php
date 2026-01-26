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
    public bool $showPassword = false;
    public bool $terms = false;

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'terms' => ['accepted'],
        ], [
            'terms.accepted' => 'You must accept the terms and conditions.'
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="w-full">
    <form wire:submit="register" class="space-y-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5" for="name">Full Name</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">person</span>
                    <x-text-input wire:model="name" id="name" 
                        class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all text-slate-900 dark:text-white" 
                        type="text" 
                        name="name" 
                        required 
                        autofocus 
                        autocomplete="name" 
                        placeholder="John Doe" />
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5" for="email">Email Address</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">mail</span>
                    <x-text-input wire:model="email" id="email" 
                        class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all text-slate-900 dark:text-white"
                        type="email" 
                        name="email" 
                        required 
                        autocomplete="email" 
                        placeholder="name@company.com" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5" for="password">Password</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">lock</span>
                    <x-text-input wire:model="password" id="password" 
                        class="w-full pl-11 pr-11 py-3 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all text-slate-900 dark:text-white"
                        :type="$showPassword ? 'text' : 'password'"
                        name="password"
                        required 
                        autocomplete="new-password" 
                        placeholder="••••••••" />
                    <button type="button" 
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" 
                        wire:click="$set('showPassword', !$showPassword)">
                        <span class="material-symbols-outlined text-[20px]">
                            {{ $showPassword ? 'visibility_off' : 'visibility' }}
                        </span>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5" for="password_confirmation">Confirm Password</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">lock_reset</span>
                    <x-text-input wire:model="password_confirmation" id="password_confirmation" 
                        class="w-full pl-11 pr-11 py-3 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all text-slate-900 dark:text-white"
                        :type="$showPassword ? 'text' : 'password'"
                        name="password_confirmation" 
                        required 
                        autocomplete="new-password" 
                        placeholder="••••••••" />
                    <button type="button" 
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" 
                        wire:click="$set('showPassword', !$showPassword)">
                        <span class="material-symbols-outlined text-[20px]">
                            {{ $showPassword ? 'visibility_off' : 'visibility' }}
                        </span>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input id="terms" name="terms" type="checkbox" 
                    wire:model.live="terms"
                    class="h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary dark:border-slate-600 dark:bg-slate-800"
                    required>
            </div>
            <div class="ml-3 text-sm">
                <label for="terms" class="font-medium text-slate-700 dark:text-slate-300">
                    I agree to the <a href="#" class="text-primary hover:underline">Terms of Service</a> and <a href="#" class="text-primary hover:underline">Privacy Policy</a>
                </label>
                <x-input-error :messages="$errors->get('terms')" class="mt-1" />
            </div>
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3.5 px-4 rounded-xl shadow-lg shadow-blue-500/20 transition-all transform hover:-translate-y-0.5 active:translate-y-0">
            CREATE ACCOUNT
        </button>

        <div class="relative flex items-center py-2">
            <div class="flex-grow border-t border-slate-200 dark:border-slate-800"></div>
            <span class="flex-shrink mx-4 text-slate-400 text-xs font-medium uppercase tracking-widest">or sign up with</span>
            <div class="flex-grow border-t border-slate-200 dark:border-slate-800"></div>
        </div>

        <button type="button" class="w-full flex items-center justify-center gap-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-750 text-slate-700 dark:text-slate-200 font-medium py-3 rounded-xl transition-all">
            <img alt="Google Logo" class="w-5 h-5" src="https://www.google.com/favicon.ico">
            SIGN UP WITH GOOGLE
        </button>

        <div class="pt-2 text-center">
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-primary font-semibold hover:underline" wire:navigate>
                    Sign in
                </a>
            </p>
        </div>
    </form>
</div>