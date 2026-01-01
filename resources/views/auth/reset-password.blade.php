@extends('layouts.app')

@section('content')
<style>
    .auth-container { max-width: 480px; margin: 0 auto; padding-top: 2rem; }
    .auth-card { border: none; border-radius: 1rem; background: #fff; box-shadow: 0 10px 40px rgba(0,0,0,0.08); overflow: hidden; }
    .auth-header { background: linear-gradient(135deg, #2d3436 0%, #34495e 100%); color: white; padding: 2.5rem 2rem; text-align: center; }
    .auth-header h1 { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.5rem; color: white; }
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
    .btn-auth { background: linear-gradient(135deg, #2d3436 0%, #34495e 100%); color: white; border: none; padding: 0.875rem 1.5rem; border-radius: 0.5rem; font-weight: 600; font-size: 0.95rem; width: 100%; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-top: 1rem; }
    .btn-auth:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); background: linear-gradient(135deg, #1c1c1a 0%, #2d3436 100%); }
    .btn-auth:active { transform: translateY(0); }
    .password-hint { background-color: #f8f9fa; border-left: 3px solid #d4af37; padding: 0.75rem 1rem; border-radius: 0.25rem; font-size: 0.85rem; color: #706f6c; margin-bottom: 1.5rem; }
    .password-hint ul { margin: 0.5rem 0 0 0; padding-left: 1.25rem; }
    .password-hint li { margin: 0.25rem 0; }
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Reset Password</h1>
            <p>Create a new secure password</p>
        </div>
        <div class="auth-body">
            <div class="password-hint">
                <strong>Password Requirements:</strong>
                <ul>
                    <li>Minimum 8 characters</li>
                    <li>At least one uppercase letter</li>
                    <li>At least one number</li>
                    <li>At least one special character</li>
                </ul>
            </div>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="form-group">
                    <label class="form-label" for="email">
                        <i class="bi bi-envelope"></i> Email Address
                    </label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email', $email) }}"
                        class="form-control @error('email') is-invalid @enderror"
                        required
                        readonly
                        style="background-color: #f8f9fa;"
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">
                        <i class="bi bi-lock"></i> New Password
                    </label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Enter new password"
                        required
                        autofocus
                    >
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">
                        <i class="bi bi-lock-fill"></i> Confirm Password
                    </label>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        class="form-control"
                        placeholder="Re-enter new password"
                        required
                    >
                </div>

                <button type="submit" class="btn-auth">
                    <i class="bi bi-check-circle"></i> Reset Password
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
