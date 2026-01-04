@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Edit Villa</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('villas.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Villas
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('villas.update', $villa) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-8">
                <!-- Basic Information -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $enTranslation = $villa->translations()->where('locale', 'en')->first();
                            $frTranslation = $villa->translations()->where('locale', 'fr')->first();
                        @endphp

                        <x-bilingual-editor
                            field="title"
                            label="Villa Title"
                            :value_en="$enTranslation?->title"
                            :value_fr="$frTranslation?->title"
                            required
                        />

                        <x-bilingual-editor
                            field="description"
                            label="Description"
                            type="textarea"
                            :value_en="$enTranslation?->description"
                            :value_fr="$frTranslation?->description"
                            required
                        />

                        <x-bilingual-editor
                            field="amenities"
                            label="Features"
                            type="textarea"
                            :value_en="$enTranslation?->amenities"
                            :value_fr="$frTranslation?->amenities"
                        />

                        <x-bilingual-editor
                            field="rules"
                            label="Rules & Guidelines"
                            type="textarea"
                            :value_en="$enTranslation?->rules"
                            :value_fr="$frTranslation?->rules"
                        />
                    </div>
                </div>

                <!-- Details -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Villa Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price_shoulder_season" class="form-label">Shoulder Season Price (€)</label>
                                <input
                                    type="number"
                                    id="price_shoulder_season"
                                    name="price_shoulder_season"
                                    class="form-control @error('price_shoulder_season') is-invalid @enderror"
                                    step="0.01"
                                    min="0"
                                    value="{{ old('price_shoulder_season', $enTranslation?->price_shoulder_season) }}"
                                >
                                <small class="text-muted">01 July to 31 Oct | 2 Nights minimum</small>
                                @error('price_shoulder_season')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="price_high_season" class="form-label">High Season Price (€)</label>
                                <input
                                    type="number"
                                    id="price_high_season"
                                    name="price_high_season"
                                    class="form-control @error('price_high_season') is-invalid @enderror"
                                    step="0.01"
                                    min="0"
                                    value="{{ old('price_high_season', $enTranslation?->price_high_season) }}"
                                >
                                <small class="text-muted">06 Jan to 05 Feb | 10 Mar to 03 Apr | 01 Nov to 16 Dec | 3 Nights minimum</small>
                                @error('price_high_season')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="price_peak_season" class="form-label">Peak Season Price (€)</label>
                                <input
                                    type="number"
                                    id="price_peak_season"
                                    name="price_peak_season"
                                    class="form-control @error('price_peak_season') is-invalid @enderror"
                                    step="0.01"
                                    min="0"
                                    value="{{ old('price_peak_season', $enTranslation?->price_peak_season) }}"
                                >
                                <small class="text-muted">17 Dec to 05 Jan | 06 Feb to 09 Mar | 04 Apr to 30 Apr | 5 Nights minimum</small>
                                @error('price_peak_season')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="min_guests" class="form-label">Minimum Guests</label>
                                <input
                                    type="number"
                                    id="min_guests"
                                    name="min_guests"
                                    class="form-control @error('min_guests') is-invalid @enderror"
                                    min="1"
                                    max="20"
                                    value="{{ old('min_guests', $enTranslation?->min_guests) }}"
                                >
                                @error('min_guests')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="max_guests" class="form-label">Max Guests</label>
                                <input
                                    type="number"
                                    id="max_guests"
                                    name="max_guests"
                                    class="form-control @error('max_guests') is-invalid @enderror"
                                    min="1"
                                    max="20"
                                    value="{{ old('max_guests', $enTranslation?->max_guests) }}"
                                    required
                                >
                                @error('max_guests')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Featured Image</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $featuredImage = $villa->media()->where('is_featured', true)->first();
                        @endphp

                        @if($featuredImage)
                            <div class="mb-3">
                                <p class="text-muted">Current featured image:</p>
                                <img
                                    src="{{ asset($featuredImage->image_path) }}"
                                    alt="{{ $enTranslation?->title }}"
                                    class="img-fluid rounded"
                                    style="max-height: 200px; max-width: 100%;"
                                >
                                <button
                                    type="submit"
                                    form="delete-featured-{{ $featuredImage->id }}"
                                    class="btn btn-danger btn-sm mt-2"
                                    onclick="return confirm('Are you sure you want to remove the featured image?');"
                                >
                                    <i class="bi bi-trash"></i> Remove Featured Image
                                </button>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="featured_image" class="form-label">Upload New Featured Image</label>
                            <input
                                type="file"
                                id="featured_image"
                                name="featured_image"
                                class="form-control @error('featured_image') is-invalid @enderror"
                                accept="image/jpeg,image/png,image/webp"
                            >
                            <small class="form-text text-muted">Max 5MB. Formats: JPG, PNG, WebP</small>
                            @error('featured_image')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div id="featuredImagePreview"></div>
                    </div>
                </div>

                <!-- Gallery Images -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Gallery Images</h5>
                    </div>
                    <div class="card-body">
                        @if($villa->media()->where('is_featured', false)->count() > 0)
                            <div class="mb-3">
                                <p class="text-muted mb-2">Current gallery images:</p>
                                <div class="row g-2">
                                    @foreach($villa->media()->where('is_featured', false)->ordered()->get() as $media)
                                        <div class="col-md-3">
                                            <div class="card">
                                                <img
                                                    src="{{ asset($media->image_path) }}"
                                                    class="card-img-top"
                                                    alt="{{ $media->alt_text_en }}"
                                                    style="height: 150px; object-fit: cover;"
                                                >
                                                <div class="card-body p-2">
                                                    <button
                                                        type="submit"
                                                        form="delete-media-{{ $media->id }}"
                                                        class="btn btn-danger btn-sm w-100"
                                                        onclick="return confirm('Are you sure you want to remove this image?');"
                                                    >
                                                        <i class="bi bi-trash"></i> Remove
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <x-image-uploader />
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Publication Settings</h5>
                    </div>
                    <div class="card-body">
                        <!-- Status checkboxes removed -->
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-check-lg"></i> Update Villa
                        </button>
                        <a href="{{ route('villas.index') }}" class="btn btn-light w-100">
                            <i class="bi bi-x-lg"></i> Cancel
                        </a>
                    </div>
                </div>

                <div class="card mt-3 border-danger">
                    <div class="card-body">
                        <p class="text-danger mb-2"><small><strong>Danger Zone</strong></small></p>
                        <button
                            type="submit"
                            form="delete-villa-{{ $villa->id }}"
                            class="btn btn-danger btn-sm w-100"
                            onclick="return confirm('Are you sure? This can be undone.')"
                        >
                            <i class="bi bi-trash"></i> Delete Villa
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Hidden delete forms to avoid nesting inside the main form --}}
    @if($featuredImage)
        <form id="delete-featured-{{ $featuredImage->id }}" action="{{ route('villas.media.delete', [$villa, $featuredImage]) }}" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    @endif

    @foreach($villa->media()->where('is_featured', false)->get() as $media)
        <form id="delete-media-{{ $media->id }}" action="{{ route('villas.media.delete', [$villa, $media]) }}" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

    <form id="delete-villa-{{ $villa->id }}" action="{{ route('villas.destroy', $villa) }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
</div>

<script>
document.getElementById('featured_image').addEventListener('change', function(e) {
    const preview = document.getElementById('featuredImagePreview');
    preview.innerHTML = '';

    if (e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'img-fluid rounded';
            img.style.maxHeight = '200px';
            preview.appendChild(img);
        };
        reader.readAsDataURL(e.target.files[0]);
    }
});
</script>
@endsection
