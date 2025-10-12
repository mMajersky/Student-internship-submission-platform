<!-- src/App.vue -->
<template>
  <div id="app-wrapper">
    <header class="p-3 mb-3 border-bottom shadow-sm">
      <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
          <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none">
            <svg class="bi me-2" width="24" height="24" role="img" aria-label="Bootstrap">
              <use xlink:href="#bootstrap"></use>
            </svg>
            <span class="fs-5">Odborná prax</span>
          </a>

          <div class="text-end">
            <button type="button" class="btn btn-outline-secondary">Späť</button>
          </div>
        </div>
      </div>
    </header>

    <main>
      <router-view />
    </main>
  </div>
</template>

<style>
#app-wrapper {
  background-color: #f8f9fa;
  min-height: 100vh;
}
</style>
