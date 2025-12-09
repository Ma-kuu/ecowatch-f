<!-- Public Announcements Section -->
<div class="card shadow-sm border-0 mb-4">
  <div class="card-header bg-white border-bottom py-3">
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="fw-bold mb-0">
        <i class="bi bi-megaphone me-2 text-primary"></i>Public Announcements
      </h5>
      <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
        <i class="bi bi-plus-circle me-1"></i>Create Announcement
      </button>
    </div>
  </div>
  <div class="card-body">
    @forelse($announcements ?? [] as $announcement)
    <div class="card mb-3 border">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <h6 class="fw-bold mb-0">{{ $announcement->title }}</h6>
          <div>
            <span class="badge bg-{{ $announcement->type === 'urgent' ? 'danger' : ($announcement->type === 'warning' ? 'warning' : ($announcement->type === 'success' ? 'success' : 'info')) }} me-1">
              {{ ucfirst($announcement->type) }}
            </span>
            @if($announcement->is_pinned)
              <span class="badge bg-secondary"><i class="bi bi-pin-angle-fill"></i> Pinned</span>
            @endif
          </div>
        </div>
        <p class="text-muted small mb-2">
          <i class="bi bi-calendar"></i> {{ $announcement->created_at->format('M d, Y h:i A') }}
          @if($announcement->expires_at)
            • <i class="bi bi-clock"></i> Expires: {{ $announcement->expires_at->format('M d, Y') }}
          @endif
        </p>
        <p class="mb-2">{{ $announcement->content }}</p>
        <div class="d-flex gap-2">
          <button class="btn btn-sm btn-outline-primary" 
                  data-bs-toggle="modal" 
                  data-bs-target="#editAnnouncementModal"
                  data-id="{{ $announcement->id }}"
                  data-title="{{ $announcement->title }}"
                  data-content="{{ $announcement->content }}"
                  data-type="{{ $announcement->type }}"
                  data-pinned="{{ $announcement->is_pinned ? '1' : '0' }}"
                  data-expires="{{ $announcement->expires_at ? $announcement->expires_at->format('Y-m-d') : '' }}">
            <i class="bi bi-pencil"></i> Edit
          </button>
          <form action="{{ route('lgu.announcements.destroy', $announcement->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this announcement?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger">
              <i class="bi bi-trash"></i> Delete
            </button>
          </form>
        </div>
      </div>
    </div>
    @empty
    <div class="text-center py-4 text-muted">
      <i class="bi bi-megaphone" style="font-size: 48px; color: #dee2e6;"></i>
      <p class="mt-3">No announcements yet. Create one to inform your community!</p>
    </div>
    @endforelse
  </div>
</div>
