<script setup>
// Footer component
const currentYear = new Date().getFullYear()
</script>

<template>
  <footer class="bg-gray-900 text-gray-300 py-12">
    <div class="container mx-auto px-4">
      <div class="grid md:grid-cols-3 gap-8 mb-8">
        <!-- Company Info -->
        <div class="space-y-4">
          <div class="flex items-center space-x-2">
            <svg
              class="w-8 h-8 text-blue-500"
              fill="currentColor"
              viewBox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"
              />
            </svg>
            <span class="text-xl font-bold text-white">Odborná prax</span>
          </div>
          <p class="text-sm">Evidencia a správa odbornej praxe pre študentov a firmy.</p>
        </div>

        <!-- Links -->
        <div class="space-y-4">
          <h3 class="text-white font-semibold text-lg">Odkazy</h3>
          <ul class="space-y-2 text-sm">
            <li>
              <a href="#" class="hover:text-white transition-colors">Prihlásenie</a>
            </li>
            <li>
              <a href="#" class="hover:text-white transition-colors">Registrácia študenta</a>
            </li>
            <li>
              <a href="#" class="hover:text-white transition-colors">Registrácia firmy</a>
            </li>
          </ul>
        </div>

        <!-- Contact -->
        <div class="space-y-4">
          <h3 class="text-white font-semibold text-lg">Kontakt</h3>
          <ul class="space-y-2 text-sm">
            <li>Email: info@example.com</li>
            <li>Telefón: +421 123 456 789</li>
          </ul>
        </div>
      </div>

      <!-- Copyright -->
      <div class="border-t border-gray-800 pt-8 text-center text-sm">
        <p>&copy; {{ currentYear }} Odborná prax CRM</p>
      </div>
    </div>
  </footer>
</template>

