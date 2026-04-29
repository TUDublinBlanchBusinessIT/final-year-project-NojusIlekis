@props(['child'])

@if($child->hasMedicalNeeds())
<div class="bg-amber-50 border-l-4 border-amber-500 text-amber-700 p-4 mb-4 rounded-lg shadow-sm" role="alert">
    <div class="flex items-center">
        <svg class="w-6 h-6 mr-3 flex-shrink-0 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
        </svg>
        <div>
            <p class="font-bold text-amber-800">💊 Medical Needs — {{ $child->first_name }} {{ $child->last_name }}</p>
            <p class="text-sm mt-1">{{ $child->medicalNeedsSummary() }}</p>
        </div>
    </div>
</div>
@endif
