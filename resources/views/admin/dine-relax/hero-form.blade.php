@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Edit Hero Section</h1>
        <a href="{{ route('dine-relax.edit') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dine-relax.hero.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card mb-4">
            <div class="card-header">Hero Image</div>
            <div class="card-body">
                @if($page->hero_image_path)
                    <div class="mb-3">
                        <label class="form-label">Current Image</label>
                        <div>
                            <img src="{{ asset('storage/' . $page->hero_image_path) }}" alt="{{ $page->hero_image_alt }}" class="img-fluid rounded" style="max-height: 400px; object-fit: cover;">
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Upload New Image</label>
                        <input type="file" name="hero_image" class="form-control @error('hero_image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                        @error('hero_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="text-muted">JPG, PNG, or WebP. Max 5MB. Recommended: 1920x800px</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Image Alt Text</label>
                        <input type="text" name="hero_image_alt" class="form-control @error('hero_image_alt') is-invalid @enderror" value="{{ old('hero_image_alt', $page->hero_image_alt) }}" placeholder="Describe the image">
                        @error('hero_image_alt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">English Content</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Tagline (EN)</label>
                    <input type="text" name="hero_tagline_en" class="form-control @error('hero_tagline_en') is-invalid @enderror" value="{{ old('hero_tagline_en', $page->translation('en')?->hero_tagline) }}" placeholder="e.g., Experience Luxury">
                    @error('hero_tagline_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Title (EN) <span class="text-danger">*</span></label>
                    <input type="text" name="hero_title_en" class="form-control @error('hero_title_en') is-invalid @enderror" value="{{ old('hero_title_en', $page->translation('en')?->hero_title) }}" placeholder="e.g., Dine & Relax" required>
                    @error('hero_title_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Lead Paragraph (EN)</label>
                    <textarea name="hero_lead_en" class="form-control @error('hero_lead_en') is-invalid @enderror" rows="4" placeholder="Describe the dining and relaxation experience...">{{ old('hero_lead_en', $page->translation('en')?->hero_lead) }}</textarea>
                    @error('hero_lead_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">French Content</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Tagline (FR)</label>
                    <input type="text" name="hero_tagline_fr" class="form-control @error('hero_tagline_fr') is-invalid @enderror" value="{{ old('hero_tagline_fr', $page->translation('fr')?->hero_tagline) }}" placeholder="e.g., Découvrez le Luxe">
                    @error('hero_tagline_fr') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Title (FR) <span class="text-danger">*</span></label>
                    <input type="text" name="hero_title_fr" class="form-control @error('hero_title_fr') is-invalid @enderror" value="{{ old('hero_title_fr', $page->translation('fr')?->hero_title) }}" placeholder="e.g., Dîner & Détente" required>
                    @error('hero_title_fr') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Lead Paragraph (FR)</label>
                    <textarea name="hero_lead_fr" class="form-control @error('hero_lead_fr') is-invalid @enderror" rows="4" placeholder="Décrivez l'expérience culinaire et de détente...">{{ old('hero_lead_fr', $page->translation('fr')?->hero_lead) }}</textarea>
                    @error('hero_lead_fr') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg"></i> Save Hero Section
            </button>
            <a href="{{ route('dine-relax.edit') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
