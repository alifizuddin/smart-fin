<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Profile Information Card --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    {{-- This component handles Name and Email updates --}}
                    <livewire:profile.update-profile-information-form />

                    <hr class="my-6 border-gray-200 dark:border-gray-700"/>

                    {{-- Additional, non-editable information --}}
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __("Account Information") }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Additional details about your account.
                        </p>
                        <div class="mt-4 space-y-3">
<div class="text-sm text-gray-800 dark:text-gray-200 flex items-center">
    <strong class="w-24 inline-block">{{ __("Roles") }}:</strong>
    <div>
        @forelse (Auth::user()->getRoleNames() as $role)
            <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                {{ $role }}
            </span>
        @empty
            <span class="ml-2 text-gray-500">No roles assigned.</span>
        @endforelse
    </div>
</div>
                            <p class="text-sm text-gray-800 dark:text-gray-200">
                                <strong class="w-24 inline-block">{{ __("Department") }}:</strong> 
                                {{ Auth::user()->department->name ?? 'N/A' }}
                            </p>
                             <p class="text-sm text-gray-800 dark:text-gray-200">
                                <strong class="w-24 inline-block">{{ __("Member Since") }}:</strong> 
                                {{ Auth::user()->created_at->format('F d, Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Update Password Card --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            {{-- Danger Zone: Delete Account Card --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg border-l-4 border-red-500">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

