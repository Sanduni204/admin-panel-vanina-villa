@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">{{ $block ? 'Edit' : 'Create' }} Block</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $block ? route('dine-relax.blocks.update', ['block' => $block->id]) : route('dine-relax.blocks.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($block)
            @method('PUT')
        @endif

        <div class="card mb-4">
            <div class="card-header">Basic Information</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Block Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $block?->name) }}" placeholder="e.g., Restaurant, Bar, Pool, Beach" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Block Image</label>
                    @if($block?->image_path)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $block->image_path) }}" alt="{{ $block->image_alt }}" class="img-fluid rounded shadow-sm" style="max-height: 250px; object-fit: cover;">
                            <div class="mt-2">
                                <small class="text-muted"><strong>Current:</strong> {{ basename($block->image_path) }}</small>
                            </div>
                        </div>
                    @endif
                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small class="text-muted d-block mt-1">JPG, PNG, or WebP. Max 5MB.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Image Alt Text</label>
                    <input type="text" name="image_alt" class="form-control @error('image_alt') is-invalid @enderror" value="{{ old('image_alt', $block?->image_alt) }}">
                    @error('image_alt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Display Order</label>
                    <input type="number" name="display_order" class="form-control @error('display_order') is-invalid @enderror" value="{{ old('display_order', $block?->display_order ?? 0) }}">
                    @error('display_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">English Content</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Heading (EN)</label>
                    <input type="text" name="heading_en" class="form-control @error('heading_en') is-invalid @enderror" value="{{ old('heading_en', $block?->translation('en')?->heading) }}" required>
                    @error('heading_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Body (EN)</label>
                    <textarea name="body_en" class="form-control @error('body_en') is-invalid @enderror" rows="4">{{ old('body_en', $block?->translation('en')?->body) }}</textarea>
                    @error('body_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">French Content</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Heading (FR)</label>
                    <input type="text" name="heading_fr" class="form-control @error('heading_fr') is-invalid @enderror" value="{{ old('heading_fr', $block?->translation('fr')?->heading) }}" required>
                    @error('heading_fr') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Body (FR)</label>
                    <textarea name="body_fr" class="form-control @error('body_fr') is-invalid @enderror" rows="4">{{ old('body_fr', $block?->translation('fr')?->body) }}</textarea>
                    @error('body_fr') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        @if($block)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Gallery Images</span>
                <span class="badge bg-secondary">{{ $block->gallery?->count() ?? 0 }} images</span>
            </div>
            <div class="card-body">
                @if($block->gallery && $block->gallery->count() > 0)
                    <div class="row g-3 mb-4">
                        @foreach($block->gallery->sortBy('display_order') as $image)
                            <div class="col-md-3">
                                <div class="position-relative">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $image->image_alt }}" class="img-fluid rounded shadow-sm" style="width: 100%; height: 150px; object-fit: cover;">
                                    <div class="mt-2">
                                        <small class="text-muted d-block">{{ $image->image_alt }}</small>
                                        <small class="text-muted">Order: {{ $image->display_order }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle"></i> No gallery images yet. Upload multiple images below.
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label">Upload Gallery Images</label>
                    <input type="file" name="gallery_images[]" class="form-control @error('gallery_images') is-invalid @enderror" accept="image/jpeg,image/png,image/webp" multiple>
                    @error('gallery_images') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small class="text-muted d-block mt-1">Select multiple images (JPG, PNG, WebP). Max 5MB each.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Gallery Images Alt Text (applies to all new uploads)</label>
                    <input type="text" name="gallery_images_alt" class="form-control" placeholder="e.g., Restaurant interior photos" value="{{ old('gallery_images_alt') }}">
                    <small class="text-muted">This will be applied to all newly uploaded images.</small>
                </div>
            </div>
        </div>
        @else
        <div class="alert alert-warning">
            <i class="bi bi-info-circle"></i> <strong>Gallery images</strong> can be added after creating the block.
        </div>
        @endif

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">{{ $block ? 'Update' : 'Create' }} Block</button>
            <a href="{{ route('dine-relax.edit') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
