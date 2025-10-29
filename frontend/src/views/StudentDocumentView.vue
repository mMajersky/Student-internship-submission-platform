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
              <button class="btn btn-primary btn-sm">
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

const zmluvaFile = ref(null);
const vykazFile = ref(null);

function handleFileUpload(event, type) {
  const file = event.target.files[0];
  if (type === "zmluva") zmluvaFile.value = file;
  else vykazFile.value = file;
}

function uploadFile(type) {
  const file = type === "zmluva" ? zmluvaFile.value : vykazFile.value;
  if (file) {
    alert(`Súbor ${file.name} bol úspešne nahraný.`);
  } else {
    alert("Vyber súbor pred nahraním.");
  }
}
</script>
