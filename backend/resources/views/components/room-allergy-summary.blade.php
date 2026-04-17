@props(['room'])

@php
    $childrenWithAllergies   = $room->children->filter(fn($c) => $c->hasAllergies());
    $childrenWithMedicalNeeds = $room->children->filter(fn($c) => $c->hasMedicalNeeds());
@endphp

@if($childrenWithAllergies->isNotEmpty())
<div class="bg-red-50 rounded-xl border border-red-200 p-4 mb-4">
    <h4 class="text-sm font-bold text-red-800 mb-2">⚠️ Allergies in {{ $room->name }}</h4>
    <div class="space-y-1">
        @foreach($childrenWithAllergies as $child)
            <div class="flex items-center gap-2 text-sm">
                <span class="font-medium text-red-700">{{ $child->first_name }} {{ $child->last_name }}:</span>
                <span class="text-red-600">{{ $child->allergyList() }}</span>
            </div>
        @endforeach
    </div>
</div>
@endif

@if($childrenWithMedicalNeeds->isNotEmpty())
<div class="bg-amber-50 rounded-xl border border-amber-200 p-4 mb-4">
    <h4 class="text-sm font-bold text-amber-800 mb-2">💊 Medical Needs in {{ $room->name }}</h4>
    <div class="space-y-1">
        @foreach($childrenWithMedicalNeeds as $child)
            <div class="flex items-start gap-2 text-sm">
                <span class="font-medium text-amber-700 shrink-0">{{ $child->first_name }} {{ $child->last_name }}:</span>
                <span class="text-amber-600">{{ $child->medicalNeedsSummary() }}</span>
            </div>
        @endforeach
    </div>
</div>
@endif
