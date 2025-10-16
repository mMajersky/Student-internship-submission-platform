<template>
   <div class="container">
     <header class="header">
       <div class="logo">
         <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
xmlns="http://www.w3.org/2000/svg">
           <path d="M12 2L2 7V17L12 22L22 17V7L12 2Z" stroke="#2563eb"
stroke-width="2" stroke-linejoin="round"/>
           <path d="M12 22V12" stroke="#2563eb" stroke-width="2"/>
           <path d="M2 7L12 12L22 7" stroke="#2563eb" stroke-width="2"
stroke-linejoin="round"/>
         </svg>
         <span class="logo-text">Odborná prax</span>
       </div>
       <nav class="nav">
         <a href="#" class="nav-link active">Prehľad</a>
         <a href="#" class="nav-link">Nová prax</a>
         <a href="#" class="nav-link">Dokumenty</a>
       </nav>
       <div class="user-section">
         <span class="badge">Študent</span>
         <a href="#" class="btn-link">Nastavenia</a>
         <a href="#" class="btn-link">Odhlásiť</a>
       </div>
     </header>

     <main class="main">
       <h1 class="title">Prehľad študenta</h1>

       <div class="stats-grid">
         <div class="stat-card">
           <div class="stat-label">Aktívne praxe</div>
           <div class="stat-value">{{ stats.aktivne }}</div>
         </div>
         <div class="stat-card">
           <div class="stat-label">Schválené</div>
           <div class="stat-value">{{ stats.schvalene }}</div>
         </div>
         <div class="stat-card">
           <div class="stat-label">Obhájené</div>
           <div class="stat-value">{{ stats.obhajene }}</div>
         </div>
       </div>

       <div class="section-header">
         <h2 class="section-title">Praxe</h2>
         <button @click="handleNovaPrax" class="btn btn-primary">
           <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
xmlns="http://www.w3.org/2000/svg">
             <path d="M8 3V13M3 8H13" stroke="currentColor"
stroke-width="2" stroke-linecap="round"/>
           </svg>
           Nová prax
         </button>
       </div>

       <div class="table-container">
         <table class="table">
           <thead>
             <tr>
               <th>Firma</th>
               <th>Rok / Semester</th>
               <th>Termín</th>
               <th>Stav</th>
               <th></th>
             </tr>
           </thead>
           <tbody>
             <tr v-for="prax in praxe" :key="prax.id">
               <td class="td-firma">{{ prax.firma }}</td>
               <td>{{ prax.rok }} / {{ prax.semester }}</td>
               <td>{{ prax.termin }}</td>
               <td>
                 <span
                   class="status-badge"
                   :class="`status-${prax.stav.toLowerCase()}`"
                 >
                   {{ prax.stav }}
                 </span>
               </td>
               <td class="td-actions">
                 <button
                   @click="handleDokumenty(prax)"
                   class="btn btn-outline"
                 >
                   Dokumenty
                 </button>
               </td>
             </tr>
           </tbody>
         </table>
       </div>
     </main>

     <footer class="footer">
       © 2025 Odborná prax CRM
     </footer>
   </div>
</template>

<script setup>
import { ref, reactive } from 'vue';

const stats = reactive({
   aktivne: 1,
   schvalene: 1,
   obhajene: 0
});

const praxe = ref([
   {
     id: 1,
     firma: 'TechSoft s.r.o.',
     rok: 2025,
     semester: 'LS',
     termin: '01.02.2025 – 31.05.2025',
     stav: 'VYTVORENÁ'
   },
   {
     id: 2,
     firma: 'Inova Labs a.s.',
     rok: 2024,
     semester: 'ZS',
     termin: '15.09.2024 – 20.12.2024',
     stav: 'SCHVÁLENÁ'
   }
]);

const handleNovaPrax = () => {
   console.log('Vytvoriť novú prax');
   // Tu by bolo presmerovanie na stránku s formulárom
};

const handleDokumenty = (prax) => {
   console.log('Otvoriť dokumenty pre prax:', prax);
   // Tu by bolo presmerovanie na dokumenty
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
   font-weight: 500;
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
   text-decoration: underline;
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

.stats-grid {
   display: grid;
   grid-template-columns: repeat(3, 1fr);
   gap: 1.5rem;
   margin-bottom: 2.5rem;
}

.stat-card {
   background: white;
   border: 1px solid #e5e7eb;
   border-radius: 0.5rem;
   padding: 1.5rem;
}

.stat-label {
   font-size: 0.875rem;
   color: #6b7280;
   margin-bottom: 0.5rem;
}

.stat-value {
   font-size: 2.25rem;
   font-weight: 600;
   color: #1f2937;
}

.section-header {
   display: flex;
   justify-content: space-between;
   align-items: center;
   margin-bottom: 1.5rem;
}

.section-title {
   font-size: 1.5rem;
   font-weight: 600;
   color: #1f2937;
}

.btn {
   display: inline-flex;
   align-items: center;
   gap: 0.5rem;
   padding: 0.625rem 1.25rem;
   border-radius: 0.375rem;
   font-size: 0.875rem;
   font-weight: 500;
   cursor: pointer;
   border: none;
   transition: all 0.2s;
}

.btn-primary {
   background: #2563eb;
   color: white;
}

.btn-primary:hover {
   background: #1d4ed8;
}

.btn-outline {
   background: white;
   color: #374151;
   border: 1px solid #d1d5db;
}

.btn-outline:hover {
   background: #f9fafb;
}

.table-container {
   background: white;
   border: 1px solid #e5e7eb;
   border-radius: 0.5rem;
   overflow: hidden;
}

.table {
   width: 100%;
   border-collapse: collapse;
}

.table thead {
   background: #f9fafb;
}

.table th {
   text-align: left;
   padding: 0.875rem 1rem;
   font-size: 0.875rem;
   font-weight: 600;
   color: #374151;
   border-bottom: 1px solid #e5e7eb;
}

.table td {
   padding: 1rem;
   font-size: 0.875rem;
   color: #1f2937;
   border-bottom: 1px solid #e5e7eb;
}

.table tbody tr:last-child td {
   border-bottom: none;
}

.table tbody tr:hover {
   background: #f9fafb;
}

.td-firma {
   font-weight: 500;
}

.td-actions {
   text-align: right;
}

.status-badge {
   display: inline-block;
   padding: 0.375rem 0.75rem;
   border-radius: 0.375rem;
   font-size: 0.75rem;
   font-weight: 600;
   text-transform: uppercase;
   letter-spacing: 0.025em;
}

.status-vytvorená {
   background: #6b7280;
   color: white;
}

.status-schválená {
   background: #10b981;
   color: white;
}

.status-obhájená {
   background: #3b82f6;
   color: white;
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
   .stats-grid {
     grid-template-columns: 1fr;
   }

   .header {
     flex-direction: column;
     align-items: flex-start;
   }

   .nav {
     width: 100%;
   }

   .section-header {
     flex-direction: column;
     align-items: flex-start;
     gap: 1rem;
   }

   .table-container {
     overflow-x: auto;
   }
}
</style>