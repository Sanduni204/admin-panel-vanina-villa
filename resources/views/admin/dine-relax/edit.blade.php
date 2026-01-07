@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Dine & Relax</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Hero Section -->
    <div class="card mb-5">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Hero Section</span>
            <a href="{{ route('dine-relax.hero.edit') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-pencil"></i> Edit Hero
            </a>
        </div>
        <div class="card-body">
            @if($page->hero_image_path)
                <div class="mb-4">
                    <img src="{{ asset('storage/' . $page->hero_image_path) }}" alt="{{ $page->hero_image_alt }}" class="img-fluid rounded shadow-sm" style="max-height: 300px; object-fit: cover; width: 100%;">
                </div>
            @else
                <div class="alert alert-info mb-4">
                    <i class="bi bi-info-circle"></i> No hero image uploaded yet.
                </div>
            @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="border rounded p-3 bg-light">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-translate text-primary me-2"></i>
                            <h6 class="mb-0 text-primary">English Content</h6>
                        </div>
                        @php
                            $titleEn = $page->translation('en')?->hero_title;
                            $taglineEn = $page->translation('en')?->hero_tagline;
                            $titleEnClean = $titleEn ? strip_tags(html_entity_decode($titleEn)) : null;
                            $taglineEnClean = $taglineEn ? strip_tags(html_entity_decode($taglineEn)) : null;
                        @endphp
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Title</small>
                            <p class="mb-0 fw-bold">{!! $titleEnClean ?: '<span class="text-muted">Not set</span>' !!}</p>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block mb-1">Tagline</small>
                            <p class="mb-0">{!! $taglineEnClean ?: '<span class="text-muted fst-italic">Not set</span>' !!}</p>
                        </div>
                        @if($page->translation('en')?->hero_lead)
                            <div>
                                <small class="text-muted d-block mb-1">Lead Paragraph</small>
                                <p class="mb-0 small text-truncate">{{ Str::limit($page->translation('en')->hero_lead, 100) }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-3 bg-light">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-translate text-success me-2"></i>
                            <h6 class="mb-0 text-success">French Content</h6>
                        </div>
                        @php
                            $titleFr = $page->translation('fr')?->hero_title;
                            $taglineFr = $page->translation('fr')?->hero_tagline;
                            $titleFrClean = $titleFr ? strip_tags(html_entity_decode($titleFr)) : null;
                            $taglineFrClean = $taglineFr ? strip_tags(html_entity_decode($taglineFr)) : null;
                        @endphp
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Title</small>
                            <p class="mb-0 fw-bold">{!! $titleFrClean ?: '<span class="text-muted">Non défini</span>' !!}</p>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block mb-1">Tagline</small>
                            <p class="mb-0">{!! $taglineFrClean ?: '<span class="text-muted fst-italic">Non défini</span>' !!}</p>
                        </div>
                        @if($page->translation('fr')?->hero_lead)
                            <div>
                                <small class="text-muted d-block mb-1">Lead Paragraph</small>
                                <p class="mb-0 small text-truncate">{{ Str::limit($page->translation('fr')->hero_lead, 100) }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Blocks Section -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Blocks</span>
            <a href="{{ route('dine-relax.blocks.create') }}" class="btn btn-sm btn-success">+ Add Block</a>
        </div>
        <div class="card-body">
            @if($blocks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Image</th>
                                <th>Name</th>
                                <th>Heading (EN)</th>
                                <th>Heading (FR)</th>
                                <th style="width: 180px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($blocks as $block)
                                <tr>
                                    <td>
                                        @if($block->image_path)
                                            <img src="{{ asset('storage/' . $block->image_path) }}" alt="{{ $block->image_alt }}" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $block->name ?? str_replace('-', ' ', $block->slug) }}</strong>
                                        <br><small class="text-muted">{{ $block->slug }}</small>
                                    </td>
                                    <td>{{ $block->translation('en')?->heading ?? '-' }}</td>
                                    <td>{{ $block->translation('fr')?->heading ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-2">
                                            <a href="{{ route('dine-relax.blocks.edit', ['block' => $block->id]) }}" class="btn btn-sm btn-primary">Edit</a>
                                            <form action="{{ route('dine-relax.blocks.delete', ['block' => $block->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </div>
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

    <!-- Menus Section (Common description + link) -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Menus</span>
        </div>
        <div class="card-body">
            <form action="{{ route('dine-relax.menus.info.update') }}" method="POST" class="mb-4">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Menus Description (EN)</label>
                        <textarea name="menus_description_en" class="form-control" rows="3" placeholder="Optional blurb shown above menu cards on public page.">{{ old('menus_description_en', $page->translation('en')?->menus_description) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Description des menus (FR)</label>
                        <textarea name="menus_description_fr" class="form-control" rows="3" placeholder="Texte optionnel au-dessus des cartes de menu.">{{ old('menus_description_fr', $page->translation('fr')?->menus_description) }}</textarea>
                    </div>
                </div>
                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Save Description</button>
                    <button type="submit" name="clear" value="1" class="btn btn-outline-danger" onclick="return confirm('Clear both EN and FR descriptions?');"><i class="bi bi-x-circle"></i> Clear</button>
                </div>
            </form>

            <a href="{{ route('dine-relax.menus.index') }}" class="btn btn-primary">Manage Menus</a>
        </div>
    </div>
</div>
@endsection
