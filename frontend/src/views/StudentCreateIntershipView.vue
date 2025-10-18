<template>
  <div class="container">
    <main class="main">
      <h1 class="title">Nová prax</h1>

      <form @submit.prevent="handleSubmit" class="form">
        <div class="form-group">
          <label for="firma" class="label">Firma<span class="required">*</span></label>
          <input id="firma" v-model="formData.firma" type="text" placeholder="Zadajte názov firmy" class="input" required />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="rok" class="label">Rok<span class="required">*</span></label>
            <input id="rok" v-model="formData.rok" type="number" class="input" required />
          </div>

          <div class="form-group">
            <label for="semester" class="label">Semester<span class="required">*</span></label>
            <select id="semester" v-model="formData.semester" class="input" required>
              <option value="LS">LS</option>
              <option value="ZS">ZS</option>
            </select>
          </div>

          <div class="form-group">
            <label for="datumZaciatku" class="label">Dátum začiatku<span class="required">*</span></label>
            <input id="datumZaciatku" v-model="formData.datumZaciatku" type="date" class="input" required />
          </div>

          <div class="form-group">
            <label for="datumKonca" class="label">Dátum konca<span class="required">*</span></label>
            <input id="datumKonca" v-model="formData.datumKonca" type="date" class="input" required />
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
import { reactive } from 'vue';
import { useRouter } from 'vue-router';
// 1. Importujte váš Pinia auth store
import { useAuthStore } from '../stores/auth';

const router = useRouter();
// 2. Vytvorte inštanciu store
const authStore = useAuthStore();

const formData = reactive({
  firma: '',
  rok: new Date().getFullYear(),
  semester: 'LS',
  datumZaciatku: '',
  datumKonca: ''
});

const handleSubmit = async () => {
  // 3. Získajte token priamo z auth storu
  const token = authStore.token;

  // 4. Skontrolujte, či token vôbec existuje
  if (!token) {
    alert('Chyba: Neboli nájdené prihlasovacie údaje. Prosím, prihláste sa znova.');
    router.push('/login');
    return;
  }

  try {
    const response = await fetch('http://localhost:8000/api/internships', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        // 5. Priložte token do hlavičky Authorization
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify(formData)
    });

    const data = await response.json();

    if (!response.ok) {
      // Špecifická chyba pre 401
      if (response.status === 401) {
        throw new Error(data.message || 'Neoprávnený prístup. Vaše prihlásenie mohlo vypršať.');
      }
      // Validačné chyby
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
    alert(error.message); // Zobrazíme presnú chybovú hlášku používateľovi
  }
};

const handleCancel = () => {
  if (confirm('Naozaj chcete zrušiť vytvorenie praxe?')) {
    Object.assign(formData, {
      firma: '',
      rok: new Date().getFullYear(),
      semester: 'LS',
      datumZaciatku: '',
      datumKonca: ''
    });
  }
};
</script>

<style scoped>
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

.header {
   display: flex;
   align-items: center;
   padding: 1rem 2rem;
   background: white;
   border-bottom: 1px solid #e5e7eb;
   gap: 2rem;
}

.logo {
   display: flex;
   align-items: center;
   gap: 0.5rem;
}

.logo-text {
   font-size: 1.125rem;
   font-weight: 600;
   color: #1f2937;
}

.nav {
   display: flex;
   gap: 1.5rem;
   flex: 1;
}

.nav-link {
   text-decoration: none;
   color: #6b7280;
   font-size: 0.875rem;
   padding: 0.5rem 0;
   border-bottom: 2px solid transparent;
   transition: all 0.2s;
}

.nav-link:hover {
   color: #2563eb;
}

.nav-link.active {
   color: #2563eb;
   border-bottom-color: #2563eb;
}

.user-section {
   display: flex;
   align-items: center;
   gap: 1rem;
}

.badge {
   background: #2563eb;
   color: white;
   padding: 0.25rem 0.75rem;
   border-radius: 0.375rem;
   font-size: 0.875rem;
   font-weight: 500;
}

.btn-link {
   background: none;
   border: none;
   color: #6b7280;
   font-size: 0.875rem;
   cursor: pointer;
   text-decoration: none;
}

.btn-link:hover {
   color: #2563eb;
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
   grid-template-columns: repeat(4, 1fr);
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

@media (max-width: 768px) {
   .form-row {
     grid-template-columns: 1fr;
   }

   .header {
     flex-direction: column;
     align-items: flex-start;
   }

   .nav {
     width: 100%;
   }
}
</style>