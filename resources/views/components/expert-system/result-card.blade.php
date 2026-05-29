@props([
    'title',
    'code' => null,
    'description' => null,
    'score' => 0,
    'rank' => null,
    'imageUrl' => null,
    'badge' => null,
    'type' => 'Pupuk',
])

@php
    use App\Support\ExpertSystemPresenter;

    $badgeData = is_array($badge)
        ? $badge
        : ExpertSystemPresenter::recommendationBadge((string) $badge);
@endphp

<div class="card h-100 border-0 shadow-sm" style="border-radius: 20px;">
    <div class="card-body p-4">
        <div class="d-flex gap-3">
            @if($imageUrl)
            <img src="{{ $imageUrl }}" alt="{{ $title }}" style="width:84px;height:84px;object-fit:cover;border-radius:16px;background:#f8fafc;">
            @else
            <div class="d-flex align-items-center justify-content-center text-secondary" style="width:84px;height:84px;border-radius:16px;background:linear-gradient(135deg,#f8fafc 0%,#e2e8f0 100%);">
                <i class="bi {{ $type === 'Pestisida' ? 'bi-capsule' : 'bi-bag' }} fs-3"></i>
            </div>
            @endif

            <div class="flex-grow-1">
                <div class="d-flex flex-wrap gap-2 mb-2">
                    @if($rank)
                    <span class="badge text-bg-success">Peringkat {{ $rank }}</span>
                    @endif
                    @if($code)
                    <span class="badge bg-light text-dark border">{{ $code }}</span>
                    @endif
                    <span class="badge bg-{{ $badgeData['tone'] }}-subtle text-{{ $badgeData['tone'] }} border border-{{ $badgeData['tone'] }}-subtle">
                        @if(!empty($badgeData['icon']))
                        <i class="bi {{ $badgeData['icon'] }} me-1"></i>
                        @endif
                        {{ $badgeData['label'] }}
                    </span>
                </div>

                <h5 class="fw-bold mb-1">{{ $title }}</h5>
                <p class="text-muted small mb-3">{{ $description }}</p>

                <x-expert-system.confidence-bar :value="$score" />
            </div>
        </div>
    </div>
</div>
