<template>
  <teleport to="body">
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
      <transition-group name="toast">
        <div 
          v-for="toast in toasts" 
          :key="toast.id"
          class="toast show align-items-center border-0"
          :class="`bg-${toast.type}`"
          role="alert"
        >
          <div class="d-flex">
            <div class="toast-body text-white">
              <i class="bi me-2" :class="getIcon(toast.type)"></i>
              {{ toast.message }}
            </div>
            <button 
              type="button" 
              class="btn-close btn-close-white me-2 m-auto" 
              @click="removeToast(toast.id)"
            ></button>
          </div>
        </div>
      </transition-group>
    </div>
  </teleport>
</template>

<script>
export default {
  name: 'ToastNotification',
  data() {
    return {
      toasts: []
    }
  },
  mounted() {
    // Listen for custom toast events
    window.addEventListener('show-toast', this.handleToastEvent);
  },
  beforeUnmount() {
    window.removeEventListener('show-toast', this.handleToastEvent);
  },
  methods: {
    handleToastEvent(event) {
      this.showToast(event.detail.message, event.detail.type, event.detail.duration);
    },
    showToast(message, type = 'success', duration = 3000) {
      const id = Date.now();
      this.toasts.push({ id, message, type });
      
      setTimeout(() => {
        this.removeToast(id);
      }, duration);
    },
    removeToast(id) {
      const index = this.toasts.findIndex(t => t.id === id);
      if (index > -1) {
        this.toasts.splice(index, 1);
      }
    },
    getIcon(type) {
      const icons = {
        success: 'bi-check-circle-fill',
        danger: 'bi-exclamation-triangle-fill',
        warning: 'bi-exclamation-circle-fill',
        info: 'bi-info-circle-fill'
      };
      return icons[type] || icons.info;
    }
  }
}
</script>

<style scoped>
.toast {
  min-width: 300px;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}
</style>
