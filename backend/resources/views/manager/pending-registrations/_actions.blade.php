<a href="{{ route('manager.pending-registrations.show', $user) }}"
   class="inline-flex items-center justify-center rounded-xl px-3 py-1.5 text-xs font-semibold
          bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200">
    {{ __('manager.view') }}
</a>

<a href="{{ route('manager.pending-registrations.edit', $user) }}"
   class="inline-flex items-center justify-center rounded-xl px-3 py-1.5 text-xs font-semibold
          bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100">
    {{ __('manager.edit') }}
</a>

<form method="POST" action="{{ route('manager.pending-registrations.approve', $user) }}" class="inline">
    @csrf
    <button type="submit"
            class="inline-flex items-center justify-center rounded-xl px-3 py-1.5 text-xs font-semibold text-white
                   bg-gradient-to-r from-green-600 to-emerald-700 hover:brightness-110">
        {{ __('manager.approve') }}
    </button>
</form>

<form method="POST" action="{{ route('manager.pending-registrations.reject', $user) }}" class="inline"
      onsubmit="const r = prompt('{{ __('manager.rejection_reason_prompt') }}'); if (!r) return false; this.querySelector('input[name=rejection_reason]').value = r;">
    @csrf
    <input type="hidden" name="rejection_reason" value="">
    <button type="submit"
            class="inline-flex items-center justify-center rounded-xl px-3 py-1.5 text-xs font-semibold
                   bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100">
        {{ __('manager.reject') }}
    </button>
</form>

<form method="POST" action="{{ route('manager.pending-registrations.destroy', $user) }}" class="inline"
      onsubmit="return confirm('{{ addslashes(__('manager.delete_pending_confirm', ['name' => $user->name])) }}')">
    @csrf
    @method('DELETE')
    <button type="submit"
            class="inline-flex items-center justify-center rounded-xl px-3 py-1.5 text-xs font-semibold
                   bg-red-50 text-red-700 border border-red-200 hover:bg-red-100">
        {{ __('manager.delete') }}
    </button>
</form>
