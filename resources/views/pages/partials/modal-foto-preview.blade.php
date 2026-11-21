<div class="modal fade" id="modalFotoPreview" tabindex="-1" aria-labelledby="modalFotoPreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="modalFotoPreviewLabel">Pratinjau foto</h2>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-0">
                <div class="foto-preview-stage">
                    <img id="fotoPreviewGambar" src="" alt="Pratinjau lampiran">
                </div>
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-3">
                    <div class="text-break" id="fotoPreviewNama">-</div>
                    <div class="hint mb-0" id="fotoPreviewInfo">-</div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-brand btn-sm" type="button" id="fotoPreviewSebelumnya">
                        <i class="bi bi-chevron-left me-1"></i>Sebelumnya
                    </button>
                    <button class="btn btn-outline-brand btn-sm" type="button" id="fotoPreviewBerikutnya">
                        Berikutnya<i class="bi bi-chevron-right ms-1"></i>
                    </button>
                </div>
                <a class="btn btn-brand btn-sm" id="fotoPreviewBuka" href="#" target="_blank" rel="noopener">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Buka berkas
                </a>
            </div>
        </div>
    </div>
</div>
