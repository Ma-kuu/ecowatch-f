@extends('layouts.dashboard')

@section('title', 'Notifications - EcoWatch')

@section('dashboard-home', route(auth()->user()->role . '-dashboard'))

@section('content')
  <!-- Page Header -->
  <div class="row mb-4">
    <div class="col-md-8">
      <h2 class="fw-bold mb-1">Notifications</h2>
      <p class="text-muted">Stay updated on your reports and activities</p>
    </div>
    <div class="col-md-4 text-md-end">
      @if($notifications->where('is_read', false)->count() > 0)
        <form method="POST" action="{{ route('notifications.read-all') }}" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-outline-success btn-sm">
            <i class="bi bi-check-all me-1"></i>Mark all as read
          </button>
        </form>
      @endif
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <!-- Notifications List -->
  <div class="row">
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
          @forelse($notifications as $notification)
            <div class="notification-item p-4 border-bottom {{ $notification->is_read ? '' : 'bg-light' }}" 
                 style="transition: background-color 0.2s;">
              <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                  <div class="d-flex align-items-center mb-2">
                    @if(!$notification->is_read)
                      <span class="badge bg-success me-2" style="width: 8px; height: 8px; padding: 0; border-radius: 50%;"></span>
                    @endif
                    <h6 class="mb-0 {{ $notification->is_read ? 'text-muted' : 'fw-bold' }}">
                      {{ $notification->title }}
                    </h6>
                  </div>
                  <p class="mb-2 {{ $notification->is_read ? 'text-muted' : '' }}" style="font-size: 14px;">
                    {{ $notification->message }}
                  </p>
                  <div class="d-flex align-items-center text-muted" style="font-size: 13px;">
                    <i class="bi bi-clock me-1"></i>
                    <span>{{ $notification->created_at->diffForHumans() }}</span>
                    @if($notification->report_id)
                      <span class="mx-2">•</span>
                      @php
                        $dashboardRoute = auth()->user()->role === 'admin' ? 'admin-dashboard' : 
                                         (auth()->user()->role === 'lgu' ? 'lgu-dashboard' : 'user-dashboard');
                      @endphp
                      <a href="{{ route($dashboardRoute) }}#report-{{ $notification->report_id }}" 
                         class="text-success text-decoration-none">
                        <i class="bi bi-file-text me-1"></i>View report
                      </a>
                    @endif
                  </div>
                </div>
                <div class="ms-3">
                  @if(!$notification->is_read)
                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-outline-secondary" title="Mark as read">
                        <i class="bi bi-check"></i>
                      </button>
                    </form>
                  @endif
                </div>
              </div>
            </div>
          @empty
            <div class="text-center py-5">
              <i class="bi bi-bell-slash text-muted" style="font-size: 48px;"></i>
              <p class="text-muted mt-3 mb-0">No notifications yet</p>
              <p class="text-muted small">You'll be notified about updates to your reports</p>
            </div>
          @endforelse
        </div>
      </div>

      <!-- Pagination -->
      @if($notifications->hasPages())
        <div class="mt-4">
          {{ $notifications->links() }}
        </div>
      @endif
    </div>
  </div>
@endsection

@push('styles')
<style>
  .notification-item:hover {
    background-color: #f8f9fa !important;
  }
  .notification-item:last-child {
    border-bottom: none !important;
  }
</style>
@endpush
