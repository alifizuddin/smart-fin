<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Edit User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
                <div class="p-8 sm:p-10">

                    <!-- Header Section -->
                    <div class="text-center mb-8">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                            {{ __('Update User Information') }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Modify user details, roles, or access permissions.
                        </p>
                    </div>

                    <!-- Form Section -->
                    <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Name')" class="text-gray-700 dark:text-gray-300" />
                            <x-text-input 
                                id="name" 
                                class="block mt-2 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" 
                                type="text" 
                                name="name" 
                                :value="old('name', $user->name)" 
                                required 
                                autofocus 
                            />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div>
                            <x-input-label for="email" :value="__('Email')" class="text-gray-700 dark:text-gray-300" />
                            <x-text-input 
                                id="email" 
                                class="block mt-2 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" 
                                type="email" 
                                name="email" 
                                :value="old('email', $user->email)" 
                                required 
                            />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <div class="mb-4">
                            <x-input-label for="department_id" class="text-gray-700 dark:text-gray-300">Department</x-input-label>
                            <select name="department_id" id="department_id" class="block mt-2 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" {{ $user->department_id == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Roles -->
                        <div>
                            <x-input-label for="roles" :value="__('Assign Roles')" class="text-gray-700 dark:text-gray-300" />
                            <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                @foreach ($roles as $role)
                                    <label for="role_{{ $role->id }}" class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <input 
                                            id="role_{{ $role->id }}" 
                                            name="roles[]" 
                                            type="checkbox" 
                                            value="{{ $role->name }}"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600"
                                            {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $role->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end mt-8 space-x-4">
                            <a href="{{ route('users.index') }}" 
                               class="inline-flex items-center px-5 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button class="px-6 py-2.5 text-sm rounded-lg">
                                {{ __('Update User') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
