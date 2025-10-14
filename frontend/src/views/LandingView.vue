<script setup>
import NotificationBar from '@/components/landing/NotificationBar.vue'
import TheHeader from '@/components/landing/TheHeader.vue'
import HeroSection from '@/components/landing/HeroSection.vue'
import AboutSection from '@/components/landing/AboutSection.vue'
import InfoCardsSection from '@/components/landing/InfoCardsSection.vue'
import TheFooter from '@/components/landing/TheFooter.vue'
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <TheHeader />
    <NotificationBar />
    <main class="flex-grow">
      <HeroSection />
      <AboutSection />
      <InfoCardsSection />
    </main>
    <TheFooter />
  </div>
</template>

