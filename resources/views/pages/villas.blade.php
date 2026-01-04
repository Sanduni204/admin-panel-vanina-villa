@extends('layouts.app')

@section('content')
<div class="container-fluid py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1>Our Luxury Villas</h1>
            <p class="lead text-muted">Discover our exclusive collection of beautiful properties</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Search villas..."
                                value="{{ request('search') }}"
                            >
                        </div>
                        <div class="col-md-2">
                            <input
                                type="number"
                                name="price_min"
                                class="form-control"
                                placeholder="Min price"
                                value="{{ request('price_min') }}"
                            >
                        </div>
                        <div class="col-md-2">
                            <input
                                type="number"
                                name="price_max"
                                class="form-control"
                                placeholder="Max price"
                                value="{{ request('price_max') }}"
                            >
                        </div>
                        <div class="col-md-2">
                            <input
                                type="number"
                                name="guests"
                                class="form-control"
                                placeholder="Guests"
                                value="{{ request('guests') }}"
                            >
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Villas Grid -->
    <div class="row g-4">
        @forelse($villas as $villa)
            @php
                $translation = $villa->getTranslation();
                $featuredMedia = $villa->media()->where('is_featured', true)->first();
            @endphp

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm transition" style="transition: transform 0.3s;">
                    @if($featuredMedia)
                        <img
                            src="{{ asset($featuredMedia->image_path) }}"
                            alt="{{ $translation?->title }}"
                            class="card-img-top"
                            style="height: 250px; object-fit: cover;"
                        >
                    @else
                        <div
                            class="card-img-top bg-light d-flex align-items-center justify-content-center"
                            style="height: 250px;"
                        >
                            <span class="text-muted">No image</span>
                        </div>
                    @endif

                    <div class="card-body">
                        <h5 class="card-title">{{ $translation?->title }}</h5>

                        <div class="mb-2">
                            <small class="text-muted">
                                <i class="bi bi-people"></i> Up to {{ $translation?->max_guests }} Guests
                                <i class="bi bi-people"></i> {{ $translation?->max_guests }} Guests
                            </small>
                        </div>

                        <p class="card-text" style="font-size: 0.95rem; color: #666;">
                            {{ Str::limit($translation?->description, 100) }}
                        </p>
                    </div>

                    <div class="card-footer bg-white border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0 text-primary">${{ number_format($translation?->price, 2) }}<small class="text-muted fs-6">/night</small></span>
                            <a href="{{ route('pages.villa-detail', $villa->slug) }}" class="btn btn-sm btn-outline-primary">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        @empty
            <div class="col-12">
                <div class="alert alert-info" role="alert">
                    <i class="bi bi-info-circle"></i> No villas found matching your criteria.
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($villas->count() > 0)
        <div class="row mt-5">
            <div class="col-12 d-flex justify-content-center">
                {{ $villas->links() }}
            </div>
        </div>
    @endif
</div>

<style>
.transition:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
</style>
@endsection
