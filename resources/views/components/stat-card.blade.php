@props(['title', 'value', 'icon', 'color' => 'secondary', 'subtitle' => '', 'filterUrl' => null])

<div class="card stat-card {{ $color }} shadow-sm border-0 h-100 {{ $filterUrl ? 'clickable-card' : '' }}" 
     @if($filterUrl) onclick="window.location.href='{{ $filterUrl }}'" style="cursor: pointer;" @endif>
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <p class="text-muted text-uppercase small mb-1 fw-semibold">{{ $title }}</p>
        <h3 class="fw-bold mb-0">{{ $value }}</h3>
      </div>
      <div class="bg-{{ $color }} bg-opacity-10 rounded p-3">
        <i class="bi {{ $icon }} text-{{ $color }}" style="font-size: 24px;"></i>
      </div>
    </div>
    @if($subtitle)
      <p class="text-muted small mb-0 mt-2">{{ $subtitle }}</p>
    @endif
  </div>
</div>