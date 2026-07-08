@props(['id' => 'confirm-modal', 'title' => 'Konfirmasi'])

<div id="{{ $id }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50" onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="mx-4 w-full max-w-md rounded-xl bg-white p-6 shadow-xl" onclick="event.stopPropagation()">
        <h4 class="mb-2 text-lg font-semibold text-slate-800">{{ $title }}</h4>
        <p class="mb-6 text-sm text-slate-500">{{ $slot }}</p>
        <div class="flex justify-end gap-3">
            <button type="button" onclick="document.getElementById('{{ $id }}').classList.add('hidden')" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                Batal
            </button>
            <span id="{{ $id }}-action"></span>
        </div>
    </div>
</div>

@push('head')
<script>
    window.showConfirmModal = function(modalId, message) {
        const el = document.getElementById(modalId);
        // Use textContent instead of innerHTML to prevent XSS
        el.querySelector('p').textContent = message;
        el.classList.remove('hidden');
    };
</script>
@endpush