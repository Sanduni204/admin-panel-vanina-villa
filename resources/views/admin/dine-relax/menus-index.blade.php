@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Dine & Relax - Menu Categories</h1>
        <a href="{{ route('dine-relax.edit') }}" class="btn btn-secondary">Back to Dine & Relax</a>
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

    <div class="row">
        <div class="col-12">
            <div class="card h-100">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Menu Categories</h5>
                    <a href="{{ route('dine-relax.menus.create') }}" class="btn btn-sm btn-light text-primary">
                        <i class="fas fa-plus"></i> Add New Category
                    </a>
                </div>
                <div class="card-body" style="max-height: 640px; overflow-y: auto;">
                    @if($menus->isEmpty())
                        <p class="text-muted mb-0">No menu categories yet.</p>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($menus as $menu)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong class="text-capitalize">{{ $menu->type }}</strong><br>
                                        <small class="text-muted">{{ $menu->translation()?->title ?? 'No title' }}</small>
                                    </div>
                                    <div class="d-flex gap-2 align-items-center">
                                        <span class="badge {{ $menu->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $menu->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                        <a href="{{ route('dine-relax.menus.edit', $menu->type) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('dine-relax.menus.delete', $menu->type) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this menu category?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 0.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
</style>
@endsection
