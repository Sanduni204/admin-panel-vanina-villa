@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Menu Category</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('dine-relax.menus.index') }}" class="btn btn-outline-secondary">Back to Menu Categories</a>
            <a href="{{ route('dine-relax.edit') }}" class="btn btn-secondary">Back to Dine & Relax</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Edit Menu Category: {{ $menu->type }}</h5>
        </div>
        <form id="menuEditForm" method="POST" action="{{ route('dine-relax.menus.save', $menu->type) }}" enctype="multipart/form-data">
            @csrf
            @method('POST')

            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Category Name (English) *</label>
                    <input type="text" id="categoryType" name="type" class="form-control" value="{{ old('type', $menu->translations->where('locale', 'en')->first()?->title ?? $menu->type) }}" required>
                    <small class="text-muted">This updates the displayed name; the category key stays the same.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Category Name (French) *</label>
                    <input type="text" id="categoryTypeFr" name="type_fr" class="form-control" value="{{ old('type_fr', $menu->translations->where('locale', 'fr')->first()?->title ?? $menu->type) }}" required>
                    <small class="text-muted">This updates the displayed name; the category key stays the same.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">PDF File</label>
                    @if($menu->file_path && Storage::disk('public')->exists($menu->file_path))
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-file-pdf"></i> Current file: <strong>{{ $menu->file_name }}</strong>
                            <a href="{{ Storage::disk('public')->url($menu->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>
                    @endif
                    <input type="file" name="file" class="form-control" accept="application/pdf">
                    <small class="text-muted">Leave blank to keep current file. Max 15MB</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Card Image</label>
                    @if($menu->card_image_path)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $menu->card_image_path) }}" alt="Card Image" style="max-height: 200px; max-width: 300px;" class="img-thumbnail">
                        </div>
                    @else
                        <div class="alert alert-secondary mb-3">
                            <small>No image uploaded yet</small>
                        </div>
                    @endif
                    <input type="file" name="card_image" class="form-control" accept="image/jpeg,image/png,image/webp">
                    <small class="text-muted">Leave blank to keep current image. JPG, PNG, or WebP. Max 5MB.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Image Alt Text (English)</label>
                    <input type="text" name="card_image_alt_en" class="form-control" placeholder="Describe the image in English" value="{{ old('card_image_alt_en', $menu->card_image_alt) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Image Alt Text (French)</label>
                    <input type="text" name="card_image_alt_fr" class="form-control" placeholder="Décrire l'image en français" value="{{ old('card_image_alt_fr', $menu->card_image_alt_fr) }}">
                </div>

                <!-- Hidden translation inputs to satisfy validation without showing the sections -->
                <input type="hidden" name="title_en" id="title_en" value="{{ old('title_en', $menu->translations->where('locale', 'en')->first()?->title ?? $menu->type) }}">
                <input type="hidden" name="description_en" id="description_en" value="{{ old('description_en', $menu->translations->where('locale', 'en')->first()?->description) }}">
                <input type="hidden" name="button_label_en" id="button_label_en" value="{{ old('button_label_en', $menu->translations->where('locale', 'en')->first()?->button_label ?? 'Download Menu') }}">
                <input type="hidden" name="version_note_en" id="version_note_en" value="{{ old('version_note_en', $menu->translations->where('locale', 'en')->first()?->version_note) }}">

                <input type="hidden" name="title_fr" id="title_fr" value="{{ old('title_fr', $menu->translations->where('locale', 'fr')->first()?->title ?? $menu->type) }}">
                <input type="hidden" name="description_fr" id="description_fr" value="{{ old('description_fr', $menu->translations->where('locale', 'fr')->first()?->description) }}">
                <input type="hidden" name="button_label_fr" id="button_label_fr" value="{{ old('button_label_fr', $menu->translations->where('locale', 'fr')->first()?->button_label ?? 'Télécharger le Menu') }}">
                <input type="hidden" name="version_note_fr" id="version_note_fr" value="{{ old('version_note_fr', $menu->translations->where('locale', 'fr')->first()?->version_note) }}">

                <hr>

            </div>

            <div class="card-footer d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Category
                </button>
                <a href="{{ route('dine-relax.menus.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('menuEditForm').addEventListener('submit', function(e) {
    const type = document.getElementById('categoryType').value;
    const typeFr = document.getElementById('categoryTypeFr').value;

    // Auto-fill hidden translation fields
    document.getElementById('title_en').value = type;
    document.getElementById('title_fr').value = typeFr;
});
</script>
@endsection
