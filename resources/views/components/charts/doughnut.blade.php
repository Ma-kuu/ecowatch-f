@props(['id', 'title', 'icon' => 'bi-pie-chart', 'labels', 'data', 'colors'])

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
      type: 'doughnut',
      data: {
        labels: {!! json_encode($labels) !!},
        datasets: [{
          data: {!! json_encode($data) !!},
          backgroundColor: {!! json_encode($colors) !!},
          borderWidth: 2,
          borderColor: '#fff'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 15,
              font: { size: 11 }
            }
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            cornerRadius: 6,
            callbacks: {
              label: function(context) {
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percent = ((context.parsed / total) * 100).toFixed(1);
                return context.label + ': ' + context.parsed + ' (' + percent + '%)';
              }
            }
          }
        }
      }
    });
  })();
</script>
@endpush
