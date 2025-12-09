import './bootstrap';
import { createApp } from 'vue';

// Import Vue components
import ToastNotification from './components/ToastNotification.vue';
import NotificationBadge from './components/NotificationBadge.vue';
import ProgressBar from './components/ProgressBar.vue';
import PaginationComponent from './components/PaginationComponent.vue';

// Create main Vue app
const app = createApp({
  components: {
    ToastNotification,
    NotificationBadge,
    ProgressBar,
    PaginationComponent
  }
});

// Register components globally
app.component('toast-notification', ToastNotification);
app.component('notification-badge', NotificationBadge);
app.component('progress-bar', ProgressBar);
app.component('pagination-component', PaginationComponent);

// Mount main app
app.mount('#vue-app');

// Mount additional Vue instances for components outside #vue-app
document.addEventListener('DOMContentLoaded', () => {
  // Mount notification badge if exists
  const notificationBell = document.getElementById('notification-bell');
  if (notificationBell) {
    const badgeApp = createApp({});
    badgeApp.component('notification-badge', NotificationBadge);
    badgeApp.mount('#notification-bell');
  }

  // Mount progress bar if exists
  const progressBarEl = document.getElementById('progress-bar-app');
  if (progressBarEl) {
    const progressApp = createApp({});
    progressApp.component('progress-bar', ProgressBar);
    progressApp.mount('#progress-bar-app');
  }

  // Mount pagination if exists
  const paginationEl = document.getElementById('feed-pagination-app');
  if (paginationEl) {
    const paginationApp = createApp({});
    paginationApp.component('pagination-component', PaginationComponent);
    paginationApp.mount('#feed-pagination-app');
  }
});

// Global toast helper function
window.showToast = (message, type = 'success', duration = 3000) => {
  window.dispatchEvent(new CustomEvent('show-toast', {
    detail: { message, type, duration }
  }));
};