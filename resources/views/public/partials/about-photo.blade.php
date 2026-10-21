<div class="home-photo about-photo">
    @if(file_exists(public_path('images/about/' . $filename)))
        <img src="{{ asset('images/about/' . $filename) }}"
             alt="{{ $alt }}"
             loading="{{ ($eager ?? false) ? 'eager' : 'lazy' }}"
             @if($eager ?? false) fetchpriority="high" @endif
             width="900" height="600">
    @else
        <div class="home-photo-placeholder">
            <i class="bi bi-{{ $icon }}" aria-hidden="true"></i>
            <span>{{ $alt }}</span>
        </div>
    @endif
</div>
