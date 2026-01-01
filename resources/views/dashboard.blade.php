@extends('layouts.app')

@section('content')
@if (auth()->user()->role === 'admin')
    @include('admin')
@else
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-3">
            <div class="card-body d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
                <div>
                    <h1 class="h4 mb-1">Hello, {{ auth()->user()->name }}</h1>
                    <p class="mb-0 text-muted">You are signed in as <span class="fw-semibold">{{ auth()->user()->role }}</span>.</p>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h5 mb-3">Next steps</h2>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">Review your profile and settings.</li>
                    <li class="mb-2">Access role-specific pages using the navigation.</li>
                    <li class="mb-2">Invite another user to test permissions.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
