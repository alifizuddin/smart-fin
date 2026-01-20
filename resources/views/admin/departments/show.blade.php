<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Department Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('departments.edit', $department) }}" class="inline-flex items-center text-white bg-indigo-600 border border-transparent focus:outline-none hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 font-medium rounded-lg text-sm px-4 py-2 shadow-sm transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    Edit Department
                </a>
                <a href="{{ route('departments.index') }}" class="inline-flex items-center text-gray-700 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-2 focus:ring-indigo-200 font-medium rounded-lg text-sm px-4 py-2 shadow-sm transition dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Departments
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Department Info Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $department->name }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $department->description ?? 'No description provided' }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Created</div>
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $department->created_at ? $department->created_at->format('M d, Y') : 'N/A' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="p-6 bg-gray-50 dark:bg-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $department->users->count() }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">Staff Members</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $department->budgets->count() }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">Active Budgets</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ $department->expenses->count() }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">Total Expenses</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Staff Members Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Staff Members</h3>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Users assigned to this department</p>
                </div>

                <div class="p-6">
                    @if($department->users->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                                <thead class="text-xs uppercase bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                                    <tr>
                                        <th class="px-6 py-3">Name</th>
                                        <th class="px-6 py-3">Email</th>
                                        <th class="px-6 py-3">Roles</th>
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3">Joined</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($department->users as $user)
                                        <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <img class="h-10 w-10 rounded-full object-cover shadow-sm"
                                                        src="{{ $user->profile_photo_url }}"
                                                        alt="{{ $user->name }}">
                                                    <div class="ml-4">
                                                        <div class="font-medium text-gray-900 dark:text-white">
                                                            {{ $user->name }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $user->email }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($user->roles as $role)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                            @if($role->name === 'Admin') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                            @elseif($role->name === 'Finance Admin') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                                            @elseif($role->name === 'Manager') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                                            @endif">
                                                            {{ $role->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($user->status === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                    @endif">
                                                    {{ ucfirst($user->status ?? 'active') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                {{ $user->created_at?->format('M d, Y') ?? 'N/A' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No staff members</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This department doesn't have any assigned staff members yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>