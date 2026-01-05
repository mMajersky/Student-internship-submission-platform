<script setup>
import StudentsTrendChart from './StudentsTrendChart.vue'
import InternshipTypesChart from './InternshipTypesChart.vue'
import TopCompaniesChart from './TopCompaniesChart.vue'
</script>

<template>
  <section class="py-5 bg-white">
    <div class="container">

      <div class="row g-4">

        <!-- Trend študentov -->
        <div class="col-md-6 col-lg-4">
          <h5 class="text-center mb-2">Počet študentov podľa ročníka</h5>
          <StudentsTrendChart />
        </div>

        <!-- Typy praxí -->
        <div class="col-md-6 col-lg-4">
          <h5 class="text-center mb-2">Typy praxí</h5>
          <InternshipTypesChart />
        </div>

        <!-- Top firmy -->
        <div class="col-md-6 col-lg-4">
          <h5 class="text-center mb-2">Top zamestnávatelia</h5>
          <TopCompaniesChart />
        </div>

      </div>
    </div>
  </section>
</template>
