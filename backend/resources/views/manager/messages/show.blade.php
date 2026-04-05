<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                    Enquiry from {{ $user->name }}
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    Parent conversation thread
                </p>
            </div>

            <a href="{{ route('manager.messages.index') }}"
               class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                      bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100
                      dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700/60">
                ← Back to Enquiries
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 flex flex-col gap-6">

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6 flex flex-col gap-4 max-h-[60vh] overflow-y-auto
                        dark:border-slate-800 dark:bg-slate-950/40">
                @if($messages->count() === 1)
                    <p class="text-center text-xs text-slate-400">
                        Start of conversation
                    </p>
                @endif

                @forelse ($messages as $messageItem)
                    @php $isMine = $messageItem->sender_id === auth()->id(); @endphp

                    <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-sm">
                            @if ($messageItem->child)
                                <p class="text-xs text-slate-400 mb-1 {{ $isMine ? 'text-right' : 'text-left' }}">
                                    Re: {{ $messageItem->child->first_name }} {{ $messageItem->child->last_name }}
                                </p>
                            @endif

                            <div class="px-4 py-3 text-sm
                                {{ $isMine
                                    ? 'bg-blue-600 text-white rounded-2xl rounded-br-sm ml-auto'
                                    : 'bg-slate-200 text-slate-900 rounded-2xl rounded-bl-sm mr-auto dark:bg-slate-800 dark:text-slate-100' }}">
                                {{ $messageItem->body }}
                            </div>

                            <p class="text-xs text-slate-400 mt-1 {{ $isMine ? 'text-right' : 'text-left' }}">
                                {{ $messageItem->created_at->format('g:i A') }}
                                &middot; {{ $messageItem->created_at->format('d M') }}

                                @if ($isMine)
                                    &middot; {{ $messageItem->read_at ? 'Read' : 'Sent' }}
                                @endif
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-400 text-sm text-center py-8">
                        No messages yet.
                    </p>
                @endforelse
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5
                        dark:border-slate-800 dark:bg-slate-950/40">
                @if (session('success'))
                    <p class="mb-3 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg px-4 py-2
                              dark:bg-green-950/30 dark:border-green-900/60 dark:text-green-200">
                        {{ session('success') }}
                    </p>
                @endif

                <form action="{{ route('manager.messages.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="receiver_id" value="{{ $user->id }}">

                    <div>
                        <label for="body" class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">
                            Reply
                        </label>
                        <textarea name="body" id="body" rows="3" required maxlength="2000"
                                  placeholder="Type your reply..."
                                  class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:ring-blue-500 focus:border-blue-500 resize-none
                                         dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">{{ old('body') }}</textarea>

                        @if ($errors->has('body'))
                            <p class="text-xs text-red-600 mt-1">{{ $errors->first('body') }}</p>
                        @endif
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2 text-sm font-semibold text-white
                                       bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                       shadow-sm shadow-blue-500/20 hover:brightness-110 focus:outline-none focus:ring-4 focus:ring-blue-200
                                       active:translate-y-[1px]">
                            Send Reply
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>