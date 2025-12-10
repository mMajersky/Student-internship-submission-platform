<template>
  <section class="py-3">
    <h4 class="mb-4">Štatistiky praxí</h4>

    <div class="row g-4">

      <div class="col-md-6 col-lg-4">
        <h6 class="text-center mb-2">Počet študentov podľa ročníka</h6>
        <StudentsTrendChart />
      </div>

      <div class="col-md-6 col-lg-4">
        <h6 class="text-center mb-2">Typy praxí</h6>
        <InternshipTypesChart />
      </div>

      <div class="col-12 mb-4">
        <h5 class="text-center">Praxe podľa firmy (všetky firmy)</h5>
        <AllCompaniesChart />
      </div>


    </div>
  </section>
</template>

<script setup>
import StudentsTrendChart from '@/components/charts/StudentsTrendChart.vue'
import InternshipTypesChart from '@/components/charts/InternshipTypesChart.vue'
import AllCompaniesChart from '@/components/charts/AllCompaniesChart.vue'
</script>
