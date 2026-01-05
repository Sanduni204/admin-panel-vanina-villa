@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Dine & Relax</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Hero Section -->
    <form action="{{ route('dine-relax.update') }}" method="POST" enctype="multipart/form-data" class="mb-5">
        @csrf
        <div class="card mb-4">
            <div class="card-header">Hero Section</div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Hero Image</label>
                        <input type="file" name="hero_image" class="form-control @error('hero_image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                        @error('hero_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Hero Image Alt</label>
                        <input type="text" name="hero_image_alt" class="form-control @error('hero_image_alt') is-invalid @enderror" value="{{ old('hero_image_alt', $page->hero_image_alt) }}">
                        @error('hero_image_alt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <x-bilingual-editor field="hero_tagline" label="Hero Tagline" :value_en="$page->translation('en')?->hero_tagline" :value_fr="$page->translation('fr')?->hero_tagline" />
                <x-bilingual-editor field="hero_title" label="Hero Title" :value_en="$page->translation('en')?->hero_title" :value_fr="$page->translation('fr')?->hero_title" required="true" />
                <x-bilingual-editor field="hero_lead" label="Hero Lead" :value_en="$page->translation('en')?->hero_lead" :value_fr="$page->translation('fr')?->hero_lead" type="textarea" />
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Save Hero</button>
            </div>
        </div>
    </form>

    <!-- Blocks Section -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Blocks</span>
            <a href="{{ route('dine-relax.blocks.create') }}" class="btn btn-sm btn-success">+ Add Block</a>
        </div>
        <div class="card-body">
            @if($blocks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Heading (EN)</th>
                                <th>Heading (FR)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($blocks as $block)
                                <tr>
                                    <td>
                                        <strong>{{ $block->name ?? str_replace('-', ' ', $block->slug) }}</strong>
                                        <br><small class="text-muted">{{ $block->slug }}</small>
                                    </td>
                                    <td>{{ $block->translation('en')?->heading ?? '-' }}</td>
                                    <td>{{ $block->translation('fr')?->heading ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('dine-relax.blocks.edit', ['block' => $block->id]) }}" class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ route('dine-relax.blocks.delete', ['block' => $block->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">No blocks yet. <a href="{{ route('dine-relax.blocks.create') }}">Create one</a></p>
            @endif
        </div>
    </div>

    <!-- Menus Section (Link to separate page) -->
    <div class="card">
        <div class="card-header">Menus</div>
        <div class="card-body">
            <a href="{{ route('dine-relax.menus.index') }}" class="btn btn-primary">Manage Menus</a>
        </div>
    </div>
</div>
@endsection
