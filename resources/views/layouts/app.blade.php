<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'BiteRoute') · BiteRoute</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --ink: #182b25; --green: #176b50; --mint: #e8f5ef; }
        body { background: #f5f7f5; color: var(--ink); }
        .navbar { background: #123c30; }
        .navbar-brand { font-weight: 800; letter-spacing: -.04em; }
        .nav-link { color: rgba(255,255,255,.78); }
        .nav-link:hover, .nav-link.active { color: #fff; }
        .page-heading { letter-spacing: -.04em; font-weight: 800; }
        .card { border: 1px solid #e5ebe7; border-radius: 1rem; box-shadow: 0 8px 24px rgba(24,43,37,.04); }
        .btn-primary { background: var(--green); border-color: var(--green); }
        .btn-primary:hover { background: #10523d; border-color: #10523d; }
        .table > :not(caption) > * > * { padding: .9rem .8rem; vertical-align: middle; }
        .badge-soft { background: var(--mint); color: var(--green); }
        footer { color: #718078; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">BiteRoute</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <div class="navbar-nav ms-auto">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">Customers</a>
                <a class="nav-link {{ request()->routeIs('restaurants.*') ? 'active' : '' }}" href="{{ route('restaurants.index') }}">Restaurants</a>
                <a class="nav-link {{ request()->routeIs('foods.*') ? 'active' : '' }}" href="{{ route('foods.index') }}">Food & Items</a>
                <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">Orders</a>
                <a class="nav-link {{ request()->routeIs('deliveries.*') ? 'active' : '' }}" href="{{ route('deliveries.index') }}">Deliveries</a>
            </div>
        </div>
    </div>
</nav>

<main class="container py-4 py-lg-5">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>Please correct the following:</strong>
            <ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    @yield('content')
</main>
<footer class="container pb-4 small">BiteRoute · Online food delivery management</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) event.preventDefault();
        });
    });
</script>
</body>
</html>
