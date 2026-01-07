@extends('layouts.app')

@section('content')
@php
    $pageEn = $page->translation('en');
    $pageFr = $page->translation('fr');
    $locale = app()->getLocale();
@endphp
<div class="container-fluid px-0">
    <section class="position-relative text-white" style="min-height: 420px;">
        @if($page->hero_image_path)
            <div class="position-absolute inset-0 w-100 h-100" style="background:url('{{ asset('storage/' . $page->hero_image_path) }}') center/cover no-repeat; filter: brightness(0.6);"></div>
        @endif
        <div class="position-relative container py-5" style="z-index:1;">
            <p class="text-uppercase fw-semibold mb-2">{{ $page->translation(null, true)?->hero_tagline }}</p>
            <h1 class="display-4 fw-bold mb-3">{{ $page->translation(null, true)?->hero_title }}</h1>
            <p class="lead col-lg-6">{{ $page->translation(null, true)?->hero_lead }}</p>
        </div>
    </section>

    @php
        $blocksBySlug = $blocks->keyBy('slug');
        $restaurant = $blocksBySlug->get('restaurant');
        $barCoffee = $blocksBySlug->get('bar-coffee');
        $pool = $blocksBySlug->get('pool');
        $beach = $blocksBySlug->get('beach');
    @endphp

    <section class="container py-5">
        @if($restaurant)
            @php $t = $restaurant->translation(); @endphp
            <div class="row align-items-center g-4 mb-5">
                <div class="col-lg-6">
                    <h2 class="mb-3">{{ $t?->heading }}</h2>
                    <div class="text-muted" style="white-space: pre-line;">{{ $t?->body }}</div>
                </div>
                <div class="col-lg-6">
                    @if($restaurant->image_path)
                        <img src="{{ asset('storage/' . $restaurant->image_path) }}" alt="{{ $restaurant->image_alt }}" class="img-fluid rounded shadow-sm">
                    @endif
                </div>
            </div>
        @endif

        @if($barCoffee)
            @php $t = $barCoffee->translation(); @endphp
            <div class="mb-5">
                <div class="row align-items-center g-4 mb-3">
                    <div class="col-lg-6">
                        <h2 class="mb-3">{{ $t?->heading }}</h2>
                        <div class="text-muted mb-3" style="white-space: pre-line;">{{ $t?->body }}</div>
                        @if($t?->hours)
                            <p class="fw-semibold mb-2">Hours</p>
                            <p class="text-muted">{{ $t->hours }}</p>
                        @endif
                        @if($barCoffee->cta_label && $barCoffee->cta_url)
                            <a class="btn btn-outline-primary" href="{{ $barCoffee->cta_url }}">{{ $barCoffee->cta_label }}</a>
                        @endif
                    </div>
                    <div class="col-lg-6">
                        <div id="barCoffeeCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner rounded shadow">
                                @forelse($barCoffee->gallery as $idx => $media)
                                    <div class="carousel-item {{ $idx === 0 ? 'active' : '' }}">
                                        <img src="{{ asset('storage/' . $media->image_path) }}" class="d-block w-100" alt="{{ $media->image_alt }}" style="height:360px; object-fit:cover;">
                                    </div>
                                @empty
                                    <div class="carousel-item active">
                                        <div class="d-flex align-items-center justify-content-center bg-light" style="height:360px;">No images yet</div>
                                    </div>
                                @endforelse
                            </div>
                            @if($barCoffee->gallery->count() > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#barCoffeeCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#barCoffeeCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($pool)
            @php $t = $pool->translation(); @endphp
            <div class="row align-items-center g-4 mb-5">
                <div class="col-lg-6 order-lg-2">
                    <h2 class="mb-3">{{ $t?->heading }}</h2>
                    <div class="text-muted mb-3" style="white-space: pre-line;">{{ $t?->body }}</div>
                    @if($pool->cta_label && $pool->cta_url)
                        <a class="btn btn-outline-primary" href="{{ $pool->cta_url }}">{{ $pool->cta_label }}</a>
                    @endif
                </div>
                <div class="col-lg-6 order-lg-1">
                    @if($pool->image_path)
                        <img src="{{ asset('storage/' . $pool->image_path) }}" alt="{{ $pool->image_alt }}" class="img-fluid rounded shadow-sm">
                    @endif
                </div>
            </div>
        @endif

        @if($beach)
            @php $t = $beach->translation(); @endphp
            <div class="row align-items-center g-4 mb-5">
                <div class="col-lg-6">
                    <h2 class="mb-3">{{ $t?->heading }}</h2>
                    <div class="text-muted mb-3" style="white-space: pre-line;">{{ $t?->body }}</div>
                    @if($beach->cta_label && $beach->cta_url)
                        <a class="btn btn-outline-primary" href="{{ $beach->cta_url }}">{{ $beach->cta_label }}</a>
                    @endif
                </div>
                <div class="col-lg-6">
                    @if($beach->image_path)
                        <img src="{{ asset('storage/' . $beach->image_path) }}" alt="{{ $beach->image_alt }}" class="img-fluid rounded shadow-sm">
                    @endif
                </div>
            </div>
        @endif
    </section>

    <section class="bg-light py-5">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <p class="text-uppercase text-muted mb-1">Menus</p>
                    <h2 class="mb-0">Download our menus</h2>
                </div>
            </div>
            @php
                $menusDescription = $page->translation()?->menus_description;
            @endphp
            @if($menusDescription)
                <div class="alert alert-info mb-4" style="background-color: #e7f3ff; border-color: #b3d9ff; color: #004085;">
                    {{ $menusDescription }}
                </div>
            @endif
            <div class="row g-4">
                @forelse($menus as $menu)
                    <div class="col-md-6 col-lg-3">
                        <x-menu-download-card :menu="$menu" />
                    </div>
                @empty
                    <p class="text-muted">Menus will be available soon.</p>
                @endforelse
            </div>
        </div>
    </section>
</div>
@endsection
