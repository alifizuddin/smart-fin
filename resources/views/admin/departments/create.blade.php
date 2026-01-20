<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('Create Department') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Create a new department for organizing users and budgets.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
                <div class="p-8 sm:p-10">

                    <div class="text-center mb-8">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                            {{ __('Add New Department') }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Create a new department for organizing users and budgets.
                        </p>
                    </div>

                    <form action="{{ route('departments.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="name" :value="__('Department Name')" class="text-gray-700 dark:text-gray-300" />
                            <x-text-input
                                id="name"
                                class="block mt-2 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                type="text"
                                name="name"
                                :value="old('name')"
                                required
                                autofocus
                                placeholder="Enter department name"
                            />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" class="text-gray-700 dark:text-gray-300" />
                            <textarea
                                id="description"
                                name="description"
                                rows="4"
                                class="block mt-2 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                placeholder="Enter department description (optional)"
                            >{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-8 space-x-4">
                            <a href="{{ route('departments.index') }}"
                                class="inline-flex items-center px-5 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button class="px-6 py-2.5 text-sm rounded-lg">
                                {{ __('Create Department') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>