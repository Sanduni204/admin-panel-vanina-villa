@extends('layouts.app')

@section('content')
<style>
    .auth-container { max-width: 580px; margin: 0 auto; padding-top: 2rem; }
    .auth-card { border: none; border-radius: 1rem; background: #fff; box-shadow: 0 10px 40px rgba(0,0,0,0.08); overflow: hidden; }
    .auth-header { background: linear-gradient(135deg, #2d3436 0%, #34495e 100%); color: white; padding: 2.5rem 2rem; text-align: center; }
    .auth-header h1 { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.5rem; color: white; }
    .auth-header p { color: rgba(255,255,255,0.9); font-size: 0.95rem; margin: 0; }
    .auth-body { padding: 2.5rem 2rem; }
    .form-group { margin-bottom: 1.5rem; }
    .form-label { font-weight: 600; color: #2d3436; margin-bottom: 0.5rem; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; }
    .form-label i { color: #d4af37; font-size: 1rem; }
    .form-control, .form-select { border-radius: 0.5rem; border: 2px solid #e3e3e0; padding: 0.75rem 1rem; font-size: 0.95rem; transition: all 0.3s ease; }
    .form-control:focus, .form-select:focus { border-color: #d4af37; box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1); background-color: white; }
    .form-control.is-invalid, .form-select.is-invalid { border-color: #f53003; }
    .form-control.is-invalid:focus, .form-select.is-invalid:focus { box-shadow: 0 0 0 3px rgba(245, 48, 3, 0.1); }
    .invalid-feedback { color: #f53003; font-size: 0.85rem; margin-top: 0.5rem; display: block; }
    .btn-auth { background: linear-gradient(135deg, #2d3436 0%, #34495e 100%); color: white; border: none; padding: 0.875rem 1.5rem; border-radius: 0.5rem; font-weight: 600; font-size: 0.95rem; width: 100%; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-top: 1.5rem; }
    .btn-auth:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); background: linear-gradient(135deg, #1c1c1a 0%, #2d3436 100%); }
    .btn-auth:active { transform: translateY(0); }
    .auth-footer { text-align: center; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e3e3e0; }
    .auth-footer p { color: #706f6c; font-size: 0.9rem; margin: 0; }
    .auth-footer a { color: #d4af37; text-decoration: none; font-weight: 600; transition: color 0.3s ease; }
    .auth-footer a:hover { color: #e6c200; text-decoration: underline; }
    .role-badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 0.25rem; font-size: 0.8rem; font-weight: 600; margin-left: 0.5rem; }
    .role-badge.admin { background-color: #d4af37; color: #2d3436; }
    .role-badge.user { background-color: #e3e3e0; color: #2d3436; }
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Create Account</h1>

        </div>
        <div class="auth-body">
            <form method="POST" action="{{ url('/register') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="name">
                        <i class="bi bi-person"></i> Full Name
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name') }}"
                        class="form-control @error('name') is-invalid @enderror"
                        placeholder="Enter your full name"
                        required
                        autofocus
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">
                        <i class="bi bi-envelope"></i> Email Address
                    </label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="your.email@example.com"
                        required
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="role">
                        <i class="bi bi-shield-check"></i> Account Role
                    </label>
                    <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                        <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="password">
                                <i class="bi bi-lock"></i> Password
                            </label>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Minimum 8 characters"
                                required
                            >
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="password_confirmation">
                                <i class="bi bi-lock-fill"></i> Confirm Password
                            </label>
                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                class="form-control"
                                placeholder="Re-enter password"
                                required
                            >
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-auth">
                    <i class="bi bi-check-circle"></i> Create Account
                </button>

                <div class="auth-footer">
                    <p>Already have an account? <a href="{{ route('login') }}">Sign in here</a></p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
