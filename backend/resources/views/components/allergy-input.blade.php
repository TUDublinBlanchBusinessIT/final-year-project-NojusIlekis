@props(['value' => ''])

@php
    // Normalise: if an existing value is passed, split into array for Alpine
    $initial = array_values(array_filter(array_map('trim', explode(',', old('allergies', $value ?? '')))));
@endphp

<div x-data="{
        allergies: {{ Js::from($initial) }},
        newAllergy: '',
        addAllergy() {
            const val = this.newAllergy.trim().replace(/,/g, '');
            if (val && !this.allergies.includes(val)) {
                this.allergies.push(val);
            }
            this.newAllergy = '';
            this.$refs.allergyInput.focus();
        },
        removeAllergy(index) {
            this.allergies.splice(index, 1);
        }
     }" class="space-y-2">

    {{-- Tag display --}}
    <div class="flex flex-wrap gap-2 min-h-[2rem]">
        <template x-for="(allergy, index) in allergies" :key="index">
            <span class="inline-flex items-center bg-red-100 text-red-800 text-sm font-medium px-3 py-1 rounded-full">
                <span x-text="allergy"></span>
                <button type="button" @click="removeAllergy(index)"
                        class="ml-1.5 text-red-600 hover:text-red-800 focus:outline-none">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </span>
        </template>
    </div>

    {{-- Text input with datalist --}}
    <div class="flex gap-2">
        <input type="text" x-model="newAllergy" x-ref="allergyInput"
               list="common-allergies"
               @keydown.enter.prevent="addAllergy()"
               @keydown.comma.prevent="addAllergy()"
               placeholder="Type an allergy and press Enter…"
               class="flex-1 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900
                      focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                      dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm">
        <button type="button" @click="addAllergy()"
                class="px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-200">
            Add
        </button>
    </div>

    {{-- Common allergies datalist --}}
    <datalist id="common-allergies">
        <option value="Dairy">
        <option value="Peanuts">
        <option value="Tree Nuts">
        <option value="Eggs">
        <option value="Gluten">
        <option value="Wheat">
        <option value="Shellfish">
        <option value="Fish">
        <option value="Soy">
        <option value="Sesame">
        <option value="Milk">
        <option value="Latex">
        <option value="Bee Stings">
        <option value="Penicillin">
    </datalist>

    {{-- Quick-add buttons --}}
    <div class="flex flex-wrap gap-1.5 items-center">
        <span class="text-xs text-slate-500">Quick add:</span>
        <template x-for="suggestion in ['Dairy', 'Peanuts', 'Tree Nuts', 'Eggs', 'Gluten', 'Shellfish', 'Soy']" :key="suggestion">
            <button type="button"
                    @click="if(!allergies.includes(suggestion)) { allergies.push(suggestion); }"
                    x-show="!allergies.includes(suggestion)"
                    class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded-full hover:bg-slate-200 transition">
                + <span x-text="suggestion"></span>
            </button>
        </template>
    </div>

    {{-- Hidden input that submits the data as a comma-separated string --}}
    <input type="hidden" name="allergies" :value="allergies.join(', ')">

    <p class="text-xs text-slate-500">Select common allergies or type custom ones. Leave empty if none.</p>
</div>
