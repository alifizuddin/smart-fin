<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;
    public bool $remember = false;
    public bool $showPassword = false;

    public function login(): void
    {
        $this->validate();
        $this->form->authenticate($this->remember);
        Session::regenerate();
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="w-full">
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form wire:submit="login" class="space-y-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5" for="email">Email Address</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">mail</span>
                <x-text-input wire:model="form.email" id="email" 
                    class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all text-slate-900 dark:text-white" 
                    type="email" 
                    name="email" 
                    required 
                    autofocus 
                    autocomplete="username" 
                    placeholder="name@company.com" />
            </div>
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="password">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-sm font-medium text-primary hover:underline" href="{{ route('password.request') }}" wire:navigate>
                        Forgot password?
                    </a>
                @endif
            </div>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">lock</span>
                <x-text-input wire:model="form.password" id="password" 
                    class="w-full pl-11 pr-11 py-3 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all text-slate-900 dark:text-white"
                    :type="$showPassword ? 'text' : 'password'"
                    name="password"
                    required 
                    autocomplete="current-password" 
                    placeholder="••••••••" />
                <button type="button" 
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" 
                    wire:click="$set('showPassword', !$showPassword)">
                    <span class="material-symbols-outlined text-[20px]">
                        {{ $showPassword ? 'visibility_off' : 'visibility' }}
                    </span>
                </button>
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" class="sr-only peer" wire:model.live="remember">
                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 dark:peer-focus:ring-primary/10 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
                <span class="ml-3 text-sm font-medium text-slate-600 dark:text-slate-400">Remember me</span>
            </label>
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3.5 px-4 rounded-xl shadow-lg shadow-blue-500/20 transition-all transform hover:-translate-y-0.5 active:translate-y-0">
            SIGN IN TO YOUR ACCOUNT
        </button>

        <div class="relative flex items-center py-2">
            <div class="flex-grow border-t border-slate-200 dark:border-slate-800"></div>
            <span class="flex-shrink mx-4 text-slate-400 text-xs font-medium uppercase tracking-widest">or continue with</span>
            <div class="flex-grow border-t border-slate-200 dark:border-slate-800"></div>
        </div>

        <button type="button" class="w-full flex items-center justify-center gap-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-750 text-slate-700 dark:text-slate-200 font-medium py-3 rounded-xl transition-all">
            <img alt="Google Logo" class="w-5 h-5" src="https://www.google.com/favicon.ico">
            SIGN IN WITH GOOGLE
        </button>

        <div class="pt-6 space-y-4 text-center">
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Don't have an account? 
                <a href="{{ route('register') }}" class="text-primary font-semibold hover:underline" wire:navigate>
                    Create account
                </a>
            </p>
        </div>
    </form>
</div>