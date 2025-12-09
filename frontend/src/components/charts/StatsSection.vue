<script setup>
import StudentsTrendChart from './StudentsTrendChart.vue'
import InternshipTypesChart from './InternshipTypesChart.vue'
import TopCompaniesChart from './TopCompaniesChart.vue'
</script>

<template>
  <section class="py-5 bg-white">
    <div class="container">
      <h2 class="text-center mb-4">Štatistiky praxí</h2>

      <div class="row g-4">
        <div class="col-md-6 col-lg-4">
          <StudentsTrendChart />
        </div>
        <div class="col-md-6 col-lg-4">
          <InternshipTypesChart />
        </div>
        <div class="col-md-6 col-lg-4">
          <TopCompaniesChart />
        </div>
      </div>
    </div>
  </section>
</template>
