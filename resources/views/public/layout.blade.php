<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Garage ô tô')</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #f5f7fb;
            color: #243247;
        }

        .garage-navbar {
            background: #13243b;
        }

        .garage-hero {
            background: linear-gradient(120deg, #13243b, #24558c);
            color: white;
            border-radius: 20px;
        }

        .service-card {
            border: 0;
            border-radius: 16px;
            overflow: hidden;
            transition: transform 0.2s ease;
        }

        .service-card:hover {
            transform: translateY(-4px);
        }

        .service-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .service-placeholder {
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e8eff8;
            color: #24558c;
            font-size: 18px;
            font-weight: 600;
        }

        .service-price {
            color: #24558c;
            font-size: 22px;
            font-weight: 700;
        }

        .service-description {
            white-space: pre-line;
            overflow-wrap: anywhere;
        }
    </style>
</head>

<body>
<nav class="navbar navbar-dark garage-navbar">
    <div class="container">
        <a
            class="navbar-brand fw-bold"
            href="{{ route('public.dichvu.index') }}"
        >
            GARAGE Ô TÔ
        </a>

        <a
            class="btn btn-outline-light btn-sm"
            href="{{ route('public.dichvu.index') }}"
        >
            Dịch vụ
        </a>
    </div>
</nav>

<main class="container py-4">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

<footer class="border-top mt-4">
    <div class="container py-4 text-muted">
        <div class="fw-semibold">Garage ô tô</div>
        <div>Giờ hoạt động: 07:30–20:00 hằng ngày.</div>
    </div>
</footer>
</body>
</html>