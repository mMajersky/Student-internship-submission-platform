<script setup>
import { ref, onMounted, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { Pie } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from 'chart.js'
import { baseOptions } from '@/components/charts/chartOptions'


ChartJS.register(Title, Tooltip, Legend, ArcElement)

const { t } = useI18n()

// 1) FIXED order of types (these are backend keys)
const TYPE_ORDER = ['paid', 'unpaid', 'school_project']

// 2) Labels are always derived from i18n (auto changes with language)
const labels = computed(() =>
  TYPE_ORDER.map(type => t(`landing.internshipTypes.${type}`))
)

// 3) Values follow the fixed order
const values = ref([0, 0, 0])

onMounted(async () => {
  try {
    const res = await fetch('/api/stats/internship-types')
    if (!res.ok) throw new Error('Failed to fetch data')
    const data = await res.json()

    // data example: [{ type: "paid", count: 10 }, ...]
    const countByType = Object.fromEntries(
      data.map(item => [item.type, item.count])
    )

    values.value = TYPE_ORDER.map(type => countByType[type] ?? 0)
  } catch (error) {
    console.error('Error fetching internship types:', error)
    values.value = [0, 0, 0]
  }
})

const chartData = computed(() => ({
  labels: labels.value,
  datasets: [
    {
      // optional: keep dataset label translated too (or remove it)
      label: t('landing.officeWork'), // or create new key like landing.internshipTypesTitle
      data: values.value,
      backgroundColor: ['#4bc0c0', '#36a2eb', '#ff9f40']
    }
  ]
}))

const options = computed(() => ({
  responsive: true,
  plugins: {
    title: {
      display: true,
      onClick: null
    },
    legend: { display: true, onClick: () => {} }
  }
}))
</script>

<template>
  <Pie :data="chartData" :options="options" />

</template>
