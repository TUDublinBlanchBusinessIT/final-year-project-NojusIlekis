<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                    New Message
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    Choose a carer to start a conversation with.
                </p>
            </div>
            <a href="{{ route('parent.messages.index') }}"
               class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100">
                ← Back to Messages
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if ($carers->isEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-8 text-center text-slate-500">
                    No carers are currently assigned to your children's rooms.
                </div>
            @else
                @foreach ($carers as $carer)
                    <a href="{{ route('parent.messages.show', $carer->id) }}"
                       class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-shadow px-5 py-4">
                        <div>
                            <p class="font-medium text-slate-900">{{ $carer->name }}</p>
                            @if ($carer->activeRooms->isNotEmpty())
                                <p class="text-sm text-slate-500 mt-0.5">
                                    {{ $carer->activeRooms->pluck('name')->join(', ') }}
                                </p>
                            @endif
                        </div>
                        <span class="text-sm text-blue-600 font-medium">Message →</span>
                    </a>
                @endforeach
            @endif

        </div>
    </div>
</x-app-layout>
