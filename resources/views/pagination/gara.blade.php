@if ($paginator->hasPages())
    <nav class="pager" role="navigation" aria-label="Phân trang">
        @if ($paginator->onFirstPage())
            <span class="dis">‹ Trước</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev">‹ Trước</a>
        @endif

        <span class="on">Trang {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next">Sau ›</a>
        @else
            <span class="dis">Sau ›</span>
        @endif
    </nav>
@endif
