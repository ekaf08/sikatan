<div class="modal fade" id="modalFotoPreview" tabindex="-1" aria-labelledby="modalFotoPreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="modalFotoPreviewLabel">Pratinjau foto</h2>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-0">
                <div class="foto-preview-stage d-flex align-items-center justify-content-center overflow-hidden">
                    <img id="fotoPreviewGambar" class="img-fluid mw-100 mh-100 object-fit-contain rounded" alt="Pratinjau lampiran">
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

<script>
    (function () {
        "use strict";

        var fotoModal = document.getElementById("modalFotoPreview");

        if (!fotoModal) {
            return;
        }

        var gambarFoto = fotoModal.querySelector("#fotoPreviewGambar");
        var namaFoto = fotoModal.querySelector("#fotoPreviewNama");
        var infoFoto = fotoModal.querySelector("#fotoPreviewInfo");
        var bukaFoto = fotoModal.querySelector("#fotoPreviewBuka");
        var fotoSebelumnya = fotoModal.querySelector("#fotoPreviewSebelumnya");
        var fotoBerikutnya = fotoModal.querySelector("#fotoPreviewBerikutnya");
        var daftarFoto = [];
        var indeksFoto = 0;

        function renderFotoPreview() {
            if (!daftarFoto.length || !gambarFoto) {
                return;
            }

            indeksFoto = ((indeksFoto % daftarFoto.length) + daftarFoto.length) % daftarFoto.length;

            var foto = daftarFoto[indeksFoto] || {};

            gambarFoto.src = foto.url || "";
            gambarFoto.alt = foto.nama || "Pratinjau lampiran";

            if (namaFoto) {
                namaFoto.textContent = foto.nama || "-";
            }

            if (infoFoto) {
                infoFoto.textContent = (indeksFoto + 1) + " dari " + daftarFoto.length;
            }

            if (bukaFoto) {
                bukaFoto.href = foto.url || "#";
                bukaFoto.classList.toggle("d-none", !foto.url);
            }

            if (fotoSebelumnya) {
                fotoSebelumnya.disabled = daftarFoto.length < 2;
            }

            if (fotoBerikutnya) {
                fotoBerikutnya.disabled = daftarFoto.length < 2;
            }
        }

        fotoModal.addEventListener("show.bs.modal", function (event) {
            var trigger = event.relatedTarget;

            if (!trigger || !trigger.hasAttribute("data-foto-preview")) {
                return;
            }

            try {
                daftarFoto = JSON.parse(trigger.getAttribute("data-foto-daftar") || "[]");
            } catch (error) {
                daftarFoto = [];
            }

            if (!Array.isArray(daftarFoto)) {
                daftarFoto = [];
            }

            var indeks = parseInt(trigger.getAttribute("data-foto-indeks"), 10);
            indeksFoto = Number.isNaN(indeks) ? 0 : indeks;

            renderFotoPreview();
        });

        fotoModal.addEventListener("hidden.bs.modal", function () {
            daftarFoto = [];
            indeksFoto = 0;

            if (gambarFoto) {
                gambarFoto.removeAttribute("src");
            }
        });

        if (fotoSebelumnya) {
            fotoSebelumnya.addEventListener("click", function () {
                if (!daftarFoto.length) {
                    return;
                }

                indeksFoto -= 1;
                renderFotoPreview();
            });
        }

        if (fotoBerikutnya) {
            fotoBerikutnya.addEventListener("click", function () {
                if (!daftarFoto.length) {
                    return;
                }

                indeksFoto += 1;
                renderFotoPreview();
            });
        }

        fotoModal.addEventListener("keydown", function (event) {
            if (!daftarFoto.length) {
                return;
            }

            if (event.key === "ArrowLeft") {
                indeksFoto -= 1;
                renderFotoPreview();
            }

            if (event.key === "ArrowRight") {
                indeksFoto += 1;
                renderFotoPreview();
            }
        });
    })();
</script>
