@extends(backpack_view('layouts.plain'))

@section('content')
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
    <div class="row w-100">
        <div class="col-lg-6 d-none d-lg-flex flex-column align-items-center justify-content-center text-white p-5" 
             style="background: linear-gradient(135deg, #6f42c1, #d63384);">
            
            {{-- Branding / Illustration --}}
            <div class="text-center">
               
                <h1 class="fw-bold display-5">PlateMarket</h1>
               
            </div>
        </div>

        {{-- Login Form --}}
        <div class="col-lg-6 d-flex align-items-center justify-content-center p-5">
            <div class="card shadow-lg border-0 rounded-4 w-100" style="max-width: 450px;">
                <div class="card-body p-5">
                    
                    {{-- Title --}}
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-dark">Welcome Back 👋</h2>
                        <p class="text-muted">Sign in to your account</p>
                    </div>

                    {{-- Form --}}
                    <form method="POST" action="{{ route('admin.login.post') }}">
                        @csrf

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" name="email" id="email"
                                   class="form-control form-control-lg rounded-3 @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password"
                                   class="form-control form-control-lg rounded-3 @error('password') is-invalid @enderror"
                                   required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Remember Me --}}
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            <a href="#" class="text-decoration-none text-primary fw-semibold">Forgot password?</a>
                        </div>

                        {{-- Submit --}}
                        <button type="submit" 
                            class="btn btn-lg btn-primary w-100 rounded-3 shadow-sm">
                            Sign In
                        </button>
                    </form>

                    {{-- Divider --}}
                  

                    {{-- Footer --}}
                    <div class="text-center mt-4 small text-muted">
                        &copy; {{ date('Y') }} <strong>{{config('app.name')}}</strong>. All rights reserved.
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
