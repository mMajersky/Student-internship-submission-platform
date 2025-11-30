<template>
  <div class="row mb-4 g-3">
    <!-- Year Filter -->
    <div class="col-md-3">
      <label class="form-label fw-semibold">
        <i class="bi bi-calendar3 me-1"></i>
        {{ $t('garantDashboard.filters.year') }}
      </label>
      <div class="dropdown">
        <button 
          class="btn btn-outline-secondary dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center" 
          type="button" 
          data-bs-toggle="dropdown" 
          data-bs-auto-close="outside"
          aria-expanded="false"
        >
          <span class="text-truncate">
            {{ selectedYears.length === 0 ? $t('garantDashboard.filters.all') : 
               selectedYears.length === availableYears.length ? $t('garantDashboard.filters.all') :
               selectedYears.length === 1 ? selectedYears[0] : 
               $t('garantDashboard.filters.selected', { count: selectedYears.length }) }}
          </span>
        </button>
        <ul class="dropdown-menu w-100 p-2" style="max-height: 250px; overflow-y: auto;">
          <li>
            <div class="form-check mb-2 pb-2 border-bottom">
              <input 
                class="form-check-input" 
                type="checkbox" 
                :checked="selectedYears.length === availableYears.length"
                :indeterminate="selectedYears.length > 0 && selectedYears.length < availableYears.length"
                @change="toggleSelectAllYears"
                :id="idPrefix + 'selectAllYears'"
              >
              <label class="form-check-label fw-semibold" :for="idPrefix + 'selectAllYears'">
                {{ $t('garantDashboard.filters.selectAll') }}
              </label>
            </div>
          </li>
          <li v-for="year in availableYears" :key="year">
            <div class="form-check">
              <input 
                class="form-check-input" 
                type="checkbox" 
                :value="year" 
                v-model="localSelectedYears"
                :id="idPrefix + 'year-' + year"
              >
              <label class="form-check-label" :for="idPrefix + 'year-' + year">{{ year }}</label>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <!-- Company Filter -->
    <div class="col-md-3">
      <label class="form-label fw-semibold">
        <i class="bi bi-building me-1"></i>
        {{ $t('garantDashboard.filters.company') }}
      </label>
      <div class="dropdown">
        <button 
          class="btn btn-outline-secondary dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center" 
          type="button" 
          data-bs-toggle="dropdown"
          data-bs-auto-close="outside"
          aria-expanded="false"
        >
          <span class="text-truncate">
            {{ selectedCompanies.length === 0 ? $t('garantDashboard.filters.all') : 
               selectedCompanies.length === availableCompanies.length ? $t('garantDashboard.filters.all') :
               selectedCompanies.length === 1 ? availableCompanies.find(c => c.id === selectedCompanies[0])?.name : 
               $t('garantDashboard.filters.selected', { count: selectedCompanies.length }) }}
          </span>
        </button>
        <ul class="dropdown-menu w-100 p-2" style="max-height: 250px; overflow-y: auto;">
          <li>
            <div class="form-check mb-2 pb-2 border-bottom">
              <input 
                class="form-check-input" 
                type="checkbox" 
                :checked="selectedCompanies.length === availableCompanies.length"
                :indeterminate="selectedCompanies.length > 0 && selectedCompanies.length < availableCompanies.length"
                @change="toggleSelectAllCompanies"
                :id="idPrefix + 'selectAllCompanies'"
              >
              <label class="form-check-label fw-semibold" :for="idPrefix + 'selectAllCompanies'">
                {{ $t('garantDashboard.filters.selectAll') }}
              </label>
            </div>
          </li>
          <li v-for="company in availableCompanies" :key="company.id">
            <div class="form-check">
              <input 
                class="form-check-input" 
                type="checkbox" 
                :value="company.id" 
                v-model="localSelectedCompanies"
                :id="idPrefix + 'company-' + company.id"
              >
              <label class="form-check-label" :for="idPrefix + 'company-' + company.id">{{ company.name }}</label>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <!-- Study Field Filter -->
    <div class="col-md-3">
      <label class="form-label fw-semibold">
        <i class="bi bi-mortarboard me-1"></i>
        {{ $t('garantDashboard.filters.studyField') }}
      </label>
      <div class="dropdown">
        <button 
          class="btn btn-outline-secondary dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center" 
          type="button" 
          data-bs-toggle="dropdown"
          data-bs-auto-close="outside"
          aria-expanded="false"
        >
          <span class="text-truncate">
            {{ selectedStudyFields.length === 0 ? $t('garantDashboard.filters.all') : 
               selectedStudyFields.length === availableStudyFields.length ? $t('garantDashboard.filters.all') :
               selectedStudyFields.length === 1 ? selectedStudyFields[0] : 
               $t('garantDashboard.filters.selected', { count: selectedStudyFields.length }) }}
          </span>
        </button>
        <ul class="dropdown-menu w-100 p-2" style="max-height: 250px; overflow-y: auto;">
          <li>
            <div class="form-check mb-2 pb-2 border-bottom">
              <input 
                class="form-check-input" 
                type="checkbox" 
                :checked="selectedStudyFields.length === availableStudyFields.length"
                :indeterminate="selectedStudyFields.length > 0 && selectedStudyFields.length < availableStudyFields.length"
                @change="toggleSelectAllStudyFields"
                :id="idPrefix + 'selectAllStudyFields'"
              >
              <label class="form-check-label fw-semibold" :for="idPrefix + 'selectAllStudyFields'">
                {{ $t('garantDashboard.filters.selectAll') }}
              </label>
            </div>
          </li>
          <li v-for="field in availableStudyFields" :key="field">
            <div class="form-check">
              <input 
                class="form-check-input" 
                type="checkbox" 
                :value="field" 
                v-model="localSelectedStudyFields"
                :id="idPrefix + 'field-' + field"
              >
              <label class="form-check-label" :for="idPrefix + 'field-' + field">{{ field }}</label>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <!-- Student Filter -->
    <div class="col-md-3">
      <label class="form-label fw-semibold">
        <i class="bi bi-person me-1"></i>
        {{ $t('garantDashboard.filters.student') }}
      </label>
      <div class="dropdown">
        <button 
          class="btn btn-outline-secondary dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center" 
          type="button" 
          data-bs-toggle="dropdown"
          data-bs-auto-close="outside"
          aria-expanded="false"
        >
          <span class="text-truncate">
            {{ selectedStudents.length === 0 ? $t('garantDashboard.filters.all') : 
               selectedStudents.length === availableStudents.length ? $t('garantDashboard.filters.all') :
               selectedStudents.length === 1 ? availableStudents.find(s => s.id === selectedStudents[0])?.name : 
               $t('garantDashboard.filters.selected', { count: selectedStudents.length }) }}
          </span>
        </button>
        <ul class="dropdown-menu w-100 p-2" style="max-height: 250px; overflow-y: auto;">
          <li>
            <div class="form-check mb-2 pb-2 border-bottom">
              <input 
                class="form-check-input" 
                type="checkbox" 
                :checked="selectedStudents.length === availableStudents.length"
                :indeterminate="selectedStudents.length > 0 && selectedStudents.length < availableStudents.length"
                @change="toggleSelectAllStudents"
                :id="idPrefix + 'selectAllStudents'"
              >
              <label class="form-check-label fw-semibold" :for="idPrefix + 'selectAllStudents'">
                {{ $t('garantDashboard.filters.selectAll') }}
              </label>
            </div>
          </li>
          <li v-for="student in availableStudents" :key="student.id">
            <div class="form-check">
              <input 
                class="form-check-input" 
                type="checkbox" 
                :value="student.id" 
                v-model="localSelectedStudents"
                :id="idPrefix + 'student-' + student.id"
              >
              <label class="form-check-label" :for="idPrefix + 'student-' + student.id">{{ student.name }}</label>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, watch } from 'vue'

const props = defineProps({
  // Available options
  availableYears: {
    type: Array,
    required: true
  },
  availableCompanies: {
    type: Array,
    required: true
  },
  availableStudyFields: {
    type: Array,
    required: true
  },
  availableStudents: {
    type: Array,
    required: true
  },
  // Selected values
  selectedYears: {
    type: Array,
    default: () => []
  },
  selectedCompanies: {
    type: Array,
    default: () => []
  },
  selectedStudyFields: {
    type: Array,
    default: () => []
  },
  selectedStudents: {
    type: Array,
    default: () => []
  },
  // Unique ID prefix for form elements (to avoid ID collisions when used multiple times)
  idPrefix: {
    type: String,
    default: ''
  }
})

const emit = defineEmits([
  'update:selectedYears',
  'update:selectedCompanies',
  'update:selectedStudyFields',
  'update:selectedStudents'
])

// Local computed properties with v-model support
const localSelectedYears = computed({
  get: () => props.selectedYears,
  set: (value) => emit('update:selectedYears', value)
})

const localSelectedCompanies = computed({
  get: () => props.selectedCompanies,
  set: (value) => emit('update:selectedCompanies', value)
})

const localSelectedStudyFields = computed({
  get: () => props.selectedStudyFields,
  set: (value) => emit('update:selectedStudyFields', value)
})

const localSelectedStudents = computed({
  get: () => props.selectedStudents,
  set: (value) => emit('update:selectedStudents', value)
})

// Toggle functions
const toggleSelectAllYears = () => {
  if (props.selectedYears.length === props.availableYears.length) {
    emit('update:selectedYears', [])
  } else {
    emit('update:selectedYears', [...props.availableYears])
  }
}

const toggleSelectAllCompanies = () => {
  if (props.selectedCompanies.length === props.availableCompanies.length) {
    emit('update:selectedCompanies', [])
  } else {
    emit('update:selectedCompanies', props.availableCompanies.map(c => c.id))
  }
}

const toggleSelectAllStudyFields = () => {
  if (props.selectedStudyFields.length === props.availableStudyFields.length) {
    emit('update:selectedStudyFields', [])
  } else {
    emit('update:selectedStudyFields', [...props.availableStudyFields])
  }
}

const toggleSelectAllStudents = () => {
  if (props.selectedStudents.length === props.availableStudents.length) {
    emit('update:selectedStudents', [])
  } else {
    emit('update:selectedStudents', props.availableStudents.map(s => s.id))
  }
}
</script>
