<script setup>
import { ref, onMounted } from 'vue'
import { Line } from 'vue-chartjs'
import { baseOptions } from '@/components/charts/chartOptions'

import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale
} from 'chart.js'

ChartJS.register(
  Title, Tooltip, Legend,
  LineElement, PointElement,
  CategoryScale, LinearScale
)

const labels = ref([])
const values = ref([])

onMounted(async () => {
  try {
    const res = await fetch('/api/stats/students-trend')
    if (!res.ok) throw new Error('Failed to fetch data')
    const data = await res.json()

    labels.value = data.map(i => i.academy_year)
    values.value = data.map(i => i.count)
  } catch (error) {
    console.error('Error fetching students trend:', error)
    // Optionally set some default data or leave empty
  }
})

const options = {
  responsive: true,
  plugins: {
    legend: { display: false }
  }
}
</script>

<template>
  <Line
    :data="{
      labels,
      datasets: [
        {
          label: 'Počet študentov',
          data: values,
          borderColor: '#4C9AFF',
          backgroundColor: 'rgba(76, 154, 255, 0.3)',
          tension: 0.3
        }
      ]
    }"
    :options="options"
  />
</template>
