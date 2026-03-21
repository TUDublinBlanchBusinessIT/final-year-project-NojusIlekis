<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                    Parent Enquiries
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    View and respond to fee and invoice enquiries sent by parents.
                </p>
            </div>

            <a href="{{ route('manager.dashboard') }}"
               class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                      bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100
                      dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700/60">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if ($conversations->isEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-8 text-center text-slate-500
                            dark:border-slate-800 dark:bg-slate-950/40 dark:text-slate-300">
                    No parent enquiries yet.
                </div>
            @else
                @foreach ($conversations as $conversation)
                    <a href="{{ route('manager.messages.show', $conversation->user->id) }}"
                       class="block rounded-2xl border shadow-sm hover:shadow-md transition-shadow px-5 py-4
                              {{ $conversation->unread_count > 0
                                  ? 'border-blue-200 bg-blue-50/40 dark:border-blue-900/50 dark:bg-blue-950/20'
                                  : 'border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950/40' }}">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <p class="{{ $conversation->unread_count > 0 ? 'font-bold' : 'font-medium' }} text-slate-900 dark:text-slate-100 truncate">
                                    {{ $conversation->user->name }}
                                </p>

                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                                    {{ Str::limit($conversation->last_message->body, 70) }}
                                </p>

                                <p class="text-xs text-slate-400 mt-1">
                                    @if ($conversation->last_message->child)
                                        About: {{ $conversation->last_message->child->first_name }} {{ $conversation->last_message->child->last_name }}
                                    @else
                                        General enquiry
                                    @endif
                                </p>
                            </div>

                            <div class="flex flex-col items-end gap-1 shrink-0">
                                <span class="text-xs text-slate-400">
                                    {{ $conversation->last_message->created_at->diffForHumans() }}
                                </span>

                                @if ($conversation->unread_count > 0)
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-blue-600 text-white text-xs font-bold">
                                        {{ $conversation->unread_count }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            @endif

        </div>
    </div>
</x-app-layout>