<template>
  <span 
    v-if="count > 0" 
    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge"
    :class="{ 'pulse': shouldPulse }"
  >
    {{ displayCount }}
  </span>
</template>

<script>
export default {
  name: 'NotificationBadge',
  props: {
    count: {
      type: Number,
      default: 0
    }
  },
  data() {
    return {
      shouldPulse: false
    }
  },
  computed: {
    displayCount() {
      return this.count > 9 ? '9+' : this.count;
    }
  },
  watch: {
    count(newVal, oldVal) {
      // Trigger pulse animation when count increases
      if (newVal > oldVal && newVal > 0) {
        this.shouldPulse = true;
        setTimeout(() => {
          this.shouldPulse = false;
        }, 1000);
      }
    }
  }
}
</script>

<style scoped>
.notification-badge {
  font-size: 10px;
  padding: 3px 6px;
  animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translate(-50%, -50%) scale(0.5);
  }
  to {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
  }
}

.notification-badge.pulse {
  animation: pulse 0.6s ease-in-out;
}

@keyframes pulse {
  0%, 100% {
    transform: translate(-50%, -50%) scale(1);
  }
  50% {
    transform: translate(-50%, -50%) scale(1.3);
  }
}
</style>
