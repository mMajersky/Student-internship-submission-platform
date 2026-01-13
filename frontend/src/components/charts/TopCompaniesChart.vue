<script setup>
import { ref, onMounted, computed } from 'vue'
import { Bar } from 'vue-chartjs'
import { baseOptions } from '@/components/charts/chartOptions'

import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const labels = ref([])
const values = ref([])

function shortenLabel(name, max = 18) {
  return name.length > max ? name.substring(0, max) + '…' : name
}

const formattedLabels = computed(() =>
  labels.value.map(name => shortenLabel(name))
)

onMounted(async () => {
  try {
    const res = await fetch('/api/stats/top-companies')
    if (!res.ok) throw new Error('Failed to fetch data')
    const data = await res.json()

    labels.value = data.map(i => i.company_name)
    values.value = data.map(i => i.count)
  } catch (error) {
    console.error('Error fetching top companies:', error)
    // Optionally set some default data or leave empty
  }
})

const options = {
  responsive: true,
  indexAxis: 'y',
  scales: {
    y: {
      ticks: {
        font: { size: 12 }
      }
    }
  },
  plugins: {
    legend: { display: false }
  }
}
</script>

<template>
  <Bar
    :data="{
      labels: formattedLabels,
      datasets: [
        {
          label: 'Počet praxí',
          data: values,
          backgroundColor: '#4C9AFF',
          borderRadius: 6
        }
      ]
    }"
    :options="options"
  />
</template>
