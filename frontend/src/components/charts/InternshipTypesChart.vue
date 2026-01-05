<script setup>
import { ref, onMounted } from 'vue'
import { Pie } from 'vue-chartjs'

import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, ArcElement)

const labels = ref([])
const values = ref([])

onMounted(async () => {
  const res = await fetch('/api/stats/internship-types')
  const data = await res.json()

  labels.value = data.map(i => i.type)
  values.value = data.map(i => i.count)
})

const options = {
  responsive: true
}
</script>

<template>
  <Pie
    :data="{
      labels,
      datasets: [
        {
          label: 'Typy praxí',
          data: values,
          backgroundColor: ['#4bc0c0', '#ff9f40', '#36a2eb']
        }
      ]
    }"
    :options="options"
  />
</template>
