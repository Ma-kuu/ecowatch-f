<template>
  <nav aria-label="Page navigation" class="pagination-wrapper">
    <ul class="pagination justify-content-end mb-0">
      <!-- Previous Button -->
      <li class="page-item" :class="{ disabled: currentPage === 1 }">
        <a 
          class="page-link" 
          :href="currentPage > 1 ? getPageUrl(currentPage - 1) : '#'"
          @click.prevent="changePage(currentPage - 1)"
          aria-label="Previous"
        >
          <span aria-hidden="true">&laquo;</span>
        </a>
      </li>

      <!-- Page Numbers -->
      <li 
        v-for="page in visiblePages" 
        :key="page"
        class="page-item"
        :class="{ active: page === currentPage }"
      >
        <a 
          v-if="page !== '...'"
          class="page-link" 
          :href="getPageUrl(page)"
          @click.prevent="changePage(page)"
        >
          {{ page }}
        </a>
        <span v-else class="page-link">...</span>
      </li>

      <!-- Next Button -->
      <li class="page-item" :class="{ disabled: currentPage === totalPages }">
        <a 
          class="page-link" 
          :href="currentPage < totalPages ? getPageUrl(currentPage + 1) : '#'"
          @click.prevent="changePage(currentPage + 1)"
          aria-label="Next"
        >
          <span aria-hidden="true">&raquo;</span>
        </a>
      </li>
    </ul>
  </nav>
</template>

<script>
export default {
  name: 'PaginationComponent',
  props: {
    currentPage: {
      type: Number,
      required: true
    },
    totalPages: {
      type: Number,
      required: true
    },
    baseUrl: {
      type: String,
      required: true
    }
  },
  computed: {
    visiblePages() {
      const pages = [];
      const maxVisible = 5;
      
      if (this.totalPages <= maxVisible) {
        // Show all pages if total is small
        for (let i = 1; i <= this.totalPages; i++) {
          pages.push(i);
        }
      } else {
        // Always show first page
        pages.push(1);
        
        // Calculate range around current page
        let start = Math.max(2, this.currentPage - 1);
        let end = Math.min(this.totalPages - 1, this.currentPage + 1);
        
        // Add ellipsis after first page if needed
        if (start > 2) {
          pages.push('...');
        }
        
        // Add pages around current
        for (let i = start; i <= end; i++) {
          pages.push(i);
        }
        
        // Add ellipsis before last page if needed
        if (end < this.totalPages - 1) {
          pages.push('...');
        }
        
        // Always show last page
        pages.push(this.totalPages);
      }
      
      return pages;
    }
  },
  methods: {
    getPageUrl(page) {
      if (page < 1 || page > this.totalPages) return '#';
      
      const url = new URL(this.baseUrl, window.location.origin);
      url.searchParams.set('page', page);
      return url.toString();
    },
    changePage(page) {
      if (page < 1 || page > this.totalPages || page === this.currentPage) {
        return;
      }
      
      // Add smooth transition
      const container = document.querySelector('.tab-content');
      if (container) {
        container.style.opacity = '0.5';
        container.style.transition = 'opacity 0.3s ease';
      }
      
      // Navigate to new page
      window.location.href = this.getPageUrl(page);
    }
  }
}
</script>

<style scoped>
.pagination-wrapper {
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.page-link {
  transition: all 0.2s ease;
  cursor: pointer;
}

.page-link:hover {
  transform: translateY(-2px);
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.page-item.active .page-link {
  background-color: #198754;
  border-color: #198754;
  transform: scale(1.1);
}

.page-item.disabled .page-link {
  cursor: not-allowed;
  opacity: 0.5;
}
</style>
