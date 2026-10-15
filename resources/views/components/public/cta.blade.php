@props(['title', 'description', 'href', 'label'])
<div class="public-cta" data-reveal>
    <div><h2>{{ $title }}</h2><p>{{ $description }}</p></div>
    <a class="btn btn-primary" href="{{ $href }}"><i class="bi bi-calendar-plus me-2" aria-hidden="true"></i>{{ $label }}</a>
</div>
