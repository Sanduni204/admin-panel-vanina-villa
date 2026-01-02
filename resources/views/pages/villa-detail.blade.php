@extends('layouts.app')

@section('content')
<div class="container py-5">
    @php
        $translation = $villa->getTranslation();
    @endphp

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pages.villas') }}">Villas</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $translation?->title }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <!-- Featured Image -->
            @php
                $featuredImage = $villa->media()->where('is_featured', true)->first();
            @endphp

            @if($featuredImage)
                <div class="mb-4">
                    <img
                        src="{{ asset('storage/' . $featuredImage->image_path) }}"
                        alt="{{ $translation?->title }}"
                        class="img-fluid rounded"
                        style="max-height: 500px; object-fit: cover; width: 100%;"
                    >
                </div>
            @endif

            <!-- Title and Meta -->
            <h1 class="mb-3">{{ $translation?->title }}</h1>

            <div class="row mb-4 text-muted">
                <div class="col-auto">
                    <i class="bi bi-door-closed"></i> {{ $translation?->bedrooms }} Bedrooms
                </div>
                <div class="col-auto">
                    <i class="bi bi-shop"></i> {{ $translation?->bathrooms }} Bathrooms
                </div>
                <div class="col-auto">
                    <i class="bi bi-people"></i> Up to {{ $translation?->max_guests }} Guests
                </div>
            </div>

            <!-- Description -->
            <div class="mb-4">
                <h3>About This Villa</h3>
                <p class="lead">{{ $translation?->description }}</p>
            </div>

            <!-- Amenities -->
            @if($translation?->amenities)
                <div class="mb-4">
                    <h3>Amenities</h3>
                    <div style="white-space: pre-wrap;">{{ $translation?->amenities }}</div>
                </div>
            @endif

            <!-- Rules -->
            @if($translation?->rules)
                <div class="mb-4">
                    <h3>House Rules</h3>
                    <div style="white-space: pre-wrap;">{{ $translation?->rules }}</div>
                </div>
            @endif

            <!-- Gallery -->
            @if($villa->media()->where('is_featured', false)->count() > 0)
                <div class="mb-4">
                    <h3 class="mb-3">Gallery</h3>
                    <div class="row g-3">
                        @foreach($villa->media()->where('is_featured', false)->ordered()->get() as $media)
                            <div class="col-md-4">
                                <a
                                    href="{{ asset('storage/' . $media->image_path) }}"
                                    class="d-block overflow-hidden rounded"
                                    style="aspect-ratio: 1; cursor: pointer;"
                                    data-lightbox="villa-gallery"
                                >
                                    <img
                                        src="{{ asset('storage/' . $media->image_path) }}"
                                        alt="{{ $media->alt_text_en }}"
                                        class="w-100 h-100"
                                        style="object-fit: cover; transition: transform 0.3s;"
                                    >
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-body">
                    @if($translation?->price_shoulder_season || $translation?->price_high_season || $translation?->price_peak_season)
                    <div class="alert alert-info py-2 px-3 mb-3">
                        <p class="mb-1 small"><strong>Seasonal Rates:</strong></p>
                        @if($translation?->price_shoulder_season)
                        <p class="mb-1 small">
                            <span class="badge bg-secondary">Shoulder</span> €{{ number_format($translation?->price_shoulder_season, 0) }}/night (2 nights min)
                        </p>
                        @endif
                        @if($translation?->price_high_season)
                        <p class="mb-1 small">
                            <span class="badge bg-warning text-dark">High</span> €{{ number_format($translation?->price_high_season, 0) }}/night (3 nights min)
                        </p>
                        @endif
                        @if($translation?->price_peak_season)
                        <p class="mb-0 small">
                            <span class="badge bg-danger">Peak</span> €{{ number_format($translation?->price_peak_season, 0) }}/night (5 nights min)
                        </p>
                        @endif
                    </div>
                    @endif

                    <p class="text-muted mb-3">
                        Available for booking. Contact us for reservations and special rates.
                    </p>

                    <button class="btn btn-primary btn-lg w-100 mb-2">
                        <i class="bi bi-calendar-check"></i> Check Availability
                    </button>

                    <button class="btn btn-outline-primary btn-lg w-100">
                        <i class="bi bi-envelope"></i> Contact Owner
                    </button>
                </div>
            </div>

            <!-- Quick Info -->
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Quick Info</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <strong>Bedrooms:</strong> {{ $translation?->bedrooms }}
                    </li>
                    <li class="list-group-item">
                        <strong>Bathrooms:</strong> {{ $translation?->bathrooms }}
                    </li>
                    <li class="list-group-item">
                        <strong>Max Guests:</strong> {{ $translation?->max_guests }}
                    </li>
                    <li class="list-group-item">
                        <strong>Base Price:</strong> €{{ number_format($translation?->price, 2) }}/night
                    </li>
                </ul>
            </div>

            @if($villa->featured)
                <div class="card mt-3 border-warning bg-light">
                    <div class="card-body text-center">
                        <i class="bi bi-star-fill text-warning fs-3"></i>
                        <p class="mb-0 mt-2"><strong>Featured Property</strong></p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Similar Villas -->
    @php
        $similar = \App\Models\Villa::published()
            ->where('id', '!=', $villa->id)
            ->limit(3)
            ->get();
    @endphp

    @if($similar->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">Similar Villas</h3>
            </div>
            @foreach($similar as $similarVilla)
                @php
                    $similarTranslation = $similarVilla->getTranslation();
                    $similarFeaturedMedia = $similarVilla->media()->where('is_featured', true)->first();
                @endphp

                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        @if($similarFeaturedMedia)
                            <img
                                src="{{ asset('storage/' . $similarFeaturedMedia->image_path) }}"
                                alt="{{ $similarTranslation?->title }}"
                                class="card-img-top"
                                style="height: 200px; object-fit: cover;"
                            >
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $similarTranslation?->title }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($similarTranslation?->description, 80) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-primary fw-bold">${{ number_format($similarTranslation?->price, 2) }}/night</span>
                                <a href="{{ route('pages.villa-detail', $similarVilla->slug) }}" class="btn btn-sm btn-outline-primary">
                                    View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Lightbox CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">

<!-- Lightbox JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
@endsection
