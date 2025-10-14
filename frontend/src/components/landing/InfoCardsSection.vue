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
                <h5 class="card-title mb-0">Pre študentov</h5>
              </div>
              <p class="card-text text-muted">
                Vytvorte novú prax, vyberte firmu, doplňte termíny a sledujte stav. Nahrajte zmluvu a výkaz.
              </p>
              <button class="btn btn-outline-primary">
                <i class="bi bi-play-circle me-2"></i>
                Začať
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
                <h5 class="card-title mb-0">Pre firmy</h5>
              </div>
              <p class="card-text text-muted">
                Potvrďte alebo zamietnite praxe v stave „Vytvorená". Spravujte dokumenty a komunikáciu.
              </p>
              <button class="btn btn-outline-primary">
                <i class="bi bi-building-add me-2"></i>
                Registrovať firmu
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
                <h5 class="card-title mb-0">Pre garanta praxe</h5>
              </div>
              <p class="card-text text-muted">
                Filtrujte, upravujte atribúty praxí a exportujte dáta do CSV pre reporty a štatistiky.
              </p>
              <button class="btn btn-outline-primary">
                <i class="bi bi-box-arrow-in-right me-2"></i>
                Prihlásiť sa
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
// No additional logic needed for this static component
</script>

<style scoped>
.card {
  transition: transform 0.2s ease-in-out;
}

.card:hover {
  transform: translateY(-2px);
}

.btn {
  transition: all 0.2s ease-in-out;
}
</style>
