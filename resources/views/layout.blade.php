<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>License Plate Market</title>

    {{-- Bootstrap CSS CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- SweetAlert2 (Optional, used for alerts) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                🏷️ PlateMarket
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav ms-auto">
                       <li class="nav-item"><a class="nav-link" href="{{ url('plates') }}"> Plates</a></li>
                    @auth
                     
                        <li class="nav-item"><a class="nav-link" href="{{ url('plates') }}">My Sold Plates</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ url('/profile') }}">Profile</a></li>
                        <li class="nav-item">
                            <form action="{{ url('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button class="nav-link btn btn-link text-white" type="submit">Logout</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ url('login') }}">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ url('register') }}">Register</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="py-4">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="text-center py-3 bg-light border-top">
        <div class="container">
            &copy; {{ date('Y') }} PlateMarket. All rights reserved.
        </div>
    </footer>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Custom JS --}}
    @stack('scripts')

</body>
</html>
