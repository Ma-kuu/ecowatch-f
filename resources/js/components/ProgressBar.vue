<template>
  <div class="progress-tracker">
    <div class="progress-bar-container">
      <div class="progress-bar-bg">
        <div 
          class="progress-bar-fill" 
          :style="{ width: progressPercentage + '%' }"
          :class="progressColorClass"
        ></div>
      </div>
      <div class="progress-percentage">{{ progressPercentage }}%</div>
    </div>
    
    <div class="progress-steps">
      <div 
        v-for="(step, index) in steps" 
        :key="index"
        class="progress-step"
        :class="{ 
          'completed': index < currentStepIndex,
          'active': index === currentStepIndex,
          'pending': index > currentStepIndex
        }"
      >
        <div class="step-icon">
          <i v-if="index < currentStepIndex" class="bi bi-check-circle-fill"></i>
          <i v-else-if="index === currentStepIndex" class="bi bi-arrow-right-circle-fill"></i>
          <i v-else class="bi bi-circle"></i>
        </div>
        <div class="step-label">{{ step }}</div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ProgressBar',
  props: {
    status: {
      type: String,
      required: true,
      validator: (value) => ['pending', 'in-review', 'in-progress', 'resolved'].includes(value)
    }
  },
  data() {
    return {
      steps: ['Submitted', 'Verified', 'In Progress', 'Resolved']
    }
  },
  computed: {
    currentStepIndex() {
      const statusMap = {
        'pending': 0,
        'in-review': 1,
        'in-progress': 2,
        'resolved': 3
      };
      return statusMap[this.status] || 0;
    },
    progressPercentage() {
      return Math.round((this.currentStepIndex / (this.steps.length - 1)) * 100);
    },
    progressColorClass() {
      if (this.currentStepIndex === 0) return 'progress-pending';
      if (this.currentStepIndex === 1) return 'progress-review';
      if (this.currentStepIndex === 2) return 'progress-ongoing';
      if (this.currentStepIndex === 3) return 'progress-resolved';
      return 'progress-pending';
    }
  }
}
</script>

<style scoped>
.progress-tracker {
  margin: 20px 0;
}

.progress-bar-container {
  position: relative;
  margin-bottom: 30px;
}

.progress-bar-bg {
  height: 12px;
  background-color: #e9ecef;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
}

.progress-bar-fill {
  height: 100%;
  transition: width 0.8s ease-in-out, background-color 0.5s ease;
  border-radius: 10px;
}

.progress-pending {
  background: linear-gradient(90deg, #ffc107 0%, #ffca2c 100%);
}

.progress-review {
  background: linear-gradient(90deg, #0dcaf0 0%, #31d2f2 100%);
}

.progress-ongoing {
  background: linear-gradient(90deg, #0d6efd 0%, #3184fd 100%);
}

.progress-resolved {
  background: linear-gradient(90deg, #198754 0%, #20c997 100%);
}

.progress-percentage {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 11px;
  font-weight: 700;
  color: #fff;
  text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.progress-steps {
  display: flex;
  justify-content: space-between;
  gap: 10px;
}

.progress-step {
  flex: 1;
  text-align: center;
  transition: all 0.3s ease;
}

.step-icon {
  font-size: 24px;
  margin-bottom: 8px;
  transition: transform 0.3s ease;
}

.progress-step.completed .step-icon {
  color: #198754;
  animation: checkmark 0.5s ease;
}

.progress-step.active .step-icon {
  color: #0d6efd;
  animation: pulse-icon 1.5s ease-in-out infinite;
}

.progress-step.pending .step-icon {
  color: #dee2e6;
}

.step-label {
  font-size: 12px;
  font-weight: 600;
  color: #6c757d;
  transition: color 0.3s ease;
}

.progress-step.completed .step-label {
  color: #198754;
}

.progress-step.active .step-label {
  color: #0d6efd;
}

@keyframes checkmark {
  0% {
    transform: scale(0.5);
    opacity: 0;
  }
  50% {
    transform: scale(1.2);
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

@keyframes pulse-icon {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1);
  }
}

/* Responsive */
@media (max-width: 575.98px) {
  .step-label {
    font-size: 10px;
  }
  .step-icon {
    font-size: 20px;
  }
}
</style>
