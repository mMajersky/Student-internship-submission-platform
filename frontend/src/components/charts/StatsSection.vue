<script setup>
import { useI18n } from 'vue-i18n'
import StudentsTrendChart from './StudentsTrendChart.vue'
import InternshipTypesChart from './InternshipTypesChart.vue'
import TopCompaniesChart from './TopCompaniesChart.vue'

const { t } = useI18n()
</script>

<template>
  <section class="py-5 bg-white">
    <div class="container">
      <div class="row g-4 align-items-stretch">

        <!-- Trend študentov -->
        <div class="col-md-6 col-lg-4 d-flex flex-column">
          <h5 class="text-center mb-3">{{ t('landing.studentsByYear') }}</h5>
          <div class="flex-grow-1 d-flex align-items-center">
            <div class="w-100 pt-4">
              <StudentsTrendChart />
            </div>
          </div>
        </div>

        <!-- Typy praxí -->
        <div class="col-md-6 col-lg-4 d-flex flex-column">
          <h5 class="text-center mb-3">{{ t('landing.internshipTypesTitle') }}</h5>
          <div class="flex-grow-1 d-flex align-items-center">
            <div class="w-100">
              <InternshipTypesChart />
            </div>
          </div>
        </div>

        <!-- Top firmy -->
        <div class="col-md-6 col-lg-4 d-flex flex-column">
          <h5 class="text-center mb-3">{{ t('landing.topEmployers') }}</h5>
          <div class="flex-grow-1 d-flex align-items-center">
            <div class="w-100 pt-4">
              <TopCompaniesChart />
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>
</template>


