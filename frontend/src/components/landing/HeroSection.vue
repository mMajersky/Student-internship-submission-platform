<script setup>
// Hero section component
</script>

<template>
  <section class="bg-light py-5">
    <div class="container px-4">
      <div class="row align-items-center">
        <!-- Left column - Text content -->
        <div class="col-lg-6 mb-4 mb-lg-0">
          <h1 class="display-4 fw-bold text-dark mb-4">
            Evidencia a správa odbornej praxe
          </h1>
          <p class="lead text-muted mb-4">
            Lorem ipsum dolor sit amet consectetur. Blandit felis sed feugiat sapien imperdiet
            quam suspendisse enim nec. Lectus tristique massa habitasse at praesent massa felis.
            Diam pulvinar magna et lorem a lectus.
          </p>

          <div class="d-flex flex-column flex-sm-row gap-3 mb-4">
            <button class="btn btn-primary btn-lg d-flex align-items-center justify-content-center">
              <i class="bi bi-person-plus me-2"></i>
              Registrovať študenta
            </button>

            <button class="btn btn-outline-primary btn-lg d-flex align-items-center justify-content-center">
              <i class="bi bi-building me-2"></i>
              Registrovať firmu
            </button>
          </div>

          <p class="text-muted small">
            Alebo sa
            <router-link to="/login" class="text-primary text-decoration-none fw-medium">
              prihláste
            </router-link>
            do existujúceho účtu.
          </p>
        </div>

        <!-- Right column - Image -->
        <div class="col-lg-6">
          <div class="ratio ratio-1x1">
            <img
              src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=800&q=80"
              alt="Študenti pri práci"
              class="img-fluid rounded shadow"
            />
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

