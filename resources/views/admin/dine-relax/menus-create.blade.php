@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Add New Menu Category</h1>
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
            <h5 class="mb-0">Add New Menu Category</h5>
        </div>
        <form id="menuCreateForm" method="POST" action="{{ route('dine-relax.menus.store') }}" enctype="multipart/form-data" onsubmit="handleCreateForm(event)">
            @csrf
            @method('POST')

            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Category Name (English) *</label>
                    <input type="text" id="categoryType" name="type" class="form-control" placeholder="e.g., Beverage, Snacking, Today's Special" required>
                    <small class="text-muted">Enter the menu category name in English</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Category Name (French) *</label>
                    <input type="text" id="categoryTypeFr" name="type_fr" class="form-control" placeholder="e.g., Boisson, Collation, Spécial d'aujourd'hui" required>
                    <small class="text-muted">Enter the menu category name in French</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">PDF File *</label>
                    <input type="file" name="file" class="form-control" accept="application/pdf" required>
                    <small class="text-muted">Max 15MB</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Card Image</label>
                    <input type="file" name="card_image" class="form-control" accept="image/jpeg,image/png,image/webp">
                    <small class="text-muted">JPG, PNG, or WebP. Max 5MB.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Image Alt Text (English)</label>
                    <input type="text" name="card_image_alt_en" class="form-control" placeholder="Describe the image in English">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Image Alt Text (French)</label>
                    <input type="text" name="card_image_alt_fr" class="form-control" placeholder="Décrire l'image en français">
                </div>

                <!-- Hidden translation inputs to satisfy validation without showing the sections -->
                <input type="hidden" name="title_en" id="title_en" value="">
                <input type="hidden" name="description_en" id="description_en" value="">
                <input type="hidden" name="button_label_en" id="button_label_en" value="Download Menu">
                <input type="hidden" name="version_note_en" id="version_note_en" value="">

                <input type="hidden" name="title_fr" id="title_fr" value="">
                <input type="hidden" name="description_fr" id="description_fr" value="">
                <input type="hidden" name="button_label_fr" id="button_label_fr" value="Télécharger le Menu">
                <input type="hidden" name="version_note_fr" id="version_note_fr" value="">

                <hr>

            </div>

            <div class="card-footer d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Category
                </button>
                <a href="{{ route('dine-relax.menus.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function handleCreateForm(e) {
    e.preventDefault();

    const type = document.getElementById('categoryType').value;
    const typeFr = document.getElementById('categoryTypeFr').value;

    // Validate required fields
    if (!type || !typeFr) {
        alert('Category Name fields are required');
        return;
    }

    // Auto-fill hidden translation fields
    document.getElementById('title_en').value = type;
    document.getElementById('title_fr').value = typeFr;

    // Submit the form to the /store endpoint
    document.getElementById('menuCreateForm').submit();
}
</script>
@endsection
