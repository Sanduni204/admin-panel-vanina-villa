@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0"><i class="bi bi-house-door"></i> Individual Villa Page</h2>
            <p class="text-muted small mt-2">Villa: <strong>{{ $villa->name ?? $villa->slug }}</strong></p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Tabs for Language Toggle -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="en-tab" data-bs-toggle="tab" data-bs-target="#en-content" type="button" role="tab">
                <i class="bi bi-globe"></i> English (EN)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="fr-tab" data-bs-toggle="tab" data-bs-target="#fr-content" type="button" role="tab">
                <i class="bi bi-globe"></i> Français (FR)
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- English Tab -->
        <div class="tab-pane fade show active" id="en-content" role="tabpanel">
            @include('admin.villa-pages.partials.individual-form', ['villa' => $villa, 'page' => $villaEn, 'locale' => 'en'])
        </div>

        <!-- French Tab -->
        <div class="tab-pane fade" id="fr-content" role="tabpanel">
            @include('admin.villa-pages.partials.individual-form', ['villa' => $villa, 'page' => $villaFr, 'locale' => 'fr'])
        </div>
    </div>

    <!-- Rates Table Section (Shared across languages) -->
    <div class="card border-0 shadow-sm mt-5">
        <div class="card-header bg-light border-bottom">
            <h5 class="card-title mb-0"><i class="bi bi-table"></i> Section 4: The Room & Rates</h5>
        </div>
        <div class="card-body">
            @include('admin.villa-pages.partials.rates-table', ['villa' => $villa, 'rates' => $rates])
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
@endsection
