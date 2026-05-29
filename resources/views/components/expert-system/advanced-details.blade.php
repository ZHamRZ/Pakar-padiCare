@props([
    'target',
    'buttonLabel' => 'Lihat Detail Perhitungan',
])

<div class="accordion">
    <div class="accordion-item border rounded-4 overflow-hidden">
        <h2 class="accordion-header">
            <button
                class="accordion-button collapsed fw-semibold"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#{{ $target }}"
                aria-expanded="false"
                aria-controls="{{ $target }}"
            >
                {{ $buttonLabel }}
            </button>
        </h2>
        <div id="{{ $target }}" class="accordion-collapse collapse">
            <div class="accordion-body">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
