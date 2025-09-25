<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' - ' : '' }}{{ config('app.name', 'ParoCompta') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-light">
    <!-- Header -->
    <header class="parocompta-header">
        <div class="container-fluid">
            <div class="row align-items-center py-3">
                <div class="col-md-6">
                    <h1 class="text-white mb-0 fw-bold">
                        <i class="fas fa-church me-2"></i>
                        ParoCompta
                    </h1>
                    <p class="text-white-50 mb-0 small">Gestion comptable des paroisses</p>
                </div>
                <div class="col-md-6 text-end">
                    @auth
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>
                                {{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user-edit me-2"></i>Profil
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    @auth
    <nav class="parocompta-nav">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <ul class="nav nav-pills py-2">
                        <li class="nav-item">
                            <a class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                               href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link-custom {{ request()->routeIs('entries.*') ? 'active' : '' }}"
                               href="{{ route('entries.index') }}">
                                <i class="fas fa-file-alt me-2"></i>Fiches comptables
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link-custom {{ request()->routeIs('parishes.*') ? 'active' : '' }}"
                               href="{{ route('parishes.index') }}">
                                <i class="fas fa-church me-2"></i>Paroisses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link-custom {{ request()->routeIs('payments.*') ? 'active' : '' }}"
                               href="{{ route('payments.index') }}">
                                <i class="fas fa-euro-sign me-2"></i>Suivi des paiements
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link-custom {{ request()->routeIs('stats.*') ? 'active' : '' }}"
                               href="{{ route('stats.index') }}">
                                <i class="fas fa-chart-line me-2"></i>Statistiques
                            </a>
                        </li>
                        @if(auth()->user()->role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link-custom {{ request()->routeIs('admin.*') ? 'active' : '' }}"
                               href="{{ route('admin.storage-test') }}">
                                <i class="fas fa-tools me-2"></i>Outils Admin
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <!-- Page Content -->
    <main class="container-fluid py-4">
        <!-- Breadcrumb -->
        @if(isset($breadcrumb) && count($breadcrumb) > 0)
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">
                        <i class="fas fa-home"></i>
                    </a>
                </li>
                @foreach($breadcrumb as $item)
                    @if($loop->last)
                        <li class="breadcrumb-item active">{{ $item['title'] }}</li>
                    @else
                        <li class="breadcrumb-item">
                            <a href="{{ $item['url'] }}">{{ $item['title'] }}</a>
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>
        @endif

        <!-- Page Header -->
        @isset($header)
        <div class="row mb-4">
            <div class="col-12">
                <div class="parocompta-card p-4">
                    {{ $header }}
                </div>
            </div>
        </div>
        @endisset

        <!-- Flash Messages -->
        @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Main Content -->
        <div class="fade-in">
            {{ $slot }}
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-top mt-5">
        <div class="container-fluid py-3">
            <div class="row">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        <i class="fas fa-copyright me-1"></i>
                        {{ date('Y') }} ParoCompta - Gestion comptable des paroisses
                    </p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="text-muted mb-0">
                        Version 1.0.0
                    </p>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
