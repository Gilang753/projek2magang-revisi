<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warung Makan - @yield('title')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @stack('styles')

    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-size: 1.75rem;
            font-weight: bold;
        }
        .navbar .dropdown-menu {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
        }
        .navbar .dropdown-item {
            padding: 0.75rem 1.5rem;
            transition: all 0.3s;
            font-weight: 500;
        }
        .navbar .dropdown-item:hover {
            background-color: #e9ecef;
            padding-left: 2rem;
            color: #212529;
        }
        .navbar .dropdown-item i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }
        .navbar-nav .nav-link {
            font-weight: 600;
            transition: color 0.3s ease;
        }
        .navbar-nav .nav-link:hover {
            color: #d8d8d8 !important;
        }
        .btn-outline-light {
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-outline-light:hover {
            background-color: #fff;
            color: #212529 !important;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">Rumah Makan</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                @if(session('admin_logged_in'))
                <ul class="navbar-nav"> 
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('menus.index') }}">Daftar Menu</a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="fuzzyDropdown" role="button" 
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-sliders-h me-1"></i> Setting Fuzzy
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="fuzzyDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('fuzzy.input') }}">
                                    <i class="fas fa-tag me-1"></i> Fuzzy Harga
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('fuzzy.inputRating') }}">
                                    <i class="fas fa-star me-1"></i> Fuzzy Rating
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('fuzzy.inputRasa') }}">
                                    <i class="fas fa-utensils me-1"></i> Fuzzy Rasa
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('rules.index') }}">
                            <i class="fas fa-gavel me-1"></i> Rules
                        </a>
                    </li>
                    
                </ul>
                @endif
                
                <ul class="navbar-nav ms-auto">
                    @if(session('admin_logged_in'))
                    <li class="nav-item">
                        <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                        </form>
                    </li>
                    @else
                    <li class="nav-item">
                        <a href="{{ route('admin.login') }}" class="btn btn-outline-light btn-sm">Login Admin</a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        @yield('content')
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('scripts')

</body>
</html>