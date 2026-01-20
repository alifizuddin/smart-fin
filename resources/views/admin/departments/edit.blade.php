<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Edit Department') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
                <div class="p-8 sm:p-10">

                    <!-- Header Section -->
                    <div class="text-center mb-8">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                            {{ __('Update Department Information') }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Modify department details and description.
                        </p>
                    </div>

                    <!-- Form Section -->
                    <form action="{{ route('departments.update', $department) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Department Name')" class="text-gray-700 dark:text-gray-300" />
                            <x-text-input
                                id="name"
                                class="block mt-2 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                type="text"
                                name="name"
                                :value="old('name', $department->name)"
                                required
                                autofocus
                                placeholder="Enter department name"
                            />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description')" class="text-gray-700 dark:text-gray-300" />
                            <textarea
                                id="description"
                                name="description"
                                rows="4"
                                class="block mt-2 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                placeholder="Enter department description (optional)"
                            >{{ old('description', $department->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end mt-8 space-x-4">
                            <a href="{{ route('departments.index') }}"
                               class="inline-flex items-center px-5 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button class="px-6 py-2.5 text-sm rounded-lg">
                                {{ __('Update Department') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>