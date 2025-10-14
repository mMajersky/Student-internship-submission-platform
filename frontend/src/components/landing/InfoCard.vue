<script setup>
defineProps({
  title: {
    type: String,
    required: true,
  },
  description: {
    type: String,
    required: true,
  },
  actionText: {
    type: String,
    required: true,
  },
  iconColor: {
    type: String,
    default: 'blue',
  },
  buttonVariant: {
    type: String,
    default: 'primary', // 'primary' or 'outline'
  },
})

const emit = defineEmits(['action'])

const colorClasses = {
  blue: {
    bg: 'bg-primary bg-opacity-10',
    icon: 'text-primary',
  },
  purple: {
    bg: 'bg-info bg-opacity-10',
    icon: 'text-info',
  },
  pink: {
    bg: 'bg-danger bg-opacity-10',
    icon: 'text-danger',
  },
}
</script>

<template>
  <div class="card h-100 shadow-sm">
    <div class="card-body d-flex flex-column">
      <div
        :class="[
          'w-12 h-12 rounded d-flex align-items-center justify-content-center mb-3',
          colorClasses[iconColor].bg,
        ]"
      >
        <slot name="icon" :icon-class="colorClasses[iconColor].icon">
          <!-- Default icon if none provided -->
          <i :class="['bi bi-mortarboard fs-4', colorClasses[iconColor].icon]"></i>
        </slot>
      </div>
      <h5 class="card-title fw-bold text-dark mb-3">{{ title }}</h5>
      <p class="card-text text-muted flex-grow-1">{{ description }}</p>
      <button
        :class="[
          'btn w-100',
          buttonVariant === 'primary' ? 'btn-primary' : 'btn-outline-primary',
        ]"
        @click="emit('action')"
      >
        {{ actionText }}
      </button>
    </div>
  </div>
</template>

