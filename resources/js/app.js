import './bootstrap';
import { createApp } from 'vue';

// Import Vue components
import ToastNotification from './components/ToastNotification.vue';

const app = createApp({
  components: {
    ToastNotification
  }
});

// Register components
app.component('toast-notification', ToastNotification);

// Mount to body (always available)
app.mount('#vue-app');

// Global toast helper function
window.showToast = (message, type = 'success', duration = 3000) => {
  window.dispatchEvent(new CustomEvent('show-toast', {
    detail: { message, type, duration }
  }));
};