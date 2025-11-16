<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>

<template>
  <section class="py-5 bg-white">
    <div class="container px-4">
      <div class="row g-4">
        <!-- Pre študentov -->
        <div class="col-md-4">
          <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-4">
              <div class="d-flex align-items-center mb-3">
                <i class="bi bi-mortarboard-fill text-primary fs-4 me-3"></i>
                <h5 class="card-title mb-0">{{ $t('infoCards.forStudents') }}</h5>
              </div>
              <p class="card-text text-muted">
                {{ $t('infoCards.forStudentsDesc') }}
              </p>
              <button class="btn btn-outline-primary">
                <i class="bi bi-play-circle me-2"></i>
                {{ $t('infoCards.start') }}
              </button>
            </div>
          </div>
        </div>

        <!-- Pre firmy -->
        <div class="col-md-4">
          <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-4">
              <div class="d-flex align-items-center mb-3">
                <i class="bi bi-building text-primary fs-4 me-3"></i>
                <h5 class="card-title mb-0">{{ $t('infoCards.forCompanies') }}</h5>
              </div>
              <p class="card-text text-muted">
                {{ $t('infoCards.forCompaniesDesc') }}
              </p>
              <button class="btn btn-outline-primary">
                <i class="bi bi-building-add me-2"></i>
                {{ $t('infoCards.registerCompany') }}
              </button>
            </div>
          </div>
        </div>

        <!-- Pre garanta praxe -->
        <div class="col-md-4">
          <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-4">
              <div class="d-flex align-items-center mb-3">
                <i class="bi bi-people-fill text-primary fs-4 me-3"></i>
                <h5 class="card-title mb-0">{{ $t('infoCards.forGarants') }}</h5>
              </div>
              <p class="card-text text-muted">
                {{ $t('infoCards.forGarantsDesc') }}
              </p>
              <button class="btn btn-outline-primary">
                <i class="bi bi-box-arrow-in-right me-2"></i>
                {{ $t('infoCards.login') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
