<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('Department Management') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Manage departments, organize users, budgets, and expenses effectively.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <div class="relative overflow-x-auto shadow-lg sm:rounded-xl border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between flex-wrap md:flex-nowrap gap-4 p-4 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-2">
                                {{-- ADD DEPARTMENT BUTTON --}}
                                <a href="{{ route('departments.create') }}" class="inline-flex items-center text-white bg-indigo-600 border border-transparent focus:outline-none hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 font-medium rounded-lg text-sm px-4 py-1.5 shadow-sm transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Add Department
                                </a>
                            </div>

                            {{-- Search Bar --}}
                            <div class="relative w-full md:w-80">
                                <input type="text" id="table-search-departments"
                                    class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-200"
                                    placeholder="Search for departments...">
                                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Table --}}
                        <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                            <thead class="text-xs uppercase bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 sticky top-0 border-b border-gray-200 dark:border-gray-600">
                                <tr>
                                    <th class="px-6 py-3">Name</th>
                                    <th class="px-6 py-3">Description</th>
                                    <th class="px-6 py-3">Users</th>
                                    <th class="px-6 py-3">Budgets</th>
                                    <th class="px-6 py-3">Expenses</th>
                                    <th class="px-6 py-3 text-right">Action</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($departments as $department)
                                    <tr class="bg-white dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-medium text-gray-900 dark:text-white">
                                                {{ $department->name }}
                                            </div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
                                                {{ $department->description ?? 'No description' }}
                                            </div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                {{ $department->users_count }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ $department->budgets_count }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                                {{ $department->expenses_count }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 text-right space-x-3 flex justify-end">
                                            <a href="{{ route('departments.show', $department) }}"
                                                class="p-1.5 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300 rounded-full hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                     viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>

                                            <a href="{{ route('departments.edit', $department) }}"
                                                class="p-1.5 text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 rounded-full hover:bg-blue-50 dark:hover:bg-gray-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                     viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                </svg>
                                            </a>

                                            <form action="{{ route('departments.destroy', $department) }}" method="POST"
                                                  onsubmit="event.preventDefault(); confirmAction('{{ route('departments.destroy', $department) }}', 'delete', 'confirmationModal', '{{ $department->name }}');">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                    class="p-1.5 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 rounded-full hover:bg-red-50 dark:hover:bg-gray-700">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />

</x-app-layout>