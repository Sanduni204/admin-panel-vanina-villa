<form action="{{ route('villa-pages.individual.update', $villa->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="locale" value="{{ $locale }}">

    <!-- Section 1: Header -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light border-bottom">
            <h5 class="card-title mb-0"><i class="bi bi-image"></i> Section 1: Header</h5>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <label class="form-label fw-bold">Hero Image</label>
                <div class="border-2 border-dashed rounded p-4 text-center heroImageDropZone" data-locale="{{ $locale }}">
                    @if ($page && $page->hero_image_path)
                        <img src="{{ asset('storage/' . $page->hero_image_path) }}" alt="Hero" class="img-fluid mb-3" style="max-height: 200px;">
                    @else
                        <i class="bi bi-cloud-arrow-up" style="font-size: 2rem; color: #0d6efd;"></i>
                    @endif
                    <p class="mt-3 mb-0 text-muted">Drag & drop or click to upload</p>
                    <input type="file" name="hero_image" class="d-none heroImageInput" accept="image/*" data-locale="{{ $locale }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Villa Name</label>
                <input type="text" name="name" class="form-control" placeholder="Villa name" value="{{ old('name', $page?->name) }}">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Hero Content</label>
                <textarea name="hero_content" class="form-control" rows="4" placeholder="Hero content for this language">{{ old('hero_content', $page?->hero_content) }}</textarea>
            </div>
        </div>
    </div>

    <!-- Section 2: Brand Identity -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light border-bottom">
            <h5 class="card-title mb-0"><i class="bi bi-star"></i> Section 2: Brand Identity</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label fw-bold">Topic</label>
                <input type="text" name="brand_topic" class="form-control" placeholder="Topic/Title" value="{{ old('brand_topic', $page?->brand_topic) }}">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Express Sentence</label>
                <input type="text" name="brand_express_sentence" class="form-control" placeholder="e.g., A sanctuary of peace" value="{{ old('brand_express_sentence', $page?->brand_express_sentence) }}">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Description</label>
                <textarea name="brand_description" class="form-control richtext" rows="6" placeholder="Villa description and story">{{ old('brand_description', $page?->brand_description) }}</textarea>
            </div>
        </div>
    </div>

    <!-- Section 3: Features -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light border-bottom">
            <h5 class="card-title mb-0"><i class="bi bi-puzzle"></i> Section 3: Features</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label fw-bold">Features Description</label>
                <textarea name="features_description" class="form-control richtext" rows="6" placeholder="Describe the villa features and amenities">{{ old('features_description', $page?->features_description) }}</textarea>
            </div>
        </div>
    </div>

    <!-- Section 5: Image Gallery -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light border-bottom">
            <h5 class="card-title mb-0"><i class="bi bi-images"></i> Section 5: Image Gallery</h5>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <label class="form-label fw-bold">Gallery Images</label>
                <div class="border-2 border-dashed rounded p-4 text-center galleryDropZone" data-locale="{{ $locale }}" data-villa-id="{{ $villa->id }}">
                    <i class="bi bi-cloud-arrow-up" style="font-size: 2rem; color: #0d6efd;"></i>
                    <p class="mt-3 mb-0 text-muted">Drag & drop 10-20 photos or click to browse</p>
                    <input type="file" name="gallery_images[]" class="d-none galleryImageInput" accept="image/*" multiple data-locale="{{ $locale }}">
                </div>
            </div>

            @if ($page && $page->gallery_images)
                <div class="mb-3">
                    <label class="form-label fw-bold">Current Gallery</label>
                    <div class="row gallery-grid" data-locale="{{ $locale }}" data-villa-id="{{ $villa->id }}" id="sortableGallery">
                        @foreach ($page->gallery_images as $index => $image)
                            <div class="col-md-3 mb-3 gallery-item" data-index="{{ $index }}">
                                <div class="card position-relative">
                                    <img src="{{ asset('storage/' . $image) }}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 delete-gallery-image" data-index="{{ $index }}" data-locale="{{ $locale }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Save Button -->
    <div class="d-flex gap-2 mb-4">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle"></i> Save Changes
        </button>
    </div>
</form>

<script>
    // Hero image drop zone for this form
    document.querySelectorAll('.heroImageDropZone').forEach(zone => {
        const locale = zone.dataset.locale;
        const input = zone.querySelector('.heroImageInput');

        zone.addEventListener('dragover', (e) => {
            e.preventDefault();
            zone.classList.add('bg-light');
        });

        zone.addEventListener('dragleave', () => {
            zone.classList.remove('bg-light');
        });

        zone.addEventListener('drop', (e) => {
            e.preventDefault();
            zone.classList.remove('bg-light');
            input.files = e.dataTransfer.files;
        });

        zone.addEventListener('click', () => {
            input.click();
        });
    });

    // Gallery image drop zone
    document.querySelectorAll('.galleryDropZone').forEach(zone => {
        const locale = zone.dataset.locale;
        const villaId = zone.dataset.villaId;
        const input = zone.querySelector('.galleryImageInput');

        zone.addEventListener('dragover', (e) => {
            e.preventDefault();
            zone.classList.add('bg-light');
        });

        zone.addEventListener('dragleave', () => {
            zone.classList.remove('bg-light');
        });

        zone.addEventListener('drop', (e) => {
            e.preventDefault();
            zone.classList.remove('bg-light');
            input.files = e.dataTransfer.files;
            uploadGalleryImages(input.files, villaId, locale);
        });

        zone.addEventListener('click', () => {
            input.click();
        });

        input.addEventListener('change', (e) => {
            uploadGalleryImages(e.target.files, villaId, locale);
        });
    });

    // Delete gallery image
    document.querySelectorAll('.delete-gallery-image').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const index = btn.dataset.index;
            const locale = btn.dataset.locale;
            const villaId = document.querySelector('[data-villa-id]').dataset.villaId;

            if (confirm('Delete this image?')) {
                fetch(`/admin/villa-pages/${villaId}/gallery/${index}?locale=${locale}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            }
        });
    });

    // Upload gallery images
    function uploadGalleryImages(files, villaId, locale) {
        const formData = new FormData();
        Array.from(files).forEach(file => {
            formData.append('gallery_images[]', file);
        });
        formData.append('locale', locale);

        fetch(`/admin/villa-pages/${villaId}/gallery-upload`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
</script>
