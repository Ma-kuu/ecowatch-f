@props(['id', 'title', 'icon' => 'bi-graph-up', 'labels', 'data', 'color' => '#198754'])

<div class="card border-0 shadow-sm h-100">
  <div class="card-header bg-white border-bottom">
    <h6 class="mb-0 fw-bold">
      <i class="bi {{ $icon }} me-2 text-success"></i>{{ $title }}
    </h6>
  </div>
  <div class="card-body">
    <canvas id="{{ $id }}" height="250"></canvas>
  </div>
</div>

@push('scripts')
<script>
  (function() {
    const ctx = document.getElementById('{{ $id }}').getContext('2d');
    const color = '{{ $color }}';
    const rgbaColor = color.replace('#', '').match(/.{2}/g).map(x => parseInt(x, 16)).join(', ');
    
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: {!! json_encode($labels) !!},
        datasets: [{
          label: 'Reports',
          data: {!! json_encode($data) !!},
          borderColor: color,
          backgroundColor: `rgba(${rgbaColor}, 0.1)`,
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          pointBackgroundColor: color,
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 5,
          pointHoverRadius: 7
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            cornerRadius: 6
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { stepSize: 1 }
          }
        }
      }
    });
  })();
</script>
@endpush
