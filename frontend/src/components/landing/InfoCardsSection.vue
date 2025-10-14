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
  <section class="py-5 bg-light">
    <div class="container px-4">
      <div class="row g-4">
        <div class="col-md-4" v-for="card in cards" :key="card.id">
          <InfoCard
            :title="card.title"
            :description="card.description"
            :action-text="card.actionText"
            :icon-color="card.iconColor"
            :button-variant="card.buttonVariant"
            @action="handleCardAction(card.title)"
          >
            <template #icon="{ iconClass }">
              <!-- Student icon -->
              <i v-if="card.iconType === 'student'" :class="['bi bi-mortarboard', iconClass]"></i>
              <!-- Company icon -->
              <i v-else-if="card.iconType === 'company'" :class="['bi bi-building', iconClass]"></i>
              <!-- Admin/Guarantor icon -->
              <i v-else-if="card.iconType === 'admin'" :class="['bi bi-people', iconClass]"></i>
            </template>
          </InfoCard>
        </div>
      </div>
    </div>
  </section>
</template>

