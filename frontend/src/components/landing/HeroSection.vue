<script setup>
// Hero section component
</script>

<template>
  <section class="bg-gradient-to-b from-blue-50 to-white py-20">
    <div class="container mx-auto px-4">
      <div class="grid md:grid-cols-2 gap-12 items-center">
        <!-- Left column - Text content -->
        <div class="space-y-6">
          <h1 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight">
            Evidencia a správa odbornej praxe
          </h1>
          <p class="text-lg text-gray-600 leading-relaxed">
            Lorem ipsum dolor sit amet consectetur. Blandit felis sed feugiat sapien imperdiet
            quam suspendisse enim nec. Lectus tristique massa habitasse at praesent massa felis.
            Diam pulvinar magna et lorem a lectus.
          </p>

          <div class="flex flex-col sm:flex-row gap-4 pt-4">
            <button
              class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-lg transition-colors flex items-center justify-center space-x-2"
            >
              <svg
                class="w-5 h-5"
                fill="currentColor"
                viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"
                />
              </svg>
              <span>Registrovať študenta</span>
            </button>

            <button
              class="bg-white hover:bg-gray-50 text-blue-600 border-2 border-blue-600 font-semibold px-8 py-3 rounded-lg transition-colors flex items-center justify-center space-x-2"
            >
              <svg
                class="w-5 h-5"
                fill="currentColor"
                viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  fill-rule="evenodd"
                  d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z"
                  clip-rule="evenodd"
                />
              </svg>
              <span>Registrovať firmu</span>
            </button>
          </div>

          <p class="text-sm text-gray-500">
            Alebo sa
            <a href="#" class="text-blue-600 hover:text-blue-700 underline font-medium"
              >prihláste</a
            >
            do existujúceho účtu.
          </p>
        </div>

        <!-- Right column - Image -->
        <div class="relative">
          <div class="aspect-square rounded-lg bg-gray-200 overflow-hidden">
            <img
              src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=800&q=80"
              alt="Študenti pri práci"
              class="w-full h-full object-cover"
            />
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

