<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>

<template>
  <footer class="bg-light border-top py-4 mt-5">
    <div class="container px-4">
      <div class="row align-items-center">
        <div class="col-md-6">
          <p class="text-muted mb-0">{{ $t('footer.copyright') }}</p>
        </div>
        <div class="col-md-6 text-md-end">
          <div class="d-flex flex-wrap justify-content-md-end gap-3">
            <router-link to="/login" class="text-decoration-none text-muted">
              {{ $t('footer.login') }}
            </router-link>
            <a href="#" class="text-decoration-none text-muted">
              {{ $t('footer.registerStudent') }}
            </a>
            <a href="#" class="text-decoration-none text-muted">
              {{ $t('footer.registerCompany') }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>
