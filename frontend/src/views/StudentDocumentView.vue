<template>
  <div id="app-wrapper" class="p-4">
    <div class="container">
      <h5 class="mb-4">
        <i class="bi bi-file-earmark-text me-2"></i> Odborná prax
      </h5>

      <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
          <a class="nav-link active" href="#">Dokumenty</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-muted" href="#">Prehľad</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-muted" href="#">Moje praxe</a>
        </li>
      </ul>

      <div class="card p-4">
        <h5 class="mb-4">Dokumenty praxe</h5>

        <div class="row g-4">
          <!-- Ľavý stĺpec -->
          <div class="col-md-6">
            <div class="border rounded p-4 bg-white h-100">
              <h6 class="fw-semibold mb-3">Dohoda o odbornej praxi (PDF)</h6>
              <p class="text-muted small mb-3">
                Generované systémom po vytvorení praxe.
              </p>
              <button class="btn btn-primary btn-sm" @click="downloadGeneratedAgreement">
                <i class="bi bi-download me-1"></i> Stiahnuť dohodu
              </button>

              <div class="alert alert-info mt-4" role="alert">
                <h6 class="alert-heading"><i class="bi bi-shield-lock me-2"></i>Bezpečnosť dokumentov</h6>
                <p class="mb-0 small">
                  <strong>Kde sa ukladajú dokumenty?</strong><br>
                  Všetky nahraté dokumenty (najmä podpísané PDF) sú uložené v <strong>zabezpečenom súkromnom úložisku</strong> na serveri 
                  (<code>storage/app/private/documents/</code>). Dokumenty <strong>NIE SÚ verejne prístupné</strong> - prístup k nim majú 
                  len autorizovaní používatelia cez API s kontrolou oprávnení.
                </p>
              </div>
            </div>
          </div>

          <!-- Pravý stĺpec -->
          <div class="col-md-6">
            <div class="border rounded p-4 bg-white mb-4">
              <h6 class="fw-semibold mb-3">
                Zmluva podpísaná študentom
                <small class="text-muted d-block">
                  (povinné pri stave „Schválená“)
                </small>
              </h6>

              <div class="mb-3">
                <label for="uploadZmluva" class="form-label">Nahrať dokument (PDF)</label>
                <input
                  type="file"
                  id="uploadZmluva"
                  class="form-control form-control-sm"
                  accept="application/pdf"
                  @change="handleFileUpload($event, 'zmluva')"
                />
              </div>
              <button class="btn btn-primary btn-sm" @click="uploadFile('zmluva')">
                Nahrať
              </button>

              <div class="mt-3" v-if="signedAgreement">
                <div class="alert alert-success py-2 px-3 mb-2">
                  <i class="bi bi-check-circle me-2"></i>
                  Nahrané: <strong>{{ signedAgreement.name }}</strong>
                  <small class="text-muted ms-2">({{ new Date(signedAgreement.created_at).toLocaleString() }})</small>
                </div>
                <div class="d-flex gap-2">
                  <button class="btn btn-success btn-sm" @click="downloadSignedAgreement">
                    <i class="bi bi-download me-1"></i> Stiahnuť
                  </button>
                  <button class="btn btn-danger btn-sm" @click="deleteSignedAgreement">
                    <i class="bi bi-trash me-1"></i> Odstrániť
                  </button>
                </div>
              </div>
            </div>

            <div class="border rounded p-4 bg-white">
              <h6 class="fw-semibold mb-3">Výkaz praxe (nepovinné)</h6>

              <div class="mb-3">
                <label for="uploadVykaz" class="form-label">Nahrať dokument (PDF)</label>
                <input
                  type="file"
                  id="uploadVykaz"
                  class="form-control form-control-sm"
                  accept="application/pdf"
                  @change="handleFileUpload($event, 'vykaz')"
                />
              </div>
              <button class="btn btn-primary btn-sm" @click="uploadFile('vykaz')">
                Nahrať
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const zmluvaFile = ref(null);
const vykazFile = ref(null);
const route = useRoute();
const authStore = useAuthStore();
const internshipId = ref(route.params.id || route.query.internshipId || null);
const signedAgreement = ref(null);

const loadSignedMeta = async () => {
  if (!internshipId.value || !authStore.token) return;
  
  try {
    const resp = await fetch(`/api/student/internships/${internshipId.value}/documents/agreement-signed/meta`, {
      headers: { 'Authorization': `Bearer ${authStore.token}` }
    });
    
    if (!resp.ok) {
      console.error(`Chyba ${resp.status} pri načítaní metadát dokumentu`);
      signedAgreement.value = null;
      return;
    }
    
    const data = await resp.json();
    // Backend teraz vracia 200 aj keď dokument neexistuje (document: null)
    signedAgreement.value = data.document;
  } catch (e) { 
    console.error('Chyba pri načítaní metadát dokumentu:', e);
    signedAgreement.value = null;
  }
};
onMounted(loadSignedMeta);

function handleFileUpload(event, type) {
  const file = event.target.files[0];
  if (type === "zmluva") zmluvaFile.value = file;
  else vykazFile.value = file;
}

async function uploadFile(type) {
  const file = type === "zmluva" ? zmluvaFile.value : vykazFile.value;

  if (!internshipId.value) {
    alert('Chýba ID praxe.');
    return;
  }

  if (!file) {
    alert("Vyber súbor pred nahraním.");
    return;
  }

  if (!authStore.token) {
    alert('Nie ste prihlásený.');
    return;
  }

  // Zatiaľ implementujeme iba upload podpísanej dohody
  if (type !== 'zmluva') {
    alert('Upload výkazu nie je ešte implementovaný.');
    return;
  }

  try {
    const formData = new FormData();
    formData.append('file', file);

    const resp = await fetch(`/api/student/internships/${internshipId.value}/documents/agreement-signed`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${authStore.token}`
      },
      body: formData,
    });

    const data = await resp.json();
    if (!resp.ok) {
      throw new Error(data.message || 'Nepodarilo sa nahrať súbor.');
    }

    alert('Podpísaná dohoda bola nahraná.');
    await loadSignedMeta();
  } catch (e) {
    alert(e.message);
  }
}

async function downloadGeneratedAgreement() {
  if (!internshipId.value || !authStore.token) {
    alert('Chýba ID praxe alebo nie ste prihlásený.');
    return;
  }
  try {
    const resp = await fetch(`/api/student/internships/${internshipId.value}/documents/agreement-generated`, {
      method: 'GET',
      headers: { 
        'Accept': 'application/pdf', 
        'Authorization': `Bearer ${authStore.token}` 
      }
    });
    if (!resp.ok) {
      const data = await resp.json().catch(() => ({}));
      throw new Error(data.message || `Chyba servera: ${resp.status}`);
    }
    const blob = await resp.blob();
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `Dohoda_o_odbornej_praxi_${internshipId.value}.pdf`;
    a.click();
    window.URL.revokeObjectURL(url);
  } catch (e) {
    alert(e.message);
  }
}

async function downloadSignedAgreement() {
  if (!internshipId.value || !authStore.token) {
    alert('Chýba ID praxe alebo nie ste prihlásený.');
    return;
  }
  try {
    const resp = await fetch(`/api/student/internships/${internshipId.value}/documents/agreement-signed`, {
      method: 'GET',
      headers: { 
        'Authorization': `Bearer ${authStore.token}` 
      }
    });
    if (!resp.ok) {
      const data = await resp.json().catch(() => ({}));
      throw new Error(data.message || `Chyba servera: ${resp.status}`);
    }
    const blob = await resp.blob();
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = signedAgreement.value?.name || 'Podpisana_dohoda.pdf';
    a.click();
    window.URL.revokeObjectURL(url);
  } catch (e) {
    alert(e.message);
  }
}

async function deleteSignedAgreement() {
  if (!confirm('Naozaj chcete odstrániť tento dokument? Táto akcia sa nedá vrátiť späť.')) {
    return;
  }

  if (!internshipId.value || !authStore.token) {
    alert('Chýba ID praxe alebo nie ste prihlásený.');
    return;
  }

  try {
    const resp = await fetch(`/api/student/internships/${internshipId.value}/documents/agreement-signed`, {
      method: 'DELETE',
      headers: { 
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json'
      }
    });

    const data = await resp.json();
    
    if (!resp.ok) {
      throw new Error(data.message || `Chyba servera: ${resp.status}`);
    }

    alert('Dokument bol úspešne odstránený.');
    signedAgreement.value = null;
  } catch (e) {
    alert(e.message);
  }
}
</script>
