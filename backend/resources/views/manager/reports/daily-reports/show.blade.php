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

            @if(session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50/30 p-4 text-emerald-800
                            dark:border-emerald-900/60 dark:bg-emerald-950/10 dark:text-emerald-100">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-2xl border border-red-200 bg-red-50/30 p-4 text-red-800
                            dark:border-red-900/60 dark:bg-red-950/10 dark:text-red-100">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Acknowledgement --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950/40">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Acknowledgement</h3>

                    <form method="POST" action="{{ route('manager.reports.daily-reports.request-ack', $dailyReport->id) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                            Request Acknowledgement
                        </button>
                    </form>
                </div>

                <div class="p-5">
                    @if($dailyReport->acknowledgement && $dailyReport->acknowledgement->status === 'acknowledged')
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-green-800">Acknowledged</p>
                                <p class="text-sm text-gray-500">
                                    Signed by {{ $dailyReport->acknowledgement->signature_name }}
                                    on {{ $dailyReport->acknowledgement->signed_at->format('d M Y \a\t H:i') }}
                                </p>
                            </div>
                        </div>
                    @elseif($dailyReport->acknowledgement)
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-amber-800">Awaiting Parent Signature</p>
                                <p class="text-sm text-gray-500">Sent to parent — not yet acknowledged</p>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-400 text-sm">No acknowledgement requested for this report.</p>
                    @endif

                    @if($dailyReport->acknowledgement && ($dailyReport->acknowledgement->creator || $dailyReport->acknowledgement->updater))
                        <div class="mt-3 text-xs text-gray-400">
                            @if($dailyReport->acknowledgement->creator)
                                Requested by {{ $dailyReport->acknowledgement->creator->name }}
                            @endif
                            @if($dailyReport->acknowledgement->updater)
                                · Last updated by {{ $dailyReport->acknowledgement->updater->name }}
                            @endif
                        </div>
                    @endif
                </div>
            </div>

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