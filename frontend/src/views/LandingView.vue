<script setup>
import { useI18n } from 'vue-i18n'
import NotificationBar from '@/components/landing/NotificationBar.vue'
import InfoCardsSection from '@/components/landing/InfoCardsSection.vue'
import TheFooter from '@/components/landing/TheFooter.vue'
import AboutInternship from '@/components/landing/AboutInternship.vue'
import StatsSection from '@/components/charts/StatsSection.vue'

const { t } = useI18n()
</script>

<template>
  <div>
    <NotificationBar />

    <section class="bg-light py-5">
      <div class="container px-4">
        <div class="row align-items-center">
          <div class="col-lg-6 mb-4 mb-lg-0">
            <h1 class="display-4 fw-bold text-dark mb-4">{{ $t('landing.title') }}</h1>
            <p class="lead text-muted mb-4">
              {{ $t('landing.subtitle') }}
            </p>

            <div class="d-flex flex-column flex-sm-row gap-3 mb-4">
              <router-link
                to="/register"
                class="btn btn-primary btn-lg d-flex align-items-center justify-content-center"
              >
                <i class="bi bi-person-plus me-2"></i> {{ $t('landing.registerStudent') }}
              </router-link>

              <router-link
                to="/register-company"
                class="btn btn-outline-primary btn-lg d-flex align-items-center justify-content-center"
              >
                <i class="bi bi-building me-2"></i> {{ $t('landing.registerCompany') }}
              </router-link>
            </div>

            <p class="text-muted small">
              {{ $t('landing.loginLink') }}
              <router-link to="/login" class="text-primary fw-semibold">{{ $t('landing.login') }}</router-link>
              {{ $t('landing.loginLinkEnd') }}
            </p>
          </div>

          <div class="col-lg-6">
            <img
              src="/images/landing-page-art.png"
              :alt="$t('landing.officeWork')"
              class="img-fluid rounded shadow"
              width="786"
              height="524"
            />
          </div>
        </div>
      </div>
    </section>

    <!-- NEW SECTION: stats BELOW main hero -->
    <AboutInternship />
    <section class="py-5">
      <div class="container px-4">
        
        <StatsSection />
      </div>
    </section>

    
    <!-- <InfoCardsSection /> -->
    <TheFooter />
  </div>
</template>

