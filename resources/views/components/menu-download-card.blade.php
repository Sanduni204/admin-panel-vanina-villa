@props(['menu'])
@php
    $translation = $menu->translation();
    $downloadUrl = \Illuminate\Support\Facades\URL::signedRoute('dine-relax.menu.download', ['type' => $menu->type]);
    $typeLabel = ucfirst($menu->type);
    $bg = [
        'beverage' => 'linear-gradient(135deg,#0b486b,#f56217)',
        'snacking' => 'linear-gradient(135deg,#667eea,#764ba2)',
        'today' => 'linear-gradient(135deg,#ff758c,#ff7eb3)',
        'breakfast' => 'linear-gradient(135deg,#11998e,#38ef7d)',
    ][$menu->type] ?? 'linear-gradient(135deg,#1f4037,#99f2c8)';
    $cardImage = $menu->card_image_path ? asset('storage/' . $menu->card_image_path) : null;
    $cardAlt = app()->getLocale() === 'fr'
        ? ($menu->card_image_alt_fr ?: $menu->card_image_alt)
        : ($menu->card_image_alt ?: $menu->card_image_alt_fr);
@endphp

<div class="card h-100 shadow-sm border-0 overflow-hidden">
    <div class="ratio ratio-16x9" style="background: {{ $bg }};">
        @if($cardImage)
            <img src="{{ $cardImage }}" alt="{{ $cardAlt }}" class="w-100 h-100" style="object-fit: cover;">
            <div class="position-absolute bottom-0 start-0 end-0 p-3 text-white" style="background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.65) 100%);">
                <span class="small text-uppercase fw-semibold">{{ $typeLabel }}</span>
                <strong class="fs-5 d-block">{{ $translation?->title }}</strong>
                @if($translation?->version_note)
                    <span class="small">{{ $translation->version_note }}</span>
                @endif
            </div>
        @else
            <div class="d-flex flex-column justify-content-end p-3 text-white" style="backdrop-filter: blur(2px); background: rgba(0,0,0,0.25);">
                <span class="small text-uppercase fw-semibold">{{ $typeLabel }}</span>
                <strong class="fs-5">{{ $translation?->title }}</strong>
                @if($translation?->version_note)
                    <span class="small">{{ $translation->version_note }}</span>
                @endif
            </div>
        @endif
    </div>
    <div class="card-body d-flex flex-column">
        <p class="mb-3 text-muted">{{ $translation?->button_label }}</p>
        <a href="{{ $downloadUrl }}" class="btn btn-primary mt-auto" rel="noopener">Download</a>
    </div>
</div>
