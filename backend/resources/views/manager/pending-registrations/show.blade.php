<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
                    {{ __('manager.review_registration') }}
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">{{ $user->name }}</p>
            </div>
            <a href="{{ route('manager.pending-registrations.index') }}"
               class="inline-flex items-center justify-center rounded-xl px-3 py-1.5 text-sm font-semibold
                      bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200">
                ← {{ __('manager.back') }}
            </a>
        </div>
    </x-slot>

    @php($child = $user->children->first())

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6
                        dark:border-slate-800 dark:bg-slate-950/40">
                <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100 mb-4">
                    {{ __('manager.applicant_details') }}
                </h3>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-slate-500 dark:text-slate-400">{{ __('manager.role') }}</dt>
                        <dd class="text-slate-900 dark:text-slate-100 font-medium">{{ ucfirst($user->role) }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500 dark:text-slate-400">{{ __('manager.submitted') }}</dt>
                        <dd class="text-slate-900 dark:text-slate-100">{{ $user->created_at->format('d M Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500 dark:text-slate-400">{{ __('manager.name') }}</dt>
                        <dd class="text-slate-900 dark:text-slate-100">{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500 dark:text-slate-400">{{ __('manager.email') }}</dt>
                        <dd class="text-slate-900 dark:text-slate-100">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500 dark:text-slate-400">{{ __('auth.register_phone') }}</dt>
                        <dd class="text-slate-900 dark:text-slate-100">{{ $user->phone }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-slate-500 dark:text-slate-400">{{ __('auth.register_address') }}</dt>
                        <dd class="text-slate-900 dark:text-slate-100 whitespace-pre-line">{{ $user->address }}</dd>
                    </div>
                    @if ($user->registration_notes)
                        <div class="sm:col-span-2">
                            <dt class="text-slate-500 dark:text-slate-400">
                                {{ $user->role === 'carer' ? __('auth.register_carer_experience') : __('auth.register_additional_notes') }}
                            </dt>
                            <dd class="text-slate-900 dark:text-slate-100 whitespace-pre-line">{{ $user->registration_notes }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            @if ($child)
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6
                            dark:border-slate-800 dark:bg-slate-950/40">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100 mb-4">
                        {{ __('auth.register_child_details') }}
                    </h3>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-slate-500 dark:text-slate-400">{{ __('manager.first_name') }}</dt>
                            <dd class="text-slate-900 dark:text-slate-100">{{ $child->first_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500 dark:text-slate-400">{{ __('manager.last_name') }}</dt>
                            <dd class="text-slate-900 dark:text-slate-100">{{ $child->last_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500 dark:text-slate-400">{{ __('manager.date_of_birth') }}</dt>
                            <dd class="text-slate-900 dark:text-slate-100">{{ \Carbon\Carbon::parse($child->dob)->format('d M Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500 dark:text-slate-400">{{ __('manager.allergies') }}</dt>
                            <dd class="text-slate-900 dark:text-slate-100">{{ $child->allergies ?: __('manager.none') }}</dd>
                        </div>
                        @if ($child->medical_notes)
                            <div class="sm:col-span-2">
                                <dt class="text-slate-500 dark:text-slate-400">{{ __('manager.medical_notes') }}</dt>
                                <dd class="text-slate-900 dark:text-slate-100 whitespace-pre-line">{{ $child->medical_notes }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            @endif

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6
                        dark:border-slate-800 dark:bg-slate-950/40">
                <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100 mb-4">
                    {{ __('manager.actions') }}
                </h3>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('manager.pending-registrations.edit', $user) }}"
                       class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold
                              bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100">
                        {{ __('manager.edit') }}
                    </a>

                    @if ($user->role === 'carer')
                        <form method="POST" action="{{ route('manager.pending-registrations.approve', $user) }}" class="flex items-center gap-2">
                            @csrf
                            <select name="room_id"
                                    class="rounded-xl border-slate-300 text-sm text-slate-900">
                                <option value="">{{ __('manager.approve_no_room') }}</option>
                                @foreach (\App\Models\Room::orderBy('name')->get() as $room)
                                    <option value="{{ $room->id }}">{{ $room->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit"
                                    class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold text-white
                                           bg-gradient-to-r from-green-600 to-emerald-700 hover:brightness-110">
                                {{ __('manager.approve_registration') }}
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('manager.pending-registrations.approve', $user) }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold text-white
                                           bg-gradient-to-r from-green-600 to-emerald-700 hover:brightness-110">
                                {{ __('manager.approve_registration') }}
                            </button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('manager.pending-registrations.reject', $user) }}" class="flex items-center gap-2">
                        @csrf
                        <input type="text"
                               name="rejection_reason"
                               required
                               placeholder="{{ __('manager.rejection_reason') }}"
                               class="rounded-xl border-slate-300 text-sm text-slate-900 w-64">
                        <button type="submit"
                                class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold
                                       bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100">
                            {{ __('manager.reject_registration') }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('manager.pending-registrations.destroy', $user) }}"
                          onsubmit="return confirm('{{ addslashes(__('manager.delete_pending_confirm', ['name' => $user->name])) }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold
                                       bg-red-50 text-red-700 border border-red-200 hover:bg-red-100">
                            {{ __('manager.delete') }}
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
