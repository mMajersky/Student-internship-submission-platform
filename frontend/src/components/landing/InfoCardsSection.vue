<script setup>
import InfoCard from './InfoCard.vue'

const cards = [
  {
    id: 1,
    title: 'Pre študentov',
    description:
      'Vytvorte novú prax, vyberte firmu cez fulltext, doplňte termíny a sledujte stavy. Nahrajte zmluvu a vývaz.',
    actionText: 'Začať',
    iconColor: 'blue',
    buttonVariant: 'primary',
    iconType: 'student',
  },
  {
    id: 2,
    title: 'Pre firmy',
    description:
      'Potvrďte alebo zamietnite praxe v stave „Vytvorená". Spravujte dokumenty a komunikáciu.',
    actionText: 'Registrovať firmu',
    iconColor: 'purple',
    buttonVariant: 'outline',
    iconType: 'company',
  },
  {
    id: 3,
    title: 'Pre garanta praxe',
    description:
      'Filtrujte, upravujte atribúty praxi a exportujte dáta do CSV pre reporty a štatistiky.',
    actionText: 'Prihlásiť sa',
    iconColor: 'pink',
    buttonVariant: 'outline',
    iconType: 'admin',
  },
]

const handleCardAction = (cardTitle) => {
  console.log(`Action clicked for: ${cardTitle}`)
  // TODO: Implement navigation or action logic
}
</script>

<template>
  <section class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
      <div class="grid md:grid-cols-3 gap-8">
        <InfoCard
          v-for="card in cards"
          :key="card.id"
          :title="card.title"
          :description="card.description"
          :action-text="card.actionText"
          :icon-color="card.iconColor"
          :button-variant="card.buttonVariant"
          @action="handleCardAction(card.title)"
        >
          <template #icon="{ iconClass }">
            <!-- Student icon -->
            <svg
              v-if="card.iconType === 'student'"
              :class="['w-6 h-6', iconClass]"
              fill="currentColor"
              viewBox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"
              />
            </svg>
            <!-- Company icon -->
            <svg
              v-else-if="card.iconType === 'company'"
              :class="['w-6 h-6', iconClass]"
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
            <!-- Admin/Guarantor icon -->
            <svg
              v-else-if="card.iconType === 'admin'"
              :class="['w-6 h-6', iconClass]"
              fill="currentColor"
              viewBox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"
              />
            </svg>
          </template>
        </InfoCard>
      </div>
    </div>
  </section>
</template>

