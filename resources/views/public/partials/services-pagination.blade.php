@if($paginator->total() > 0)
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
        $start = max(1, min($current - 1, $last - 2));
        $end = min($last, $start + 2);
    @endphp
    <nav class="services-pagination" aria-label="Phân trang dịch vụ">
        <ul>
            @foreach([
                ['label' => 'Trang đầu', 'icon' => 'chevron-double-left', 'page' => 1, 'disabled' => $current === 1],
                ['label' => 'Trang trước', 'icon' => 'chevron-left', 'page' => max(1, $current - 1), 'disabled' => $current === 1]
            ] as $control)
                <li>@if($control['disabled'])<span class="is-disabled" aria-disabled="true" aria-label="{{ $control['label'] }}"><i class="bi bi-{{ $control['icon'] }}" aria-hidden="true"></i></span>@else<a href="{{ $paginator->url($control['page']) }}" aria-label="{{ $control['label'] }}"><i class="bi bi-{{ $control['icon'] }}" aria-hidden="true"></i></a>@endif</li>
            @endforeach
            @for($page = $start; $page <= $end; $page++)
                <li>@if($page === $current)<span class="is-current" aria-current="page" aria-label="Trang {{ $page }}">{{ $page }}</span>@else<a href="{{ $paginator->url($page) }}" aria-label="Trang {{ $page }}">{{ $page }}</a>@endif</li>
            @endfor
            @foreach([
                ['label' => 'Trang sau', 'icon' => 'chevron-right', 'page' => min($last, $current + 1), 'disabled' => $current === $last],
                ['label' => 'Trang cuối', 'icon' => 'chevron-double-right', 'page' => $last, 'disabled' => $current === $last]
            ] as $control)
                <li>@if($control['disabled'])<span class="is-disabled" aria-disabled="true" aria-label="{{ $control['label'] }}"><i class="bi bi-{{ $control['icon'] }}" aria-hidden="true"></i></span>@else<a href="{{ $paginator->url($control['page']) }}" aria-label="{{ $control['label'] }}"><i class="bi bi-{{ $control['icon'] }}" aria-hidden="true"></i></a>@endif</li>
            @endforeach
        </ul>
    </nav>
@endif
