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
                            label="Amenities"
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

                            <div class="col-md-6 mb-3">
                                <label for="bedrooms" class="form-label">Bedrooms</label>
                                <input
                                    type="number"
                                    id="bedrooms"
                                    name="bedrooms"
                                    class="form-control @error('bedrooms') is-invalid @enderror"
                                    min="1"
                                    value="{{ old('bedrooms', $enTranslation?->bedrooms) }}"
                                    required
                                >
                                @error('bedrooms')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="bathrooms" class="form-label">Bathrooms</label>
                                <input
                                    type="number"
                                    id="bathrooms"
                                    name="bathrooms"
                                    class="form-control @error('bathrooms') is-invalid @enderror"
                                    step="0.5"
                                    min="0.5"
                                    value="{{ old('bathrooms', $enTranslation?->bathrooms) }}"
                                    required
                                >
                                @error('bathrooms')
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
                                    src="{{ asset('storage/' . $featuredImage->image_path) }}"
                                    alt="{{ $enTranslation?->title }}"
                                    class="img-fluid rounded"
                                    style="max-height: 200px; max-width: 100%;"
                                >
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
                                                    src="{{ asset('storage/' . $media->image_path) }}"
                                                    class="card-img-top"
                                                    alt="{{ $media->alt_text_en }}"
                                                    style="height: 150px; object-fit: cover;"
                                                >
                                                <div class="card-body p-2">
                                                    <form action="#" method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="button" class="btn btn-danger btn-sm w-100">
                                                            <i class="bi bi-trash"></i> Remove
                                                        </button>
                                                    </form>
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
                        <div class="form-check mb-3">
                            <input
                                type="checkbox"
                                id="published"
                                name="published"
                                class="form-check-input"
                                value="1"
                                {{ old('published', $villa->published_at) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="published">
                                Published
                            </label>
                            @if($villa->published_at)
                                <small class="text-muted d-block">
                                    Published: {{ $villa->published_at->format('M d, Y') }}
                                </small>
                            @endif
                        </div>

                        <div class="form-check mb-3">
                            <input
                                type="checkbox"
                                id="featured"
                                name="featured"
                                class="form-check-input"
                                value="1"
                                {{ old('featured', $villa->featured) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="featured">
                                Featured villa
                            </label>
                        </div>
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
                        <form action="{{ route('villas.destroy', $villa) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="btn btn-danger btn-sm w-100"
                                onclick="return confirm('Are you sure? This can be undone.')"
                            >
                                <i class="bi bi-trash"></i> Delete Villa
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
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
