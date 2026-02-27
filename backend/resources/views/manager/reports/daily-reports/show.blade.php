<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                    {{ $dailyReport->child->first_name }} {{ $dailyReport->child->last_name }} — Daily Report
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    Room: {{ $dailyReport->child->room->name ?? '—' }} · Date: {{ $dailyReport->date }} ·
                    Submitted at: {{ $dailyReport->created_at->format('d/m/Y H:i:s') }}
                    · By: {{ $dailyReport->carer->name ?? '—' }}
                </p>
            </div>

            <a href="{{ route('manager.reports.daily-reports.index', ['date' => $dailyReport->date, 'room_id' => $dailyReport->child->room_id]) }}"
               class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50
                      dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700">
                ← Back to list
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Report text --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950/40">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">End of day report</h3>
                </div>
                <div class="p-5 text-slate-900 dark:text-slate-100 whitespace-pre-wrap">
                    {{ $dailyReport->daily_report }}
                </div>
            </div>

            {{-- Attachments --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950/40">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">
                        Attachments ({{ $dailyReport->mediaUpdates->count() }})
                    </h3>
                </div>

                <div class="p-5">
                    @if($dailyReport->mediaUpdates->isEmpty())
                        <p class="text-slate-600 dark:text-slate-300">No attachments.</p>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($dailyReport->mediaUpdates as $media)
                                @php
                                    $url = asset('storage/' . ltrim($media->file_path, '/'));
                                    $time = \Carbon\Carbon::parse($media->uploaded_at ?? $media->created_at)->format('d/m/Y H:i:s');
                                @endphp

                                <div class="rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                                    <div class="bg-slate-50 dark:bg-slate-900/40 p-3 text-sm text-slate-600 dark:text-slate-300">
                                        <div class="font-semibold text-slate-900 dark:text-slate-100">
                                            {{ ucfirst($media->type) }}
                                        </div>
                                        <div class="text-xs mt-1">
                                            Uploaded: {{ $time }}
                                        </div>
                                        @if($media->notes)
                                            <div class="text-xs mt-1">Notes: {{ $media->notes }}</div>
                                        @endif
                                    </div>

                                    <div class="p-3 bg-white dark:bg-slate-950/20">
                                        @if($media->type === 'image')
                                            <img src="{{ $url }}" alt="Attachment" class="w-full h-48 object-cover rounded-lg">
                                        @else
                                            <video controls class="w-full h-48 rounded-lg">
                                                <source src="{{ $url }}">
                                                Your browser does not support the video tag.
                                            </video>
                                        @endif

                                        <a href="{{ $url }}" target="_blank"
                                           class="mt-3 inline-flex items-center justify-center w-full rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                            Open
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>