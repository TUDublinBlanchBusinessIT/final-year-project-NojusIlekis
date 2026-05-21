<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="type" :value="__('staff.qualification_type')" />
        <select id="type" name="type" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900">
            @foreach($types as $value => $label)
                <option value="{{ $value }}" @selected(old('type', $qualification?->type) === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('type')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="issuer" :value="__('staff.issuer')" />
        <x-text-input id="issuer" name="issuer" type="text" class="mt-1 block w-full"
                      :value="old('issuer', $qualification?->issuer)" />
        <x-input-error :messages="$errors->get('issuer')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="name" :value="__('staff.qualification_name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                      :value="old('name', $qualification?->name)" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="issued_date" :value="__('staff.issued_date')" />
        <x-text-input id="issued_date" name="issued_date" type="date" class="mt-1 block w-full"
                      :value="old('issued_date', optional($qualification?->issued_date)->format('Y-m-d'))" />
        <x-input-error :messages="$errors->get('issued_date')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="expires_at" :value="__('staff.expires_at')" />
        <x-text-input id="expires_at" name="expires_at" type="date" class="mt-1 block w-full"
                      :value="old('expires_at', optional($qualification?->expires_at)->format('Y-m-d'))" />
        <p class="text-xs text-slate-500 mt-1">{{ __('staff.expires_at_help') }}</p>
        <x-input-error :messages="$errors->get('expires_at')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="document" :value="__('staff.document')" />
        <input id="document" name="document" type="file" accept=".pdf,.jpg,.jpeg,.png"
               class="mt-1 block w-full text-sm text-slate-700">
        <p class="text-xs text-slate-500 mt-1">{{ __('staff.document_help') }}</p>
        <x-input-error :messages="$errors->get('document')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="notes" :value="__('staff.qualification_notes')" />
        <textarea id="notes" name="notes" rows="3"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900">{{ old('notes', $qualification?->notes) }}</textarea>
        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
    </div>
</div>
