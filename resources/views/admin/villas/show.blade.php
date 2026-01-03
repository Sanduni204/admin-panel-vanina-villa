@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>{{ $villa->getTranslation('en')?->title }}</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('villas.edit', $villa) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('villas.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @php
        $enTranslation = $villa->getTranslation('en');
    @endphp

    <div class="row">
        <div class="col-md-8">
            <!-- Featured Image -->
            @php
                $featuredImage = $villa->media()->where('is_featured', true)->first();
            @endphp

            @if($featuredImage)
                <div class="card mb-3">
                    <img
                        src="{{ asset($featuredImage->image_path) }}"
                        alt="{{ $enTranslation?->title }}"
                        class="card-img-top"
                        style="max-height: 400px; object-fit: cover;"
                    >
                </div>
            @endif

            <!-- Basic Info -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Description</h5>
                </div>
                <div class="card-body">
                    {{ $enTranslation?->description }}
                </div>
            </div>

            <!-- Details -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Villa Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($enTranslation?->price_shoulder_season)
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Shoulder Season:</strong></p>
                            <p class="text-info">€{{ number_format($enTranslation?->price_shoulder_season, 2) }}</p>
                            <small class="text-muted">2 nights min</small>
                        </div>
                        @endif
                        @if($enTranslation?->price_high_season)
                        <div class="col-md-4">
                            <p class="mb-1"><strong>High Season:</strong></p>
                            <p class="text-warning">€{{ number_format($enTranslation?->price_high_season, 2) }}</p>
                            <small class="text-muted">3 nights min</small>
                        </div>
                        @endif
                        @if($enTranslation?->price_peak_season)
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Peak Season:</strong></p>
                            <p class="text-danger">€{{ number_format($enTranslation?->price_peak_season, 2) }}</p>
                            <small class="text-muted">5 nights min</small>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Max Guests:</strong></p>
                            <p>{{ $enTranslation?->max_guests }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Min Guests:</strong></p>
                            <p>{{ $enTranslation?->min_guests ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Amenities -->
            @if($enTranslation?->amenities)
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Amenities</h5>
                    </div>
                    <div class="card-body">
                        {!! nl2br(e($enTranslation?->amenities)) !!}
                    </div>
                </div>
            @endif

            <!-- Rules -->
            @if($enTranslation?->rules)
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Rules & Guidelines</h5>
                    </div>
                    <div class="card-body">
                        {!! nl2br(e($enTranslation?->rules)) !!}
                    </div>
                </div>
            @endif

            <!-- Gallery -->
            @if($villa->media()->where('is_featured', false)->count() > 0)
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Gallery</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($villa->media()->where('is_featured', false)->ordered()->get() as $media)
                                <div class="col-md-4">
                                    <img
                                        src="{{ asset($media->image_path) }}"
                                        alt="{{ $media->alt_text_en }}"
                                        class="img-fluid rounded"
                                        style="height: 200px; object-fit: cover; width: 100%;"
                                    >
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Status</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Publication:</strong><br>
                        @if($villa->published_at)
                            <span class="badge bg-success">Published</span><br>
                            <small class="text-muted">{{ $villa->published_at->format('M d, Y') }}</small>
                        @else
                            <span class="badge bg-warning">Draft</span>
                        @endif
                    </p>

                    @if($villa->featured)
                        <p class="mb-0">
                            <strong>Featured:</strong><br>
                            <span class="badge bg-danger">Yes</span>
                        </p>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Metadata</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><small><strong>Slug:</strong></small></p>
                    <p class="mb-3"><code>{{ $villa->slug }}</code></p>

                    <p class="mb-1"><small><strong>Created:</strong></small></p>
                    <p class="mb-3"><small>{{ $villa->created_at->format('M d, Y') }}</small></p>

                    <p class="mb-0"><small><strong>Updated:</strong></small></p>
                    <p><small>{{ $villa->updated_at->format('M d, Y') }}</small></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
