<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                    {{ __('parent.messages') }}
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    {{ __('parent.messages_description') }}
                </p>
            </div>
            <a href="{{ route('parent.messages.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold text-white
                      bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                      shadow-sm shadow-blue-500/20 hover:brightness-110 focus:outline-none focus:ring-4 focus:ring-blue-200
                      active:translate-y-[1px]">
                {{ __('parent.new_message') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if ($conversations->isEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-8 text-center text-slate-500">
                    {{ __('parent.no_messages') }}
                </div>
            @else
                @foreach ($conversations as $conversation)
                    <a href="{{ route('parent.messages.show', $conversation->user->id) }}"
                       class="block rounded-2xl border shadow-sm hover:shadow-md transition-shadow px-5 py-4
                              {{ $conversation->unread_count > 0
                                  ? 'border-blue-200 bg-blue-50/40'
                                  : 'border-slate-200 bg-white' }}">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <p class="{{ $conversation->unread_count > 0 ? 'font-bold' : 'font-medium' }} text-slate-900 truncate">
                                    {{ $conversation->user->name }}
                                </p>

                                <p class="text-sm text-slate-500 mt-0.5 truncate">
                                    {{ Str::limit($conversation->last_message->body, 50) }}
                                </p>

                                <p class="text-xs text-slate-400 mt-1">
                                    @if ($conversation->last_message->child)
                                        {{ __('parent.about') }}: {{ $conversation->last_message->child->first_name }} {{ $conversation->last_message->child->last_name }}
                                    @else
                                        {{ __('parent.general') }}
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