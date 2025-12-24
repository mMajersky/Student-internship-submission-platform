<template>
  <section class="py-3">
    <h4 class="mb-3">Štatistiky praxí</h4>

    <!-- FILTERS -->
    <InternshipFilters
      v-if="internships.length > 0"
      :available-years="availableYears"
      :available-companies="availableCompanies"
      :available-study-fields="[]"
      :available-students="[]"
      v-model:selected-years="selectedYears"
      v-model:selected-companies="selectedCompanies"
      v-model:selected-study-fields="dummyStudyFields"
      v-model:student-search-query="dummyStudentSearch"
      id-prefix="stats-"
    />

    <!-- CHARTS -->
    <div class="row g-4 mt-3">

      <!-- YEAR TREND -->
      <div class="col-md-6 col-lg-4">
        <h6 class="text-center mb-2">
          Počet praxí podľa akademického roka
        </h6>
        <div style="height:260px">
          <Line :data="yearChartData" :options="baseOptions" />
        </div>
      </div>

      <!-- TYPES -->
      <div class="col-md-6 col-lg-4">
        <h6 class="text-center mb-2">
          Typy praxí
        </h6>
        <div style="height:260px">
          <Doughnut :data="typesChartData" />
        </div>
      </div>

      <!-- COMPANIES -->
      <div class="col-12">
        <h6 class="text-center mb-2">
          Praxe podľa firmy
        </h6>
        <div style="height:350px">
          <Bar :data="companiesChartData" :options="horizontalOptions" />
        </div>
      </div>

    </div>
  </section>
</template>

<script setup>
/* ------------------ VUE ------------------ */
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'

/* ------------------ CHART.JS ------------------ */
import { Line, Doughnut, Bar } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  ArcElement,
  BarElement,
  CategoryScale,
  LinearScale
} from 'chart.js'

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  ArcElement,
  BarElement,
  CategoryScale,
  LinearScale
)

/* ------------------ COMPONENTS ------------------ */
import InternshipFilters from '@/components/garant/InternshipFilters.vue'

/* ------------------ AUTH ------------------ */
const authStore = useAuthStore()

/* ------------------ DATA ------------------ */
const internships = ref([])

/* filters */
const selectedYears = ref([])
const selectedCompanies = ref([])

/* dummy (required by InternshipFilters) */
const dummyStudyFields = ref([])
const dummyStudentSearch = ref('')

/* ------------------ FETCH ------------------ */
const fetchInternships = async () => {
  const res = await fetch('/api/internships', {
    headers: {
      Authorization: `Bearer ${authStore.token}`,
      Accept: 'application/json',
    },
  })

  const data = await res.json()
  internships.value = data.data || data
}

onMounted(fetchInternships)

/* ------------------ OPTIONS ------------------ */
const availableYears = computed(() => {
  const set = new Set()
  internships.value.forEach(i => i.academy_year && set.add(i.academy_year))
  return Array.from(set).sort().reverse()
})

const availableCompanies = computed(() => {
  const map = new Map()
  internships.value.forEach(i => i.company && map.set(i.company.id, i.company))
  return Array.from(map.values())
})

/* normalize company IDs */
const selectedCompanyIds = computed(() =>
  selectedCompanies.value
    .map(c => typeof c === 'object' ? c.id : c)
    .filter(Boolean)
)

/* ------------------ FILTERED DATA ------------------ */
const filteredInternships = computed(() => {
  return internships.value.filter(i => {

    if (
      selectedYears.value.length > 0 &&
      !selectedYears.value.includes(i.academy_year)
    ) return false

    if (
      selectedCompanyIds.value.length > 0 &&
      !selectedCompanyIds.value.includes(i.company?.id)
    ) return false

    return true
  })
})

/* ------------------ CHART DATA ------------------ */

/* 1️⃣ Year trend */
const yearChartData = computed(() => {
  const map = {}
  filteredInternships.value.forEach(i => {
    if (!i.academy_year) return
    map[i.academy_year] = (map[i.academy_year] || 0) + 1
  })

  const labels = Object.keys(map).sort()
  return {
    labels,
    datasets: [{
      label: 'Počet praxí',
      data: labels.map(l => map[l]),
      borderColor: '#4C9AFF',
      backgroundColor: 'rgba(76,154,255,0.25)',
      tension: 0.3
    }]
  }
})

/* 2️⃣ Types */
const typesChartData = computed(() => {
  const map = {}
  filteredInternships.value.forEach(i => {
    if (!i.type) return
    map[i.type] = (map[i.type] || 0) + 1
  })

  return {
    labels: Object.keys(map),
    datasets: [{
      data: Object.values(map),
      backgroundColor: ['#4C9AFF', '#36B37E', '#FFAB00']
    }]
  }
})

/* 3️⃣ Companies */
const companiesChartData = computed(() => {
  const map = {}
  filteredInternships.value.forEach(i => {
    const name = i.company?.name
    if (!name) return
    map[name] = (map[name] || 0) + 1
  })

  return {
    labels: Object.keys(map),
    datasets: [{
      label: 'Počet praxí',
      data: Object.values(map),
      backgroundColor: '#6554C0'
    }]
  }
})

/* ------------------ OPTIONS ------------------ */
const baseOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false } }
}

const horizontalOptions = {
  indexAxis: 'y',
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false } }
}
</script>
