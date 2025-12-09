@props(['id', 'title', 'icon' => 'bi-bar-chart', 'labels', 'data', 'colors'])

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
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: {!! json_encode($labels) !!},
        datasets: [{
          label: 'Reports',
          data: {!! json_encode($data) !!},
          backgroundColor: {!! json_encode($colors) !!},
          borderRadius: 6
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
