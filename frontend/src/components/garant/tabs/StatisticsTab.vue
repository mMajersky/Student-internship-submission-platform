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
      :show-study-field="false"
      :show-student="false"
      v-model:selected-years="selectedYears"
      v-model:selected-companies="selectedCompanies"
      id-prefix="stats-"
    />


    <!-- CHARTS -->
    <div class="row g-4 mt-3">

<div class="col-12">
  <div class="card p-3">
    <div class="row align-items-stretch">
      <!-- YEAR TREND -->
      <div class="col-md-6 col-lg-4">
        <h6 class="text-center mb-2">
          Počet praxí podľa akademického roka
        </h6>
        <div style="height:260px">
          <Line
            :data="yearChartData"
            :options="baseOptions"
          />
        </div>
      </div>

      <!-- TYPES -->
      <div class="col-md-6 col-lg-4">
        <h6 class="text-center mb-2">
          Typy praxí
        </h6>
        <div style="height:260px">
          <Doughnut
            v-if="typesChartData.labels.length > 0"
            :data="typesChartData"
            :options="pieChartOptions"
          />
          <div
            v-else
            class="d-flex align-items-center justify-content-center h-100 text-muted"
          >
            Žiadne dáta
          </div>
        </div>
      </div>

      <!-- KPI COLUMN -->
      <div class="col-md-12 col-lg-4 d-flex flex-column justify-content-center">
        <div class="stat-box mb-3 text-center">
          <div class="stat-number text-waiting-garant">
            {{ statusCounts.waitingGarant }}
          </div>
          <div class="stat-label">
            Čaká sa na garanta
          </div>
        </div>

        <div class="stat-box mb-3 text-center">
          <div class="stat-number text-warning">
            {{ runningCount }}
          </div>
          <div class="stat-label">
            Prebiehajúce praxe
          </div>
        </div>

        <div class="stat-box text-center">
          <div class="stat-number text-success">
            {{ finishedCount }}
          </div>
          <div class="stat-label">
            Ukončené praxe
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <!-- Rozhodnutie firmy -->
  <div class="col-md-6">
    <div class="card p-3 mb-4">
      <!-- COUNT -->
      <div class="stat-box mb-3 text-center">
        <div class="stat-number text-waiting-garant">
          {{ statusCounts.waitingCompany }}
        </div>
        <div class="stat-label">
          Čaká sa na firmu
        </div>
      </div>

      <!-- GRAF -->
      <h6 class="text-center mb-2">
        Rozhodnutie firmy
      </h6>

      <div class="chart-box" style="height: 300px;">
        <Bar
          :data="companyDecisionChartData"
          :options="barChartOptions"
        />
      </div>
    </div>
  </div>

  <!-- Výsledok praxe (študent) -->
  <div class="col-md-6">
    <div class="card p-3 mb-4">
      <!-- COUNT -->
      <div class="stat-box mb-3 text-center">
        <div class="stat-number text-waiting-garant">
          {{ statusCounts.waitingDefense }}
        </div>
        <div class="stat-label">
          Čaká sa na študenta
        </div>
      </div>

      <!-- GRAF -->
      <h6 class="text-center mb-2">
        Výsledok praxe (študent)
      </h6>

      <div class="chart-box" style="height: 300px;">
        <Bar
          :data="studentOutcomeChartData"
          :options="barChartOptions"
        />
      </div>
    </div>
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
  LinearScale,
  DoughnutController
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
  LinearScale,
  DoughnutController,
  
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

/* ------------------ FILTER OPTIONS ------------------ */
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

/* 1️⃣ Internships per academic year */
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

/* 2️⃣ Internship practice types (DOUGHNUT) */
const TYPE_LABELS = {
  paid: 'Platená prax',
  unpaid: 'Neplatená prax',
  school_project: 'Školský projekt'
}

const typesChartData = computed(() => {
  const map = {}

  filteredInternships.value.forEach(i => {
    if (!i.type) return
    const label = TYPE_LABELS[i.type] ?? i.type
    map[i.type] = (map[i.type] || 0) + 1
  })

  return {
    labels: Object.keys(map),
    datasets: [{
      data: Object.values(map),
      backgroundColor: [
        '#36B37E',
        '#4C9AFF',
        '#FFAB00'
      ]
    }]
  }
})

/* 3️⃣ Internships per company */
const companiesChartData = computed(() => {
  const map = {}

  filteredInternships.value.forEach(i => {
    const name = i.company?.name
    if (!name) return
    map[name] = (map[name] || 0) + 1
  })

  // 🔥 SORT AFTER FILTERING
  const sortedEntries = Object.entries(map)
    .sort((a, b) => b[1] - a[1]) // DESC by count

  return {
    labels: sortedEntries.map(([name]) => name),
    datasets: [{
      label: 'Počet praxí',
      data: sortedEntries.map(([, count]) => count),
      backgroundColor: '#6554C0'
    }]
  }
})



/* Company decision: confirmed vs not confirmed */
const companyDecisionChartData = computed(() => {
  const confirmedStatuses = [
    'confirmed by company',
    'defended by student',
    'not defended by student'
  ]

  const notConfirmedStatuses = [
    'not confirmed by company'
  ]

  let confirmed = 0
  let notConfirmed = 0

  filteredInternships.value.forEach(i => {
    if (confirmedStatuses.includes(i.status)) {
      confirmed++
    } else if (notConfirmedStatuses.includes(i.status)) {
      notConfirmed++
    }
  })

  return {
    labels: ['Potvrdené firmou', 'Nepotvrdené firmou'],
    datasets: [
      {
        label: 'Počet praxí',
        data: [confirmed, notConfirmed],
        backgroundColor: ['#36B37E', '#FF5630']
      }
    ]
  }
})



/* Student outcome: defended vs not defended */
const studentOutcomeChartData = computed(() => {
  let defended = 0
  let notDefended = 0

  filteredInternships.value.forEach(i => {
    if (i.status === 'defended by student') defended++
    if (i.status === 'not defended by student') notDefended++
  })

  return {
    labels: ['Obhájené', 'Neobhájené'],
    datasets: [{
      label: 'Počet praxí',
      data: [defended, notDefended],
      backgroundColor: ['#36B37E', '#FF5630']
    }]
  }
})

const finishedCount = computed(() => {
  return filteredInternships.value.filter(i =>
    i.status === 'defended by student' ||
    i.status === 'not defended by student'
  ).length
})

const runningCount = computed(() => {
  return filteredInternships.value.length - finishedCount.value
})


const fetchSummary = async () => {
  const res = await fetch('/api/stats/internship-summary')
  const data = await res.json()

  runningCount.value = data.running
  finishedCount.value = data.finished
}

onMounted(() => {
  fetchSummary()
})

const statusCounts = computed(() => {
  let waitingGarant = 0
  let waitingCompany = 0
  let waitingDefense = 0

  let defended = 0
  let notDefended = 0

  let rejected = 0

  filteredInternships.value.forEach(i => {
    switch (i.status) {
      case 'created':
        waitingGarant++
        break

      case 'approved by garant':
        waitingCompany++
        break

      case 'approved by company':
        waitingDefense++
        break

      case 'defended by student':
        defended++
        break

      case 'not defended by student':
        notDefended++
        break

      case 'rejected by garant':
      case 'not confirmed by company':
        rejected++
        break
    }
  })

  return {
    // čakacie stavy
    waitingGarant,
    waitingCompany,
    waitingDefense,

    // výsledok
    defended,
    notDefended,

    // agregácie
    finished: defended + notDefended,
    active: waitingGarant + waitingCompany + waitingDefense,
    rejected
  }
})


/* ------------------ CHART OPTIONS ------------------ */

const integerAxis = {
  beginAtZero: true,
  ticks: {
    stepSize: 1,
    precision: 0,
    callback: (value) => Number.isInteger(value) ? value : null
  }
}

const baseOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false }
  }
}

const horizontalOptions = {
  indexAxis: 'y',
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false }
  }
}

console.log(internships.value[0])


const barChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false }
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: { precision: 0 }
    }
  }
}

const pieChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    datalabels: {
      display: false // ❗ vypneme čísla v pie
    },
    legend: {
      position: 'right',
      labels: {
        usePointStyle: true,
        padding: 20,
        font: {
          size: 12
        },
        generateLabels(chart) {
          const data = chart.data
          return data.labels.map((label, i) => {
            const value = data.datasets[0].data[i]
            return {
              text: `${label}: ${value}`,
              fillStyle: data.datasets[0].backgroundColor[i],
              strokeStyle: data.datasets[0].backgroundColor[i],
              index: i
            }
          })
        }
      }
    }
  }
}





</script>

<style>
  .text-waiting-garant {
  color: #2684FF; /* blue */
}

.text-waiting-company {
  color: #DE350B; /* red */
}

.text-waiting-defense {
  color: #FFAB00; /* yellow */
}

.text-finished {
  color: #36B37E; /* green */
}

.text-rejected {
  color: #6B778C; /* gray */
}

</style>

