@if($paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();

        $start = max(1, min($current - 1, $last - 2));
        $end = min($last, $start + 2);
    @endphp

    <nav aria-label="Phân trang" class="d-flex justify-content-end">
        <ul class="pagination flex-wrap gap-1 mb-0">
            {{-- Trang đầu --}}
            <li class="page-item {{ $current === 1 ? 'disabled' : '' }}">
                @if($current === 1)
                    <span class="page-link" aria-disabled="true">
                        <span aria-hidden="true">«</span>
                        <span class="visually-hidden">Trang đầu</span>
                    </span>
                @else
                    <a class="page-link" href="{{ $paginator->url(1) }}" aria-label="Trang đầu">
                        <span aria-hidden="true">«</span>
                    </a>
                @endif
            </li>

            {{-- Trang trước --}}
            <li class="page-item {{ $current === 1 ? 'disabled' : '' }}">
                @if($current === 1)
                    <span class="page-link" aria-disabled="true">
                        <span aria-hidden="true">‹</span>
                        <span class="visually-hidden">Trang trước</span>
                    </span>
                @else
                    <a
                        class="page-link"
                        href="{{ $paginator->previousPageUrl() }}"
                        rel="prev"
                        aria-label="Trang trước"
                    >
                        <span aria-hidden="true">‹</span>
                    </a>
                @endif
            </li>

            {{-- Tối đa 3 số trang --}}
            @for($page = $start; $page <= $end; $page++)
                <li class="page-item {{ $page === $current ? 'active' : '' }}">
                    @if($page === $current)
                        <span class="page-link" aria-current="page">
                            <span class="visually-hidden">Trang </span>{{ $page }}
                        </span>
                    @else
                        <a
                            class="page-link"
                            href="{{ $paginator->url($page) }}"
                            aria-label="Trang {{ $page }}"
                        >
                            {{ $page }}
                        </a>
                    @endif
                </li>
            @endfor

            {{-- Trang sau --}}
            <li class="page-item {{ $current === $last ? 'disabled' : '' }}">
                @if($current === $last)
                    <span class="page-link" aria-disabled="true">
                        <span aria-hidden="true">›</span>
                        <span class="visually-hidden">Trang sau</span>
                    </span>
                @else
                    <a
                        class="page-link"
                        href="{{ $paginator->nextPageUrl() }}"
                        rel="next"
                        aria-label="Trang sau"
                    >
                        <span aria-hidden="true">›</span>
                    </a>
                @endif
            </li>

            {{-- Trang cuối --}}
            <li class="page-item {{ $current === $last ? 'disabled' : '' }}">
                @if($current === $last)
                    <span class="page-link" aria-disabled="true">
                        <span aria-hidden="true">»</span>
                        <span class="visually-hidden">Trang cuối</span>
                    </span>
                @else
                    <a
                        class="page-link"
                        href="{{ $paginator->url($last) }}"
                        aria-label="Trang cuối"
                    >
                        <span aria-hidden="true">»</span>
                    </a>
                @endif
            </li>
        </ul>
    </nav>
@endif