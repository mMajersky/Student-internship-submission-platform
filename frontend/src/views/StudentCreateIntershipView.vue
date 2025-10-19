<template>
  <div class="container">
    <main class="main">
      <h1 class="title">Nová prax</h1>

      <form @submit.prevent="handleSubmit" class="form">
        <!-- ZMENA: Namiesto textového poľa je tu výber firmy -->
        <div class="form-group">
          <label for="company" class="label">Firma<span class="required">*</span></label>
          <select id="company" v-model="formData.company_id" class="input" required>
            <option disabled value="">Vyberte firmu zo zoznamu</option>
            <option v-for="company in companies" :key="company.id" :value="company.id">
              {{ company.name }}
            </option>
          </select>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="rok" class="label">Akademický rok<span class="required">*</span></label>
            <!-- ZMENA: Input je teraz text, aby sme mohli vložiť formát RRRR/RRRR -->
            <input id="rok" v-model="formData.academy_year" type="text" placeholder="napr. 2025/2026" class="input" required />
          </div>

          <div class="form-group">
            <label for="datumZaciatku" class="label">Dátum začiatku<span class="required">*</span></label>
            <input id="datumZaciatku" v-model="formData.start_date" type="date" class="input" required />
          </div>

          <div class="form-group">
            <label for="datumKonca" class="label">Dátum konca<span class="required">*</span></label>
            <input id="datumKonca" v-model="formData.end_date" type="date" class="input" required />
          </div>
        </div>

        <div class="form-actions">
          <button type="button" @click="handleCancel" class="btn btn-secondary">Zrušiť</button>
          <button type="submit" class="btn btn-primary">Vytvoriť prax</button>
        </div>

        <div class="info-box">
          Po vytvorení praxe systém vygeneruje PDF „Dohoda o odbornej praxi“. Stav bude <strong>Vytvorená</strong>.
        </div>
      </form>
    </main>

    <footer class="footer">
      © 2025 Odborná prax CRM
    </footer>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const formData = reactive({
  company_id: '',
  academy_year: `${new Date().getFullYear()}/${new Date().getFullYear() + 1}`,
  start_date: '',
  end_date: ''
});

const companies = ref([]);

const loadCompanies = async () => {
  const token = authStore.token;
  if (!token) return;

  try {
    const response = await fetch('http://localhost:8000/api/companies', {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    if (!response.ok) throw new Error('Nepodarilo sa načítať firmy.');
    
    const data = await response.json();
    companies.value = data.data; 
  } catch (error) {
    console.error(error);
    alert(error.message);
  }
};

onMounted(() => {
  loadCompanies();
});

const handleSubmit = async () => {
  const token = authStore.token;
  if (!token) {
    alert('Chyba: Neboli nájdené prihlasovacie údaje.');
    router.push('/login');
    return;
  }

  // Bezpečnostná kontrola, či má používateľ priradený študentský profil
  if (!authStore.user || !authStore.user.student) {
      alert('Chyba: Vášmu používateľskému účtu nebol priradený študentský profil. Kontaktujte administrátora.');
      return;
  }

  // --- OPRÁVNENÝ PAYLOAD ---
  const payload = {
    ...formData,
    // ZMENA 1: Používame ID zo študentského profilu (ktorý nám teraz posiela backend)
    student_id: authStore.user.student.id,
    // ZMENA 2: Pridávame správny status S DIAKRITIKOU
    status: 'vytvorená'
  };

  try {
    const response = await fetch('http://localhost:8000/api/internships', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify(payload) 
    });

    const data = await response.json();

    if (!response.ok) {
      if (response.status === 422) {
          const errors = Object.values(data.errors).flat().join('\n');
          throw new Error(`Chyba validácie:\n${errors}`);
      }
      throw new Error(data.message || 'Nepodarilo sa vytvoriť prax.');
    }

    alert(data.message || 'Prax bola úspešne vytvorená!');
    router.push('/internships');

  } catch (error) {
    console.error('Chyba:', error);
    alert(error.message);
  }
};

const handleCancel = () => {
  router.push('/internships');
};
</script>

<style scoped>
/* Všetky štýly zostávajú rovnaké */
* {
   margin: 0;
   padding: 0;
   box-sizing: border-box;
}

.container {
   min-height: 100vh;
   display: flex;
   flex-direction: column;
   font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto,
'Helvetica Neue', Arial, sans-serif;
   color: #1f2937;
   background-color: #f9fafb;
}

.main {
   flex: 1;
   max-width: 1200px;
   width: 100%;
   margin: 0 auto;
   padding: 2rem;
}

.title {
   font-size: 1.875rem;
   font-weight: 600;
   margin-bottom: 2rem;
   color: #1f2937;
}

.form {
   background: white;
   border-radius: 0.5rem;
   border: 1px solid #e5e7eb;
   padding: 2rem;
}

.form-group {
   margin-bottom: 1.5rem;
   flex: 1;
}

.form-row {
   display: grid;
   grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
   gap: 1rem;
}

.label {
   display: block;
   font-size: 0.875rem;
   font-weight: 500;
   margin-bottom: 0.5rem;
   color: #374151;
}

.required {
   color: #ef4444;
}

.input {
   width: 100%;
   padding: 0.625rem 0.875rem;
   border: 1px solid #d1d5db;
   border-radius: 0.375rem;
   font-size: 0.875rem;
   transition: all 0.2s;
}

.input:focus {
   outline: none;
   border-color: #2563eb;
   box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-actions {
   display: flex;
   gap: 1rem;
   justify-content: flex-end;
   margin-top: 2rem;
}

.btn {
   padding: 0.625rem 1.25rem;
   border-radius: 0.375rem;
   font-size: 0.875rem;
   font-weight: 500;
   cursor: pointer;
   border: none;
   transition: all 0.2s;
}

.btn-secondary {
   background: white;
   color: #374151;
   border: 1px solid #d1d5db;
}

.btn-secondary:hover {
   background: #f9fafb;
}

.btn-primary {
   background: #2563eb;
   color: white;
}

.btn-primary:hover {
   background: #1d4ed8;
}

.info-box {
   margin-top: 1.5rem;
   padding: 1rem;
   background: #f3f4f6;
   border-radius: 0.375rem;
   font-size: 0.875rem;
   color: #4b5563;
}

.footer {
   padding: 1.5rem 2rem;
   text-align: center;
   color: #6b7280;
   font-size: 0.875rem;
   border-top: 1px solid #e5e7eb;
   background: white;
}
</style>