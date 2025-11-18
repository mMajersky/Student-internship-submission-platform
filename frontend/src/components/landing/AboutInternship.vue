<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>

<template>
  <section class="py-5 bg-white">
    <div class="container px-4">
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
          <h2 class="display-6 fw-bold text-dark mb-4">{{ $t('aboutInternship.title') }}</h2>
          <p class="text-muted">
            {{ $t('aboutInternship.description') }}
          </p>
        </div>
      </div>
    </div>
  </section>
</template>
