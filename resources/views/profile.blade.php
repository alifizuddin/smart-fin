<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('Profile') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Manage your account settings and profile information
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-950">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

            {{-- Profile Overview --}}
            <div class="bg-white dark:bg-gray-900 shadow-xl rounded-3xl p-10 border border-gray-200 dark:border-gray-700 
                        animate-fadeIn relative overflow-hidden">

                {{-- Subtle gradient highlight --}}
                <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-r from-indigo-600/15 to-purple-600/15
                            dark:from-indigo-500/10 dark:to-purple-500/10 pointer-events-none"></div>

                <div class="relative flex flex-col items-center text-center">
                    {{-- Profile Image --}}
                    <img class="h-32 w-32 rounded-full object-cover ring-4 ring-indigo-500/30 shadow-lg" 
                        src="{{ Auth::user()->profile_photo_url }}" 
                        alt="{{ Auth::user()->name }}">
                    
                    {{-- Name & Email --}}
                    <h3 class="mt-5 text-2xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ Auth::user()->name }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ Auth::user()->email }}
                    </p>

                    {{-- Role Tags --}}
                    <div class="mt-4 flex flex-wrap gap-2 justify-center">
                        @forelse (Auth::user()->getRoleNames() as $role)
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800
                                        dark:bg-indigo-700 dark:text-indigo-100 shadow-sm">
                                {{ $role }}
                            </span>
                        @empty
                            <span class="text-gray-500 dark:text-gray-400 text-sm">No roles assigned.</span>
                        @endforelse
                    </div>

                    {{-- Info Grid --}}
                    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4 w-full max-w-lg">
                        <div class="flex justify-between text-sm py-3 border-b border-gray-200 dark:border-gray-700">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Department</span>
                            <span class="text-gray-900 dark:text-gray-200">
                                {{ Auth::user()->department->name ?? 'N/A' }}
                            </span>
                        </div>
                        <div class="flex justify-between text-sm py-3 border-b border-gray-200 dark:border-gray-700">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Member Since</span>
                            <span class="text-gray-900 dark:text-gray-200">
                                {{ Auth::user()->created_at->format('F d, Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Update Profile Information --}}
            <div class="bg-white dark:bg-gray-900 shadow-lg rounded-3xl p-8 border border-gray-200 dark:border-gray-700 animate-fadeIn">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ __('Update Profile Information') }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Update your name, email address, or profile photo.
                    </p>
                </div>
                <div class="max-w-xl mx-auto">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            {{-- Update Password --}}
            <div class="bg-white dark:bg-gray-900 shadow-lg rounded-3xl p-8 border border-gray-200 dark:border-gray-700 animate-fadeIn">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ __('Change Password') }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Keep your account secure with a strong password.
                    </p>
                </div>
                <div class="max-w-xl mx-auto">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="bg-white dark:bg-gray-900 shadow-lg rounded-3xl p-8 border border-red-600/40 dark:border-red-600/50 animate-fadeIn">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-semibold text-red-600 dark:text-red-400">
                        {{ __('Danger Zone') }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Permanently delete your account. This action cannot be undone.
                    </p>
                </div>
                <div class="max-w-xl mx-auto">
                    <livewire:profile.delete-user-form />
                </div>
            </div>

        </div>
    </div>

    {{-- Simple fade animation --}}
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn .4s ease-out; }
    </style>

</x-app-layout>
