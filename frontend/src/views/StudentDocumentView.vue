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
                Miesto pre zobrazenie random upozornenia.
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
import { ref } from "vue";
import { useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const zmluvaFile = ref(null);
const vykazFile = ref(null);
const route = useRoute();
const authStore = useAuthStore();
const internshipId = ref(route.params.id || route.query.internshipId || null);

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

    const resp = await fetch(`http://localhost:8000/api/student/internships/${internshipId.value}/documents/agreement-signed`, {
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
  } catch (e) {
    alert(e.message);
  }
}

async function downloadGeneratedAgreement() {
  if (!internshipId.value) {
    alert('Chýba ID praxe.');
    return;
  }
  try {
    const resp = await fetch(`http://localhost:8000/api/vykaz-generate/${internshipId.value}`, {
      method: 'GET',
      headers: { 'Accept': 'application/pdf' }
    });
    if (!resp.ok) {
      const data = await resp.json().catch(() => ({}));
      throw new Error(data.message || `Chyba servera: ${resp.status}`);
    }
    const blob = await resp.blob();
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'dohoda.pdf';
    a.click();
    window.URL.revokeObjectURL(url);
  } catch (e) {
    alert(e.message);
  }
}
</script>
