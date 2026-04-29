<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="font-semibold mb-2">Welcome, {{ Auth::user()->name }}.</p>
                    <p>Role: <span class="font-mono">{{ Auth::user()->role }}</span></p>
                    <p class="mt-4 text-sm text-gray-600">Phase 2 placeholder — admin features arrive in Phase 6.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
