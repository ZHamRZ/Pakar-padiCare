@props([
    'value' => 0,
    'label' => null,
    'showLabel' => true,
    'height' => 10,
])

@php
    use App\Support\ExpertSystemPresenter;

    $normalized = max(0, min(1, (float) $value));
    $percent = ExpertSystemPresenter::rawPercent($normalized);
    $resolvedLabel = $label ?: ExpertSystemPresenter::confidenceLabel($normalized);
    $tone = ExpertSystemPresenter::confidenceTone($normalized);
@endphp

<div>
    @if($showLabel)
    <div class="d-flex justify-content-between align-items-center small mb-2">
        <span class="fw-semibold text-dark">{{ $resolvedLabel }}</span>
        <span class="text-muted">{{ ExpertSystemPresenter::percent($normalized) }}</span>
    </div>
    @endif
    <div class="progress" role="progressbar" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100" style="height: {{ (int) $height }}px;">
        <div class="progress-bar bg-{{ $tone }}" style="width: {{ $percent }}%"></div>
    </div>
</div>
