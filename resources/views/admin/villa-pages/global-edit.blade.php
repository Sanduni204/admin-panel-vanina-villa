@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0"><i class="bi bi-house-door"></i> Global Villa Pages - "Our Villas"</h2>
            <p class="text-muted small mt-2">Manage the main landing page for all villas</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('villa-pages.global.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Section 1: Hero Editor -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light border-bottom">
                <h5 class="card-title mb-0"><i class="bi bi-image"></i> Section 1: Hero Editor</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <label class="form-label fw-bold">Hero Background Image</label>
                    <div class="border-2 border-dashed rounded p-4 text-center" id="heroImageDropZone">
                        <i class="bi bi-cloud-arrow-up" style="font-size: 2rem; color: #0d6efd;"></i>
                        <p class="mt-3 mb-0 text-muted">Drag & drop your image here or click to browse</p>
                        <small class="text-muted d-block mt-1">JPG, PNG, WebP | Max 5MB</small>
                        <input type="file" name="hero_image" id="heroImageInput" class="d-none" accept="image/*">
                    </div>
                    @error('hero_image')
                        <small class="text-danger d-block mt-2">{{ $message }}</small>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Hero Content <span class="badge bg-primary">EN</span></label>
                            <textarea name="hero_content_en" class="form-control" rows="4" placeholder="Enter hero content in English">{{ old('hero_content_en') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Hero Content <span class="badge bg-info">FR</span></label>
                            <textarea name="hero_content_fr" class="form-control" rows="4" placeholder="Entrez le contenu héros en français">{{ old('hero_content_fr') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Intro Editor -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light border-bottom">
                <h5 class="card-title mb-0"><i class="bi bi-pencil-square"></i> Section 2: Intro Editor</h5>
            </div>
            <div class="card-body">
                <!-- Intro Sentence -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Intro Sentence <span class="badge bg-primary">EN</span></label>
                        <input type="text" name="intro_sentence_en" class="form-control" placeholder="Enter intro sentence in English" value="{{ old('intro_sentence_en') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Intro Sentence <span class="badge bg-info">FR</span></label>
                        <input type="text" name="intro_sentence_fr" class="form-control" placeholder="Entrez la phrase d'introduction en français" value="{{ old('intro_sentence_fr') }}">
                    </div>
                </div>

                <!-- Topic (H2) -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Topic (H2) <span class="badge bg-primary">EN</span></label>
                        <input type="text" name="intro_topic_en" class="form-control" placeholder="Enter topic heading in English" value="{{ old('intro_topic_en') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Topic (H2) <span class="badge bg-info">FR</span></label>
                        <input type="text" name="intro_topic_fr" class="form-control" placeholder="Entrez le titre du sujet en français" value="{{ old('intro_topic_fr') }}">
                    </div>
                </div>

                <!-- Description -->
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Description <span class="badge bg-primary">EN</span></label>
                        <textarea name="intro_description_en" class="form-control" rows="6" placeholder="Enter description in English">{{ old('intro_description_en') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Description <span class="badge bg-info">FR</span></label>
                        <textarea name="intro_description_fr" class="form-control" rows="6" placeholder="Entrez la description en français">{{ old('intro_description_fr') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="d-flex gap-2 mb-4">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Save Changes
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> Cancel
            </a>
        </div>
    </form>
</div>

<script>
    // Drag and drop for hero image
    const heroDropZone = document.getElementById('heroImageDropZone');
    const heroImageInput = document.getElementById('heroImageInput');

    heroDropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        heroDropZone.classList.add('bg-light');
    });

    heroDropZone.addEventListener('dragleave', () => {
        heroDropZone.classList.remove('bg-light');
    });

    heroDropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        heroDropZone.classList.remove('bg-light');
        heroImageInput.files = e.dataTransfer.files;
    });

    heroDropZone.addEventListener('click', () => {
        heroImageInput.click();
    });
</script>
@endsection
