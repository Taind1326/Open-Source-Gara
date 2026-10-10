<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>
            Sửa chữa xe
        </title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <main class="container py-4">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <h2>
                Sửa chữa xe {{ $yeuCau->xe->BienSo }}
            </h2>
            <p class="text-muted">
                Yêu cầu #{{ $yeuCau->MaYC }}
            </p>
            <a class="btn btn-outline-secondary mb-3" href="{{ route('baogia.show', $yeuCau->MaYC) }}">Xem báo giá
            </a>
            <section class="card p-4">
                <h4>
                    Bắt đầu sửa chữa
                </h4>
                <p>
                    Báo giá đã được khách đồng ý. Kỹ thuật viên đã kiểm tra tiếp tục phụ trách xe.
                </p>
                <p>
                    Tổng báo giá:
                    <strong>
                        {{ number_format($yeuCau->baoGia->TongTien, 0, ',', '.') }}đ
                    </strong>
                </p>
                <form method="POST" action="{{ route('suachua.store', $yeuCau->MaYC) }}">
                    @csrf
                    <button class="btn btn-primary">
                        Bắt đầu sửa chữa
                    </button>
                </form>
            </section>
        </main>
    </body>
</html>
