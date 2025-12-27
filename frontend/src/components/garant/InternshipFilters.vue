<template>
  <div class="row mb-4 g-3">
    <div v-for="filter in dropdownFilters" :key="filter.key" class="col-md-3">
      <label class="form-label fw-semibold">
        <i :class="`bi ${filter.icon} me-1`"></i>
        {{ $t(filter.label) }}
      </label>
      <div class="dropdown">
        <button 
          class="btn btn-outline-secondary dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center" 
          type="button" 
          data-bs-toggle="dropdown" 
          data-bs-auto-close="outside"
          aria-expanded="false"
        >
          <span class="text-truncate">{{ getButtonLabel(filter) }}</span>
        </button>
        <ul class="dropdown-menu w-100 p-2" style="max-height: 250px; overflow-y: auto;">
          <li>
            <div class="form-check mb-2 pb-2 border-bottom">
              <input 
                class="form-check-input" 
                type="checkbox" 
                :checked="filter.selected.length === filter.available.length"
                :indeterminate.prop="filter.selected.length > 0 && filter.selected.length < filter.available.length"
                @change="toggleSelectAll(filter)"
                :id="`${idPrefix}selectAll${filter.key}`"
              >
              <label class="form-check-label fw-semibold" :for="`${idPrefix}selectAll${filter.key}`">
                {{ $t('garantDashboard.filters.selectAll') }}
              </label>
            </div>
          </li>
          <li v-for="item in filter.available" :key="filter.getValue(item)">
            <div class="form-check">
              <input 
                class="form-check-input" 
                type="checkbox" 
                :value="filter.getValue(item)" 
                :checked="filter.selected.includes(filter.getValue(item))"
                @change="toggleItem(filter, filter.getValue(item))"
                :id="`${idPrefix}${filter.key}-${filter.getValue(item)}`"
              >
              <label class="form-check-label" :for="`${idPrefix}${filter.key}-${filter.getValue(item)}`">
                {{ filter.getLabel(item) }}
              </label>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <!-- Student Search -->
    <div v-if="showStudent" class="col-md-3">
      <label class="form-label fw-semibold">
        <i class="bi bi-person me-1"></i>
        {{ $t('garantDashboard.filters.student') }}
      </label>
      <div class="position-relative">
        <input 
          type="text"
          class="form-control"
          :placeholder="$t('garantDashboard.filters.searchStudent')"
          v-model="studentSearchQuery"
          @input="handleStudentSearch"
        >
        <i class="bi bi-search position-absolute top-50 end-0 translate-middle-y me-2 text-muted"></i>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
  availableYears: { type: Array, required: true },
  availableCompanies: { type: Array, required: true },
  availableStudyFields: { type: Array, required: true },
  availableStudents: { type: Array, required: true },
  selectedYears: { type: Array, default: () => [] },
  selectedCompanies: { type: Array, default: () => [] },
  selectedStudyFields: { type: Array, default: () => [] },
  studentSearchQuery: { type: String, default: '' },
  idPrefix: { type: String, default: '' },
    showStudyField: {
    type: Boolean,
    default: true
  },
  showStudent: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits([
  'update:selectedYears',
  'update:selectedCompanies',
  'update:selectedStudyFields',
  'update:studentSearchQuery'
])

const studentSearchQuery = computed({
  get: () => props.studentSearchQuery,
  set: (value) => emit('update:studentSearchQuery', value)
})

// Data-driven filter configuration (only dropdown filters)
const dropdownFilters = computed(() => [
  {
    key: 'Years',
    icon: 'bi-calendar3',
    label: 'garantDashboard.filters.year',
    available: props.availableYears,
    selected: props.selectedYears,
    emit: 'update:selectedYears',
    getValue: (item) => item,
    getLabel: (item) => item,
    getSingleLabel: (item) => item
  },
  {
    key: 'Companies',
    icon: 'bi-building',
    label: 'garantDashboard.filters.company',
    available: props.availableCompanies,
    selected: props.selectedCompanies,
    emit: 'update:selectedCompanies',
    getValue: (item) => item.id,
    getLabel: (item) => item.name,
    getSingleLabel: (item) =>
      props.availableCompanies.find(c => c.id === item)?.name
  },
  // 🔥 Study field – ONLY IF ENABLED
  ...(props.showStudyField ? [{
    key: 'StudyFields',
    icon: 'bi-mortarboard',
    label: 'garantDashboard.filters.studyField',
    available: props.availableStudyFields,
    selected: props.selectedStudyFields,
    emit: 'update:selectedStudyFields',
    getValue: (item) => item,
    getLabel: (item) => item,
    getSingleLabel: (item) => item
  }] : [])
])


const getButtonLabel = (filter) => {
  const { selected, available, getSingleLabel } = filter
  if (selected.length === 0 || selected.length === available.length) {
    return props.$t ? props.$t('garantDashboard.filters.all') : 'All'
  }
  if (selected.length === 1) {
    return getSingleLabel(selected[0])
  }
  return props.$t ? props.$t('garantDashboard.filters.selected', { count: selected.length }) : `${selected.length} selected`
}

const toggleSelectAll = (filter) => {
  const allSelected = filter.selected.length === filter.available.length
  emit(filter.emit, allSelected ? [] : filter.available.map(filter.getValue))
}

const toggleItem = (filter, value) => {
  const newSelected = filter.selected.includes(value)
    ? filter.selected.filter(v => v !== value)
    : [...filter.selected, value]
  emit(filter.emit, newSelected)
}

const handleStudentSearch = () => {
  // Emitting happens automatically via v-model
}

</script>
