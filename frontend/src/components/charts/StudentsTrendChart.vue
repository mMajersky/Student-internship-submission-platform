<script setup>
import { ref, onMounted } from 'vue'
import { Line } from 'vue-chartjs'

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
  const res = await fetch('/api/stats/students-trend')
  const data = await res.json()

  labels.value = data.map(i => i.academy_year)
  values.value = data.map(i => i.count)
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
