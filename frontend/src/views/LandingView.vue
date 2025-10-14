<script setup>
import NotificationBar from '@/components/landing/NotificationBar.vue'
import InfoCardsSection from '@/components/landing/InfoCardsSection.vue'
import TheFooter from '@/components/landing/TheFooter.vue'
</script>

<template>
  <div>
    <NotificationBar />
    <section class="bg-light py-5">
      <div class="container px-4">
        <div class="row align-items-center">
          <div class="col-lg-6 mb-4 mb-lg-0">
            <h1 class="display-4 fw-bold text-dark mb-4">Evidencia a správa odbornej praxe</h1>
            <p class="lead text-muted mb-4">
              Lorem ipsum dolor sit amet consectetur. Blandit felis sed feugiat sapien imperdiet quam suspendisse enim nec.
            </p>
            <div class="d-flex flex-column flex-sm-row gap-3 mb-4">
              <button class="btn btn-primary btn-lg d-flex align-items-center justify-content-center">
                <i class="bi bi-person-plus me-2"></i> Registrovať študenta
              </button>
              <button class="btn btn-outline-primary btn-lg d-flex align-items-center justify-content-center">
                <i class="bi bi-building me-2"></i> Registrovať firmu
              </button>
            </div>
            <p class="text-muted small">
              Alebo sa <router-link to="/login" class="text-primary text-decoration-none fw-medium">prihláste</router-link> do existujúceho účtu.
            </p>
          </div>
          <div class="col-lg-6">
            <img src="/images/landing-page-art.png" alt="Office práca" class="img-fluid rounded shadow" width="786" height="524" />
          </div>
        </div>
      </div>
    </section>

    <section class="py-5 bg-white">
      <div class="container px-4">
        <div class="row justify-content-center">
          <div class="col-lg-8 text-center">
            <h2 class="display-6 fw-bold text-dark mb-4">Čo je to odborná prax?</h2>
            <p class="text-muted">
              Lorem ipsum dolor sit amet consectetur. Gravida neque lobortis fermentum neque amet. Volutpat augue gravida vel.
            </p>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Info Cards Section -->
    <InfoCardsSection />
    
    <!-- Footer -->
    <TheFooter />
  </div>
</template>


