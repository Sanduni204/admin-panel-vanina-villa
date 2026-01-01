@extends('layouts.app')

@section('content')
<style>
    .dashboard-header { background: linear-gradient(135deg, #2d3436 0%, #34495e 100%); color: white; padding: 3rem 2rem; border-radius: 0.75rem; margin-bottom: 2rem; }
    .dashboard-header h1 { font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem; }
    .module-card { transition: all 0.3s ease; border: none; border-radius: 0.75rem; overflow: hidden; }
    .module-card:hover { transform: translateY(-8px); box-shadow: 0 12px 24px rgba(0,0,0,0.15); }
    .module-icon { width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 0.5rem; }
    .quick-action { transition: all 0.3s ease; cursor: pointer; border-left: 4px solid transparent; }
    .quick-action:hover { border-left-color: #d4af37; background-color: #f0f0f0; }
    .status-badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 2rem; font-size: 0.85rem; font-weight: 600; }
    .bg-primary-light { background-color: rgba(13, 110, 253, 0.1); }
    .bg-success-light { background-color: rgba(25, 135, 84, 0.1); }
    .bg-info-light { background-color: rgba(13, 202, 240, 0.1); }
    .bg-warning-light { background-color: rgba(255, 193, 7, 0.1); }
    .card-title { color: #2d3436; font-weight: 600; }
</style>

<div class="dashboard-header">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="mb-1"> Admin Dashboard</h1>
        </div>
        <div class="col-md-4 text-end">
            <div class="text-white">
                <p class="mb-0 small opacity-75">Welcome,</p>
                <p class="mb-0 h5"><i class="bi bi-person-check"></i> {{ auth()->user()->name }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card module-card shadow-sm h-100 bg-white">
            <div class="card-body">
                <div class="module-icon bg-primary-light mb-3">
                    <i class="bi bi-house-door" style="font-size: 1.75rem; color: #0d6efd;"></i>
                </div>
                <h5 class="card-title mb-2">Villas</h5>
                <p class="text-muted small mb-3">Manage luxury properties, pricing & details</p>
                <a href="#" class="btn btn-sm btn-primary w-100"><i class="bi bi-arrow-right"></i> Manage</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card module-card shadow-sm h-100 bg-white">
            <div class="card-body">
                <div class="module-icon bg-success-light mb-3">
                    <i class="bi bi-cup-hot" style="font-size: 1.75rem; color: #198754;"></i>
                </div>
                <h5 class="card-title mb-2">Restaurant</h5>
                <p class="text-muted small mb-3">Daily specials & menu management</p>
                <a href="#" class="btn btn-sm btn-success w-100"><i class="bi bi-arrow-right"></i> Manage</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card module-card shadow-sm h-100 bg-white">
            <div class="card-body">
                <div class="module-icon bg-info-light mb-3">
                    <i class="bi bi-pin-map" style="font-size: 1.75rem; color: #0dcaf0;"></i>
                </div>
                <h5 class="card-title mb-2">Activities</h5>
                <p class="text-muted small mb-3">Local activities & excursions</p>
                <a href="#" class="btn btn-sm btn-info w-100"><i class="bi bi-arrow-right"></i> Manage</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card module-card shadow-sm h-100 bg-white">
            <div class="card-body">
                <div class="module-icon bg-warning-light mb-3">
                    <i class="bi bi-search" style="font-size: 1.75rem; color: #ffc107;"></i>
                </div>
                <h5 class="card-title mb-2">SEO Settings</h5>
                <p class="text-muted small mb-3">Meta titles & descriptions</p>
                <a href="#" class="btn btn-sm btn-warning w-100"><i class="bi bi-arrow-right"></i> Manage</a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-bottom">
                <h5 class="card-title mb-0"><i class="bi bi-lightning-charge"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item quick-action d-flex align-items-start p-3">
                        <div class="me-3">
                            <div class="module-icon bg-primary-light" style="width: 45px; height: 45px;">
                                <i class="bi bi-plus-circle" style="font-size: 1.25rem; color: #0d6efd;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Add New Villa</h6>
                            <p class="text-muted small mb-0">Create bilingual property listing with pricing</p>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </div>
                    <div class="list-group-item quick-action d-flex align-items-start p-3">
                        <div class="me-3">
                            <div class="module-icon bg-success-light" style="width: 45px; height: 45px;">
                                <i class="bi bi-pencil-square" style="font-size: 1.25rem; color: #198754;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Update Menu</h6>
                            <p class="text-muted small mb-0">Edit restaurant daily specials & pricing</p>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </div>
                    <div class="list-group-item quick-action d-flex align-items-start p-3">
                        <div class="me-3">
                            <div class="module-icon bg-info-light" style="width: 45px; height: 45px;">
                                <i class="bi bi-image" style="font-size: 1.25rem; color: #0dcaf0;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Media Library</h6>
                            <p class="text-muted small mb-0">Upload, optimize & manage images</p>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </div>
                    <div class="list-group-item quick-action d-flex align-items-start p-3">
                        <div class="me-3">
                            <div class="module-icon bg-warning-light" style="width: 45px; height: 45px;">
                                <i class="bi bi-translate" style="font-size: 1.25rem; color: #ffc107;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Language Toggle</h6>
                            <p class="text-muted small mb-0">Switch content between EN & FR</p>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3" style="border-left: 4px solid #0d6efd;">
            <div class="card-header bg-light border-bottom">
                <h5 class="card-title mb-0"><i class="bi bi-activity"></i> System Status</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <span class="text-muted small"><i class="bi bi-database"></i> Database</span>
                    <span class="status-badge bg-success text-white">✓ Active</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <span class="text-muted small"><i class="bi bi-globe"></i> Translations</span>
                    <span class="status-badge bg-info text-white">EN • FR</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small"><i class="bi bi-clock"></i> Last Backup</span>
                    <span class="text-muted small">Today</span>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-left: 4px solid #ffc107;">
            <div class="card-header bg-light border-bottom">
                <h5 class="card-title mb-0"><i class="bi bi-shield-lock"></i> Security</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled small">
                    <li class="mb-2"><i class="bi bi-check-circle text-success"></i> <span class="text-muted">Admin restricted to @vaninavilla.com</span></li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success"></i> <span class="text-muted">XSS input sanitization</span></li>
                    <li><i class="bi bi-check-circle text-success"></i> <span class="text-muted">CSRF protection enabled</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
