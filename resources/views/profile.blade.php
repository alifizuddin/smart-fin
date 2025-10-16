<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Profile') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Profile Overview --}}
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-8 flex flex-col items-center text-center border border-gray-100 dark:border-gray-700">
                {{-- Profile Image --}}
                <img class="h-28 w-28 rounded-full object-cover ring-4 ring-indigo-500/30 shadow-md" 
                     src="{{ Auth::user()->profile_photo_url }}" 
                     alt="{{ Auth::user()->name }}">
                
                {{-- Name & Email --}}
                <h3 class="mt-4 text-xl font-semibold text-gray-900 dark:text-gray-100">
                    {{ Auth::user()->name }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ Auth::user()->email }}
                </p>

                {{-- Role Tags --}}
                <div class="mt-4">
                    @forelse (Auth::user()->getRoleNames() as $role)
                        <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                            {{ $role }}
                        </span>
                    @empty
                        <span class="text-gray-500 dark:text-gray-400 text-sm">No roles assigned.</span>
                    @endforelse
                </div>

                {{-- Basic Info --}}
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-700 dark:text-gray-300 w-full max-w-md">
                    <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                        <span class="font-medium">{{ __('Department') }}</span>
                        <span>{{ Auth::user()->department->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                        <span class="font-medium">{{ __('Member Since') }}</span>
                        <span>{{ Auth::user()->created_at->format('F d, Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- Update Profile Information --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl p-8 border border-gray-100 dark:border-gray-700">
                <h3 class="text-center text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    {{ __('Update Profile Information') }}
                </h3>
                <p class="text-center text-sm text-gray-500 dark:text-gray-400 mb-6">
                    Update your name, email address, or profile photo.
                </p>
                <div class="max-w-xl mx-auto">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            {{-- Update Password --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl p-8 border border-gray-100 dark:border-gray-700">
                <h3 class="text-center text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    {{ __('Change Password') }}
                </h3>
                <p class="text-center text-sm text-gray-500 dark:text-gray-400 mb-6">
                    Ensure your account is secure by using a strong, unique password.
                </p>
                <div class="max-w-xl mx-auto">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl p-8 border-l-4 border-red-600 border border-gray-100 dark:border-gray-700">
                <h3 class="text-center text-lg font-semibold text-red-600 dark:text-red-400 mb-4">
                    {{ __('Danger Zone') }}
                </h3>
                <p class="text-center text-sm text-gray-500 dark:text-gray-400 mb-6">
                    Permanently delete your account. This action cannot be undone.
                </p>
                <div class="max-w-xl mx-auto">
                    <livewire:profile.delete-user-form />
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
