@extends('layouts.dashboard')

@section('title', 'My Announcements - EcoWatch')

@section('dashboard-home', route('lgu-dashboard'))

@section('footer-title', 'EcoWatch')

@section('content')
  <!-- Page Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h2 class="fw-bold" style="color: #198754;">
            <i class="bi bi-megaphone me-2"></i>My Announcements
          </h2>
          <p class="text-muted">Manage and view your public announcements for {{ $lgu->name ?? 'your municipality' }}</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
          <i class="bi bi-plus-circle me-1"></i>Create New
        </button>
      </div>
    </div>
  </div>

  <!-- Success/Error Messages -->
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i>
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <!-- Announcements List -->
  <div class="row g-4">
    @forelse($announcements as $announcement)
    <div class="col-md-6 col-lg-4">
      <div class="card h-100 shadow-sm border-0">
        <div class="card-header bg-{{ $announcement->type === 'urgent' ? 'danger' : ($announcement->type === 'warning' ? 'warning' : ($announcement->type === 'success' ? 'success' : 'info')) }} text-white">
          <div class="d-flex justify-content-between align-items-center">
            <span class="fw-bold">
              <i class="bi bi-{{ $announcement->type === 'urgent' ? 'exclamation-triangle-fill' : ($announcement->type === 'warning' ? 'exclamation-circle-fill' : ($announcement->type === 'success' ? 'check-circle-fill' : 'info-circle-fill')) }}"></i>
              {{ ucfirst($announcement->type) }}
            </span>
            @if($announcement->is_pinned)
              <span class="badge bg-dark"><i class="bi bi-pin-angle-fill"></i> Pinned</span>
            @endif
          </div>
        </div>
        <div class="card-body">
          <h5 class="card-title fw-bold">{{ $announcement->title }}</h5>
          <p class="card-text text-muted small mb-2">
            <i class="bi bi-calendar"></i> {{ $announcement->created_at->format('M d, Y h:i A') }}
          </p>
          <p class="card-text">{{ Str::limit($announcement->content, 150) }}</p>
          
          <!-- Stats -->
          <div class="d-flex gap-3 mb-3">
            <div class="text-muted small">
              <i class="bi bi-hand-thumbs-up"></i> <span class="fw-bold">{{ $announcement->reactions_count ?? 0 }}</span> reactions
            </div>
          </div>

          @if($announcement->expires_at)
            <p class="text-muted small mb-0">
              <i class="bi bi-clock"></i> Expires: {{ $announcement->expires_at->format('M d, Y') }}
            </p>
          @endif
        </div>
        <div class="card-footer bg-white border-top">
          <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-primary flex-fill" 
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
            <form action="{{ route('lgu.announcements.destroy', $announcement->id) }}" method="POST" class="flex-fill" onsubmit="return confirm('Delete this announcement?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                <i class="bi bi-trash"></i> Delete
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
    @empty
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
          <i class="bi bi-megaphone" style="font-size: 64px; color: #dee2e6;"></i>
          <h5 class="mt-3 text-muted">No Announcements Yet</h5>
          <p class="text-muted">Create your first announcement to inform your community!</p>
          <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
            <i class="bi bi-plus-circle me-1"></i>Create Announcement
          </button>
        </div>
      </div>
    </div>
    @endforelse
  </div>

  <!-- Create Announcement Modal -->
  <div class="modal fade" id="createAnnouncementModal" tabindex="-1" aria-labelledby="createAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="createAnnouncementModalLabel">
            <i class="bi bi-megaphone"></i> Create Public Announcement
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{ route('lgu.announcements.store') }}" method="POST">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label for="announcementTitle" class="form-label fw-bold">Title <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="announcementTitle" name="title" required maxlength="200" placeholder="e.g., Community Clean-up Drive">
            </div>
            <div class="mb-3">
              <label for="announcementContent" class="form-label fw-bold">Content <span class="text-danger">*</span></label>
              <textarea class="form-control" id="announcementContent" name="content" rows="5" required placeholder="Enter the announcement details..."></textarea>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="announcementType" class="form-label fw-bold">Type <span class="text-danger">*</span></label>
                <select class="form-select" id="announcementType" name="type" required>
                  <option value="info" selected>Info</option>
                  <option value="warning">Warning</option>
                  <option value="urgent">Urgent</option>
                  <option value="success">Success</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label for="announcementExpires" class="form-label fw-bold">Expires On (Optional)</label>
                <input type="date" class="form-control" id="announcementExpires" name="expires_at" min="{{ date('Y-m-d') }}">
              </div>
            </div>
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="announcementPinned" name="is_pinned" value="1">
              <label class="form-check-label" for="announcementPinned">
                <i class="bi bi-pin-angle-fill"></i> Pin this announcement (appears at top)
              </label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-send"></i> Publish Announcement
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Announcement Modal -->
  <div class="modal fade" id="editAnnouncementModal" tabindex="-1" aria-labelledby="editAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="editAnnouncementModalLabel">
            <i class="bi bi-pencil"></i> Edit Announcement
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="editAnnouncementForm" method="POST">
          @csrf
          @method('PUT')
          <div class="modal-body">
            <div class="mb-3">
              <label for="editAnnouncementTitle" class="form-label fw-bold">Title <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="editAnnouncementTitle" name="title" required maxlength="200">
            </div>
            <div class="mb-3">
              <label for="editAnnouncementContent" class="form-label fw-bold">Content <span class="text-danger">*</span></label>
              <textarea class="form-control" id="editAnnouncementContent" name="content" rows="5" required></textarea>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="editAnnouncementType" class="form-label fw-bold">Type <span class="text-danger">*</span></label>
                <select class="form-select" id="editAnnouncementType" name="type" required>
                  <option value="info">Info</option>
                  <option value="warning">Warning</option>
                  <option value="urgent">Urgent</option>
                  <option value="success">Success</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label for="editAnnouncementExpires" class="form-label fw-bold">Expires On (Optional)</label>
                <input type="date" class="form-control" id="editAnnouncementExpires" name="expires_at" min="{{ date('Y-m-d') }}">
              </div>
            </div>
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="editAnnouncementPinned" name="is_pinned" value="1">
              <label class="form-check-label" for="editAnnouncementPinned">
                <i class="bi bi-pin-angle-fill"></i> Pin this announcement (appears at top)
              </label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save"></i> Update Announcement
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  // Handle Edit Announcement Modal
  const editAnnouncementModal = document.getElementById('editAnnouncementModal');
  if (editAnnouncementModal) {
    editAnnouncementModal.addEventListener('show.bs.modal', function(event) {
      const button = event.relatedTarget;
      const id = button.getAttribute('data-id');
      const title = button.getAttribute('data-title');
      const content = button.getAttribute('data-content');
      const type = button.getAttribute('data-type');
      const pinned = button.getAttribute('data-pinned');
      const expires = button.getAttribute('data-expires');

      // Update form action
      const form = document.getElementById('editAnnouncementForm');
      form.action = `/lgu/announcements/${id}`;

      // Populate fields
      document.getElementById('editAnnouncementTitle').value = title;
      document.getElementById('editAnnouncementContent').value = content;
      document.getElementById('editAnnouncementType').value = type;
      document.getElementById('editAnnouncementPinned').checked = pinned === '1';
      document.getElementById('editAnnouncementExpires').value = expires || '';
    });
  }
</script>
@endpush
