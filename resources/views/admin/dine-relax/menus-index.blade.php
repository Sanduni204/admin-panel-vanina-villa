@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Dine & Relax - Menus</h1>
        <a href="{{ route('dine-relax.edit') }}" class="btn btn-secondary">Back to Dine & Relax</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        @foreach(($types ?? ['beverage','snacking','today','breakfast']) as $type)
            @php
                $menu = $menus->firstWhere('type', $type);
                $mtEn = $menu?->translation('en');
                $mtFr = $menu?->translation('fr');
            @endphp
            <div class="col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="mb-0 text-capitalize">{{ $type }}</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('dine-relax.menus.save', $type) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @if($menu && $menu->file_name)
                                <div class="alert alert-info alert-sm mb-3">
                                    <small>Current: <strong>{{ $menu->file_name }}</strong></small>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label">PDF File</label>
                                <input type="file" name="file" class="form-control form-control-sm @error('file') is-invalid @enderror" accept="application/pdf">
                                @error('file') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Card Image</label>
                                <input type="file" name="card_image" class="form-control form-control-sm @error('card_image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                                @error('card_image') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Image Alt</label>
                                <input type="text" name="card_image_alt" class="form-control form-control-sm" value="{{ old('card_image_alt', $menu?->card_image_alt) }}" placeholder="Alt text">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Title (EN)</label>
                                <input type="text" name="title_en" class="form-control form-control-sm @error('title_en') is-invalid @enderror" value="{{ old('title_en', $mtEn?->title) }}" required>
                                @error('title_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Title (FR)</label>
                                <input type="text" name="title_fr" class="form-control form-control-sm @error('title_fr') is-invalid @enderror" value="{{ old('title_fr', $mtFr?->title) }}" required>
                                @error('title_fr') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Button Label (EN)</label>
                                <input type="text" name="button_label_en" class="form-control form-control-sm @error('button_label_en') is-invalid @enderror" value="{{ old('button_label_en', $mtEn?->button_label) }}" maxlength="100" required>
                                @error('button_label_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Button Label (FR)</label>
                                <input type="text" name="button_label_fr" class="form-control form-control-sm @error('button_label_fr') is-invalid @enderror" value="{{ old('button_label_fr', $mtFr?->button_label) }}" maxlength="100" required>
                                @error('button_label_fr') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Version Note (EN)</label>
                                <input type="text" name="version_note_en" class="form-control form-control-sm" value="{{ old('version_note_en', $mtEn?->version_note) }}" placeholder="e.g., v1.0" maxlength="100">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Version Note (FR)</label>
                                <input type="text" name="version_note_fr" class="form-control form-control-sm" value="{{ old('version_note_fr', $mtFr?->version_note) }}" placeholder="e.g., v1.0" maxlength="100">
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="is_active" id="{{ $type }}_active" {{ $menu?->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="{{ $type }}_active">Active</label>
                            </div>

                            <button type="submit" class="btn btn-sm btn-primary w-100">Save Menu</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
