<x-app-layout>
    <x-slot name="header">
        <h2>Parent Dashboard</h2>
    </x-slot>

    <div class="p-6">
        <p>Welcome, {{ auth()->user()->name }} (Role: {{ auth()->user()->role }})</p>
    </div>
</x-app-layout>
