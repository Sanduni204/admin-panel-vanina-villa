@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1>Villas Management</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('villas.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add New Villa
            </a>
        </div>
    </div>

    @if($message = session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search & Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search villa title..."
                        value="{{ request('search') }}"
                    >
                </div>
                <div class="col-md-2">
                    <input
                        type="number"
                        name="price_min"
                        class="form-control"
                        placeholder="Min price"
                        value="{{ request('price_min') }}"
                    >
                </div>
                <div class="col-md-2">
                    <input
                        type="number"
                        name="price_max"
                        class="form-control"
                        placeholder="Max price"
                        value="{{ request('price_max') }}"
                    >
                </div>
                <div class="col-md-2">
                    <select name="sort" class="form-select">
                        <option value="display_order" @selected(request('sort') === 'display_order')>Display Order</option>
                        <option value="created_at" @selected(request('sort') === 'created_at')>Date Created</option>
                        <option value="price" @selected(request('sort') === 'price')>Price</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Villas Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">Order</th>
                        <th width="80">Image</th>
                        <th>Title</th>
                        <th width="100">Price</th>
                        <th width="80">Beds</th>
                        <th width="80">Guests</th>
                        <th width="80">Status</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($villas as $villa)
                        @php
                            $translation = $villa->translations->where('locale', 'en')->first();
                            $featuredMedia = $villa->media->where('is_featured', true)->first();
                        @endphp
                        <tr>
                            <td>
                                <span class="badge bg-info">{{ $villa->display_order }}</span>
                            </td>
                            <td>
                                @if($featuredMedia)
                                    <img
                                        src="{{ asset('storage/' . $featuredMedia->image_path) }}"
                                        alt="{{ $translation->title ?? 'Villa' }}"
                                        class="img-thumbnail"
                                        width="80"
                                        height="60"
                                        style="object-fit: cover;"
                                    >
                                @else
                                    <div class="bg-light text-muted text-center p-2" style="width: 80px; height: 60px;">
                                        No image
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $translation->title ?? 'No Title' }}</strong>
                            </td>
                            <td>${{ $translation ? number_format($translation->price ?? 0, 2) : '0.00' }}</td>
                            <td>{{ $translation->bedrooms ?? '-' }}</td>
                            <td>{{ $translation->max_guests ?? '-' }}</td>
                            <td>
                                @if($villa->published_at)
                                    <span class="badge bg-success">Published</span>
                                @else
                                    <span class="badge bg-warning">Draft</span>
                                @endif
                                @if($villa->featured)
                                    <span class="badge bg-danger">Featured</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('villas.edit', $villa) }}" class="btn btn-sm btn-info" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('villas.destroy', $villa) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure?')"
                                        title="Delete"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No villas found. <a href="{{ route('villas.create') }}">Create one</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $villas->links() }}
    </div>
</div>
@endsection
