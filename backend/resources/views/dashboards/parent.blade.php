<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-white">NEW PARENT DASHBOARD</h2>
    </x-slot>

    <div class="p-6 text-white">
        <p class="mb-4">This is the updated parent dashboard.</p>

        <div class="rounded-xl border border-slate-700 bg-slate-900 p-4">
            <h3 class="text-lg font-semibold mb-2">Pending Acknowledgements</h3>

            @forelse($acknowledgements as $ack)
                <div class="mb-3 border-b border-slate-700 pb-3">
                    <p>Type: {{ $ack->record_type }}</p>
                    <p>Record ID: {{ $ack->record_id }}</p>
                    <p>Status: {{ $ack->status }}</p>

                    <form method="POST" action="{{ route('parent.acknowledgements.sign', $ack) }}" class="mt-3 space-y-2">
                        @csrf

                        <input
                            type="text"
                            name="signature_name"
                            placeholder="Type your full name"
                            class="w-full rounded border px-3 py-2 text-black"
                            required
                        >

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="confirm_acknowledgement" value="1" required>
                            <span>I confirm I have read this item</span>
                        </label>

                        <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-white">
                            Acknowledge
                        </button>
                    </form>
                </div>
            @empty
                <p>No pending acknowledgements.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>