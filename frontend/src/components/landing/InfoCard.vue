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
    bg: 'bg-blue-100',
    icon: 'text-blue-600',
  },
  purple: {
    bg: 'bg-purple-100',
    icon: 'text-purple-600',
  },
  pink: {
    bg: 'bg-pink-100',
    icon: 'text-pink-600',
  },
}
</script>

<template>
  <div class="bg-white rounded-xl shadow-md p-8 space-y-4 hover:shadow-lg transition-shadow">
    <div
      :class="[
        'w-12 h-12 rounded-lg flex items-center justify-center',
        colorClasses[iconColor].bg,
      ]"
    >
      <slot name="icon" :icon-class="colorClasses[iconColor].icon">
        <!-- Default icon if none provided -->
        <svg
          :class="['w-6 h-6', colorClasses[iconColor].icon]"
          fill="currentColor"
          viewBox="0 0 20 20"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"
          />
        </svg>
      </slot>
    </div>
    <h3 class="text-xl font-bold text-gray-900">{{ title }}</h3>
    <p class="text-gray-600">{{ description }}</p>
    <button
      :class="[
        'w-full font-medium py-2 px-4 rounded-lg transition-colors',
        buttonVariant === 'primary'
          ? 'bg-blue-600 hover:bg-blue-700 text-white'
          : 'bg-white hover:bg-gray-50 text-blue-600 border-2 border-blue-600',
      ]"
      @click="emit('action')"
    >
      {{ actionText }}
    </button>
  </div>
</template>

