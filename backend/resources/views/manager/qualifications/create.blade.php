<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
                    {{ __('staff.add_qualification') }}
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">{{ $carer->name }}</p>
            </div>
            <a href="{{ route('manager.carers.qualifications.index', $carer) }}"
               class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                      bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100">
                {{ __('manager.back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('manager.carers.qualifications.store', $carer) }}"
                  enctype="multipart/form-data"
                  class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6 space-y-4">
                @csrf

                @include('manager.qualifications._form', ['qualification' => null])

                <div class="flex items-center gap-3 pt-4 border-t border-slate-200">
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl px-4 py-2 font-semibold text-white
                                   bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 hover:brightness-110">
                        {{ __('manager.save') }}
                    </button>
                    <a href="{{ route('manager.carers.qualifications.index', $carer) }}"
                       class="inline-flex items-center justify-center rounded-xl px-4 py-2 font-semibold
                              bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200">
                        {{ __('manager.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
