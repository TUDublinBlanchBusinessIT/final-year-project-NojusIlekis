<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
                    {{ __('staff.qualifications') }}
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">{{ $carer->name }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('manager.carers.qualifications.create', $carer) }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                          bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 hover:brightness-110">
                    {{ __('staff.add_qualification') }}
                </a>
                <a href="{{ route('manager.carers.show', $carer) }}"
                   class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                          bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100">
                    {{ __('manager.back') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if($qualifications->isEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-8 text-center">
                    <p class="text-slate-600">{{ __('staff.no_qualifications_yet') }}</p>
                </div>
            @else
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr class="text-left">
                                <th class="px-4 py-3 font-semibold text-slate-700">{{ __('staff.qualification_type') }}</th>
                                <th class="px-4 py-3 font-semibold text-slate-700">{{ __('staff.qualification_name') }}</th>
                                <th class="px-4 py-3 font-semibold text-slate-700">{{ __('staff.expires_at') }}</th>
                                <th class="px-4 py-3 font-semibold text-slate-700">{{ __('manager.status') }}</th>
                                <th class="px-4 py-3 font-semibold text-slate-700">{{ __('manager.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($qualifications as $qual)
                                <tr>
                                    <td class="px-4 py-3 text-slate-800">{{ $qual->typeLabel() }}</td>
                                    <td class="px-4 py-3 text-slate-800">
                                        <div class="font-medium">{{ $qual->name }}</div>
                                        @if($qual->issuer)
                                            <div class="text-xs text-slate-500">{{ $qual->issuer }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-700">
                                        {{ $qual->expires_at ? $qual->expires_at->format('d M Y') : '—' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $qual->statusColour() }}">
                                            {{ $qual->statusLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            @if($qual->document_path)
                                                <a href="{{ Storage::disk('public')->url($qual->document_path) }}"
                                                   target="_blank"
                                                   class="text-xs text-blue-600 hover:underline">
                                                    {{ __('staff.view_document') }}
                                                </a>
                                            @endif
                                            <a href="{{ route('manager.carers.qualifications.edit', [$carer, $qual]) }}"
                                               class="inline-flex items-center justify-center rounded-lg px-3 py-1 text-xs font-semibold
                                                      bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200">
                                                {{ __('manager.edit') }}
                                            </a>
                                            <form method="POST"
                                                  action="{{ route('manager.carers.qualifications.destroy', [$carer, $qual]) }}"
                                                  onsubmit="return confirm('{{ addslashes(__('staff.delete_qualification_confirm')) }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center justify-center rounded-lg px-3 py-1 text-xs font-semibold
                                                               bg-red-50 text-red-700 border border-red-200 hover:bg-red-100">
                                                    {{ __('manager.delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
