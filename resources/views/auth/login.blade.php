@extends('layouts.app')

@section('content')
<style>
    .auth-container { max-width: 480px; margin: 0 auto; padding-top: 2rem; }
    .auth-card { border: none; border-radius: 1rem; background: #fff; box-shadow: 0 10px 40px rgba(0,0,0,0.08); overflow: hidden; }
    .auth-header { background: linear-gradient(135deg, #2d3436 0%, #34495e 100%); color: white; padding: 2.5rem 2rem; text-align: center; }
    .auth-header h1 { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.25rem; color: white; }
    .auth-header h2 { font-size: 1.25rem; font-weight: 500; color: rgba(255,255,255,0.95); margin-bottom: 0.5rem; }
    .auth-header p { color: rgba(255,255,255,0.9); font-size: 0.95rem; margin: 0; }
    .auth-body { padding: 2.5rem 2rem; }
    .form-group { margin-bottom: 1.5rem; }
    .form-label { font-weight: 600; color: #2d3436; margin-bottom: 0.5rem; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; }
    .form-label i { color: #d4af37; font-size: 1rem; }
    .form-control { border-radius: 0.5rem; border: 2px solid #e3e3e0; padding: 0.75rem 1rem; font-size: 0.95rem; transition: all 0.3s ease; }
    .form-control:focus { border-color: #d4af37; box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1); background-color: white; }
    .form-control.is-invalid { border-color: #f53003; }
    .form-control.is-invalid:focus { box-shadow: 0 0 0 3px rgba(245, 48, 3, 0.1); }
    .invalid-feedback { color: #f53003; font-size: 0.85rem; margin-top: 0.5rem; display: block; }
    .form-check { margin-bottom: 1.5rem; padding-left: 0; }
    .form-check-input { width: 1.1rem; height: 1.1rem; border-radius: 0.25rem; border: 2px solid #e3e3e0; cursor: pointer; transition: all 0.3s ease; margin-right: 0.5rem; }
    .form-check-input:checked { background-color: #d4af37; border-color: #d4af37; }
    .form-check-label { color: #706f6c; font-size: 0.9rem; cursor: pointer; }
    .btn-auth { background: linear-gradient(135deg, #2d3436 0%, #34495e 100%); color: white; border: none; padding: 0.875rem 1.5rem; border-radius: 0.5rem; font-weight: 600; font-size: 0.95rem; width: 100%; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
    .btn-auth:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); background: linear-gradient(135deg, #1c1c1a 0%, #2d3436 100%); }
    .btn-auth:active { transform: translateY(0); }
    .auth-footer { text-align: center; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e3e3e0; }
    .auth-footer p { color: #706f6c; font-size: 0.9rem; margin: 0; }
    .auth-footer a { color: #d4af37; text-decoration: none; font-weight: 600; transition: color 0.3s ease; }
    .auth-footer a:hover { color: #e6c200; text-decoration: underline; }
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Welcome Back</h1>
        </div>
        <div class="auth-body">
            <form method="POST" action="{{ url('/login') }}">
                @csrf

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
                        placeholder="admin@vaninavilla.com"
                        required
                        autofocus
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">
                        <i class="bi bi-lock"></i> Password
                    </label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Enter your password"
                        required
                    >
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-auth">
                    <i class="bi bi-box-arrow-in-right"></i> Sign In
                </button>

                <div class="auth-footer">
                    <p><a href="{{ route('password.request') }}">Forgot your password?</a></p>
                    <p style="margin-top: 0.5rem;">Don't have an account? <a href="{{ route('register') }}">Create one here</a></p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
