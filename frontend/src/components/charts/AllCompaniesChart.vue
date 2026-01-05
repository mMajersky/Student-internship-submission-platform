<script setup>
import { ref, onMounted } from 'vue'
import { Bar } from 'vue-chartjs'
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

onMounted(async () => {
  const res = await fetch('/api/stats/all-companies')
  const data = await res.json()

  labels.value = data.map(i => i.company_name)
  values.value = data.map(i => i.count)
})

const options = {
  responsive: true,
  indexAxis: 'y', // HORIZONTAL BAR CHART
  maintainAspectRatio: false,
  plugins: { legend: { display: false } },
  scales: {
    x: { beginAtZero: true }
  }
}
</script>

<template>
  <div style="height: 500px;">
    <Bar :data="{
      labels,
      datasets: [
        {
          label: 'Počet praxí',
          data: values,
          backgroundColor: '#4C9AFF'
        }
      ]
    }" :options="options" />
  </div>
</template>
