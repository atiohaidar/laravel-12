<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Laravel 12 API')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Fonts -->
<link rel="dns-prefetch" href="//fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Bootstrap CSS -->
    @vite(['resources/css/bootstrap-app.css', 'resources/js/app.js'])
    
    <!-- Custom styles -->
    <style>
        :root {
            --primary-color: #5c6ac4;
            --secondary-color: #f8fafc;
            --accent-color: #4f46e5;
            --text-color: #1e293b;
            --light-text: #64748b;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--text-color);
            background-color: #f9fafb;
            line-height: 1.6;
        }
        
        .navbar {
            background-color: white;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.2s ease;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
            padding: 1.25rem 1.5rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .footer {
            margin-top: 2rem;
            padding: 1.5rem 0;
            background-color: white;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .api-doc-box {
            background-color: #f8fafc;
            border-left: 4px solid var(--primary-color);
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .sidebar {
            background-color: white;
            height: 100%;
            border-right: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .sidebar .nav-link {
            color: var(--light-text);
            padding: 0.75rem 1.25rem;
            border-radius: 0.375rem;
            margin-bottom: 0.25rem;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: var(--primary-color);
            background-color: rgba(92, 106, 196, 0.1);
        }
        
        .dashboard-stat {
            padding: 1.5rem;
            border-radius: 10px;
            background-color: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }
        
        .dashboard-stat:hover {
            transform: translateY(-3px);
        }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(92, 106, 196, 0.1);
            color: var(--primary-color);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-light">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <span class="me-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2zM20 6h2v14a2 2 0 0 1-2 2h-8a2 2 0 0 1-2-2v-2"></path></svg>
                </span>
                Laravel 12 API
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <!-- Left Nav -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ url('/dashboard') }}">Dashboard</a>
                    </li>
                    @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('wallet*') ? 'active' : '' }}" href="#" id="walletDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            Wallet
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="walletDropdown">
                            <li><a class="dropdown-item" href="{{ route('wallet.index') }}">My Wallet</a></li>
                            <li><a class="dropdown-item" href="{{ route('wallet.topup.form') }}">Top Up</a></li>
                            <li><a class="dropdown-item" href="{{ route('wallet.transfer.form') }}">Transfer</a></li>
                            <li><a class="dropdown-item" href="{{ route('wallet.transactions') }}">Transaction History</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('products*') ? 'active' : '' }}" href="#" id="inventoryDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            Inventory
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="inventoryDropdown">
                            <li><a class="dropdown-item" href="{{ route('products.index') }}">My Products</a></li>
                            <li><a class="dropdown-item" href="{{ route('products.create') }}">Add New Product</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('marketplace*') ? 'active' : '' }}" href="#" id="marketplaceDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            Marketplace
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="marketplaceDropdown">
                            <li><a class="dropdown-item" href="{{ route('marketplace.index') }}">Browse Products</a></li>
                            <li><a class="dropdown-item" href="{{ route('marketplace.purchased') }}">My Purchases</a></li>
                            <li><a class="dropdown-item" href="{{ route('marketplace.sold') }}">My Sales</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('chat') ? 'active' : '' }}" href="{{ url('/chat') }}">
                            Chat
                            @if(auth()->user()->unreadMessages()->count() > 0)
                                <span class="badge rounded-pill bg-danger">{{ auth()->user()->unreadMessages()->count() }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('events*') || request()->is('event-categories*') ? 'active' : '' }}" href="#" id="eventsDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            Events
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="eventsDropdown">
                            <li><a class="dropdown-item" href="{{ route('events.index') }}">Browse Events</a></li>
                            <li><a class="dropdown-item" href="{{ route('events.create') }}">Create Event</a></li>
                            <li><a class="dropdown-item" href="{{ route('events.organized') }}">My Events</a></li>
                            <li><a class="dropdown-item" href="{{ route('events.registrations.my') }}">My Registrations</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('event-categories.index') }}">Event Categories</a></li>
                        </ul>
                    </li>
                    @endauth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('api-docs') ? 'active' : '' }}" href="{{ url('/api-docs') }}">API Docs</a>
                    </li>
                </ul>
                
                <!-- Right Nav -->
                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('login') ? 'active' : '' }}" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('register') ? 'active' : '' }}" href="{{ route('register') }}">Register</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="{{ url('/users/profile') }}">Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            @yield('content')
        </div>
    </main>
    
    <footer class="footer text-center">
        <div class="container">
            <span class="text-muted">Laravel 12 API &copy; {{ date('Y') }}</span>
        </div>
    </footer>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
