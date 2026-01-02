<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Vanina Villa') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #2d3436;
            --accent-color: #d4af37;
            --light-bg: #f8f9fa;
        }
        body { background-color: var(--light-bg); }
        .navbar { background: linear-gradient(135deg, var(--primary-color) 0%, #34495e 100%); padding: 1rem 0; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .navbar-brand { font-size: 1.5rem; font-weight: 700; color: var(--accent-color) !important; letter-spacing: 0.5px; }
        .nav-link { color: rgba(255,255,255,0.85) !important; transition: all 0.3s ease; padding: 0.5rem 1rem !important; }
        .nav-link:hover { color: var(--accent-color) !important; }
        .navbar-toggler { border-color: rgba(255,255,255,0.5); }
        .navbar-toggler:focus { box-shadow: none; border-color: var(--accent-color); }
        .navbar-toggler-icon { background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255,255,255,0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e"); }
        .dropdown-menu { border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 0.5rem; }
        .dropdown-header { color: var(--primary-color); font-weight: 600; }
        .dropdown-item:hover { background-color: var(--light-bg); color: var(--accent-color); }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="/">
            Vanina Villa
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            Dashboard
                        </a>
                    </li>
                    @if (auth()->user()->role === 'admin')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Admin Panel
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><h6 class="dropdown-header"><i class="bi bi-pencil-square"></i> Content Management</h6></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-house-door"></i> Villas</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-cup-hot"></i> Restaurant</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-pin-map"></i> Things to Do</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-search"></i> SEO Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-images"></i> Media Library</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-translate"></i> Translations</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-file-earmark-text"></i> Reports</a></li>
                            </ul>
                        </li>
                    @endif
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i> Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right"></i> Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}"><i class="bi bi-person-plus"></i> Register</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<div class="container py-4">
    @if (session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
