@extends('layouts.app')

@section('title', 'Pengaturan — Kas Desa Babatan')

@section('topbarTitle', 'Pengaturan')
@section('topbarSubtitle', 'Kop, penandatangan, akun')

@section('content')
    <div class="mb-4">
        <h1 class="page-title">Pengaturan</h1>
        <p class="page-subtitle">Kop laporan, penandatangan, nominal standar, dan akun pengguna.</p>
    </div>

    {{-- Sembunyikan alert HTML lama, kita tangkap lewat JS untuk SweetAlert --}}
    <div id="flash-data" data-password-sementara="{{ session('password_sementara') }}"
        data-username="{{ session('username_diatur_ulang') }}" data-akun-baru="{{ session('akun_baru') }}"
        data-galat="{{ session('galat') }}" data-sukses="{{ session('sukses') }}"
        data-has-error="{{ $errors->any() ? '1' : '0' }}" style="display: none;">
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <form class="card h-100" data-demo-form data-demo-message="Pengaturan disimpan (contoh)."
                onsubmit="return false">
                <div class="card-body d-flex flex-column gap-3">
                    <h2 class="section-title">Kop laporan</h2>
                    <div>
                        <label class="form-label-app" for="setNamaDesa">Nama desa</label>
                        <input class="form-control" id="setNamaDesa" type="text" value="Desa Babatan">
                    </div>
                    <div>
                        <label class="form-label-app" for="setKop1">Baris kop 1</label>
                        <input class="form-control" id="setKop1" type="text" value="PEMERINTAH DESA BABATAN">
                    </div>
                    <div>
                        <label class="form-label-app" for="setKop2">Baris kop 2</label>
                        <input class="form-control" id="setKop2" type="text" value="LAPORAN KEUANGAN KAS WARGA">
                    </div>
                    <div>
                        <label class="form-label-app" for="setAlamat">Alamat pada kop</label>
                        <input class="form-control" id="setAlamat" type="text" value="Alamat desa diisi di sini">
                    </div>

                    <h2 class="section-title border-top pt-3 mt-2">Penandatangan laporan</h2>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label-app" for="setBendahara">Nama bendahara</label>
                            <input class="form-control" id="setBendahara" type="text" value="Bendahara (contoh)">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label-app" for="setKetua">Nama ketua</label>
                            <input class="form-control" id="setKetua" type="text" value="Ketua (contoh)">
                        </div>
                    </div>

                    <h2 class="section-title border-top pt-3 mt-2">Nominal standar</h2>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label-app" for="setIuran">Iuran kematian per KK per minggu (Rp)</label>
                            <input class="form-control" id="setIuran" type="text" value="5.000">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label-app" for="setSantunan">Santunan kematian standar (Rp)</label>
                            <input class="form-control" id="setSantunan" type="text" value="2.000.000">
                        </div>
                    </div>
                    <p class="hint mb-0">Mengubah nominal standar tidak mengubah transaksi yang sudah tercatat.</p>

                    <div class="notice notice-green" data-demo-feedback hidden></div>

                    <div>
                        <button class="btn btn-brand" type="submit">Simpan pengaturan</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Card Akun Pengguna (Kanan) -->
        <div class="col-lg-5">
            <div class="card">
                <div class="card-body d-flex flex-column">

                    <!-- Header Card: Judul & Tombol Tambah Akun -->
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <h2 class="section-title mb-0">Akun pengguna</h2>
                        <button class="btn btn-brand btn-sm" type="button" data-bs-toggle="modal"
                            data-bs-target="#modalAkun">
                            <i class="bi bi-plus-lg"></i> Tambah
                        </button>
                    </div>

                    <!-- Tabel DataTables -->
                    <div class="table-responsive flex-grow-1">
                        <table class="table-app w-100 align-middle" id="tabelAkun">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">No</th>
                                    <th>Pengguna</th>
                                    <th>Peran</th>
                                    <th class="text-end" style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($akun as $u)
                                    <tr>
                                        <td></td> <!-- Kolom No diisi otomatis oleh DataTables -->
                                        <td>
                                            <strong class="d-block">{{ $u->name }}</strong>
                                            <span class="hint">{{ $u->username ?? $u->email }}</span>
                                            @unless ($u->aktif)
                                                <span class="badge-soft badge-neutral">Nonaktif</span>
                                            @endunless
                                        </td>
                                        <td>
                                            @if ($u->adalahAdmin())
                                                <span class="badge-soft badge-in">Admin</span>
                                            @else
                                                <span class="badge-soft badge-warn">Bendahara</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if ($u->is(auth()->user()))
                                                <span class="hint">Akun Anda</span>
                                            @else
                                                <div class="d-flex justify-content-end gap-2">
                                                    <!-- Tombol Edit (Buka Modal) -->
                                                    <!-- Tombol Edit -->
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-secondary btn-edit-akun"
                                                        data-id="{{ $u->id }}" data-name="{{ $u->name }}"
                                                        data-username="{{ $u->username }}"
                                                        data-email="{{ $u->email }}" data-role="{{ $u->role }}"
                                                        data-action="{{ route('akun.update', $u->id) }}" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>

                                                    <!-- Tombol Atur Ulang -->
                                                    <form method="POST"
                                                        action="{{ route('akun.atur-ulang-password', $u) }}"
                                                        class="form-konfirmasi-akun d-inline m-0"
                                                        data-nama="{{ $u->name }}">
                                                        @csrf
                                                        <button class="btn btn-sm btn-outline-warning tombol-atur-ulang"
                                                            type="button" title="Atur Ulang Sandi">
                                                            <i class="bi bi-key"></i>
                                                        </button>
                                                    </form>

                                                    <!-- Tombol Hapus (SweetAlert) -->
                                                    <form method="POST" action="{{ route('akun.destroy', $u) }}"
                                                        class="form-hapus-akun d-inline m-0"
                                                        data-nama="{{ $u->name }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-outline-danger tombol-hapus"
                                                            type="button" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <!-- Dikosongkan, DataTables akan menangani pesan kosong -->
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Akun --}}
    <div class="modal fade" id="modalAkun" tabindex="-1" aria-labelledby="modalAkunLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="modalAkunLabel">Tambah akun baru</h2>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form method="POST" action="{{ route('akun.simpan') }}">
                    @csrf
                    <div class="modal-body d-flex flex-column gap-3">
                        <div>
                            <label class="form-label-app" for="akunNama">Nama pengguna</label>
                            <input class="form-control @error('name') is-invalid @enderror" id="akunNama" name="name"
                                type="text" value="{{ old('name') }}" placeholder="Contoh: Sekretaris Babatan"
                                required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label-app" for="akunUsername">Username</label>
                            <input class="form-control @error('username') is-invalid @enderror" id="akunUsername"
                                name="username" type="text" value="{{ old('username') }}"
                                placeholder="Contoh: sekretaris" autocomplete="off" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <p class="hint mt-1">Dipakai untuk masuk. Hanya huruf, angka, garis bawah, dan tanda hubung.
                            </p>
                        </div>
                        <div>
                            <label class="form-label-app" for="akunEmail">Email</label>
                            <input class="form-control @error('email') is-invalid @enderror" id="akunEmail"
                                name="email" type="email" value="{{ old('email') }}"
                                placeholder="nama@babatan.test" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label-app" for="akunPeran">Peran</label>
                            <select class="form-select @error('role') is-invalid @enderror" id="akunPeran" name="role"
                                required>
                                <option value="bendahara" @selected(old('role', 'bendahara') === 'bendahara')>Bendahara</option>
                                <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label-app" for="akunPass">Kata sandi awal</label>
                            <input class="form-control @error('password') is-invalid @enderror" id="akunPass"
                                name="password" type="password" placeholder="Minimal 8 karakter"
                                autocomplete="new-password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <p class="hint mt-1">Pengguna akan diminta mengganti kata sandi saat pertama masuk.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-brand" type="button" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-brand" type="submit">Simpan akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Akun -->
    <div class="modal fade" id="modalEditAkun" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5">Edit Akun</h2>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form method="POST" id="formEditAkun">
                    @csrf
                    @method('PUT')
                    <div class="modal-body d-flex flex-column gap-3">
                        <div>
                            <label class="form-label-app">Nama pengguna</label>
                            <input class="form-control" id="editNama" name="name" type="text" required>
                        </div>
                        <div>
                            <label class="form-label-app">Username</label>
                            <input class="form-control" id="editUsername" name="username" type="text" required>
                        </div>
                        <div>
                            <label class="form-label-app">Email</label>
                            <input class="form-control" id="editEmail" name="email" type="email" required>
                        </div>
                        <div>
                            <label class="form-label-app">Peran</label>
                            <select class="form-select" id="editRole" name="role" required>
                                <option value="bendahara">Bendahara</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label-app">Kata sandi baru (Opsional)</label>
                            <input class="form-control" id="editPassword" name="password" type="password"
                                placeholder="Biarkan kosong jika tidak ingin mengubah">
                            <p class="hint mt-1">Minimal 8 karakter.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-brand" type="button" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-brand" type="submit">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. Notifikasi Flash Message (Password, Sukses, Gagal)
            const flashData = document.getElementById('flash-data');
            const passSementara = flashData.dataset.passwordSementara;
            const username = flashData.dataset.username;
            const akunBaru = flashData.dataset.akunBaru;
            const galat = flashData.dataset.galat;
            const suksesMsg = flashData.dataset.sukses;

            if (passSementara && username) {
                Swal.fire({
                    title: 'Kata Sandi Sementara',
                    html: `
                    <p class="mb-2">Kata sandi untuk <strong>${username}</strong> adalah:</p>
                    <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
                        <input type="text" id="sandiBaru" class="form-control text-center fw-bold" value="${passSementara}" readonly style="max-width: 200px;">
                        <button type="button" class="btn btn-brand" id="btnCopy" title="Salin" style="padding: 6px 12px;">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                    <p class="text-muted small mb-0">Salin sekarang, kata sandi ini tidak ditampilkan lagi.</p>
                `,
                    icon: 'info',
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: 'var(--ks-green)',
                    didOpen: () => {
                        const btnCopy = document.getElementById('btnCopy');
                        const inputSandi = document.getElementById('sandiBaru');
                        btnCopy.addEventListener('click', () => {
                            inputSandi.select();
                            document.execCommand('copy');
                            btnCopy.innerHTML = '<i class="bi bi-check2"></i>';
                            setTimeout(() => {
                                btnCopy.innerHTML = '<i class="bi bi-clipboard"></i>';
                            }, 2000);
                        });
                    }
                });
            } else if (akunBaru) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: `Akun ${akunBaru} berhasil dibuat. Pengguna akan diminta mengganti kata sandi saat pertama kali masuk.`,
                    icon: 'success',
                    confirmButtonColor: 'var(--ks-green)',
                });
            } else if (galat) {
                Swal.fire({
                    title: 'Gagal',
                    text: galat,
                    icon: 'warning',
                    confirmButtonColor: '#d33',
                });
            } else if (suksesMsg) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: suksesMsg,
                    icon: 'success',
                    confirmButtonColor: 'var(--ks-green)',
                });
            }

            // 2. Konfirmasi Atur Ulang Password
            const tombolAturUlang = document.querySelectorAll('.tombol-atur-ulang');
            tombolAturUlang.forEach(tombol => {
                tombol.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('.form-konfirmasi-akun');
                    const nama = form.getAttribute('data-nama');

                    Swal.fire({
                        title: 'Konfirmasi',
                        text: `Atur ulang kata sandi untuk ${nama}?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: 'var(--ks-green)',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Atur Ulang',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // 3. Logika Edit Akun (Buka Modal & Isi Form)
            const modalEditEl = document.getElementById('modalEditAkun');
            if (modalEditEl) {
                const modalEditAkun = new bootstrap.Modal(modalEditEl);
                const formEditAkun = document.getElementById('formEditAkun');
                const tombolEdit = document.querySelectorAll('.btn-edit-akun');

                tombolEdit.forEach(tombol => {
                    tombol.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const name = this.getAttribute('data-name');
                        const username = this.getAttribute('data-username');
                        const email = this.getAttribute('data-email');
                        const role = this.getAttribute('data-role');
                        const actionUrl = this.getAttribute(
                            'data-action'); // Ambil URL dari data-action

                        document.getElementById('editNama').value = name;
                        document.getElementById('editUsername').value = username;
                        document.getElementById('editEmail').value = email;
                        document.getElementById('editRole').value = role;
                        document.getElementById('editPassword').value = '';

                        // Gunakan URL dari data-action
                        formEditAkun.action = actionUrl;

                        modalEditAkun.show();
                    });
                });
            }

            // 4. Logika Hapus Akun (Konfirmasi SweetAlert)
            const tombolHapus = document.querySelectorAll('.tombol-hapus');
            tombolHapus.forEach(tombol => {
                tombol.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('.form-hapus-akun');
                    const nama = form.getAttribute('data-nama');

                    Swal.fire({
                        title: 'Hapus Akun?',
                        text: `Anda yakin ingin menghapus akun ${nama}? Tindakan ini tidak dapat dibatalkan.`,
                        icon: 'error',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // 5. Modal Otomatis Jika Ada Error Validasi
            const hasError = flashData.dataset.hasError === '1';
            if (hasError) {
                var el = document.getElementById("modalAkun");
                if (el && window.bootstrap) {
                    new bootstrap.Modal(el).show();
                }
            }
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 6. Inisialisasi DataTables
            if (typeof $ !== 'undefined' && $.fn.DataTable) {
                $('#tabelAkun').DataTable({
                    "language": {
                        "search": "Cari:",
                        "lengthMenu": "Tampil _MENU_ data",
                        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ akun",
                        "infoEmpty": "Tidak ada data",
                        "zeroRecords": "Akun tidak ditemukan",
                        "paginate": {
                            "first": "Awal",
                            "last": "Akhir",
                            "next": "Lanjut",
                            "previous": "Kembali"
                        }
                    },
                    "pageLength": 5,
                    "ordering": true,
                    "columnDefs": [{
                        "targets": [0, 3],
                        "orderable": false
                    }],
                    "dom": "<'row align-items-center mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 d-flex justify-content-end'f>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row align-items-center mt-3'<'col-sm-12 col-md-6'i><'col-sm-12 col-md-6 d-flex justify-content-end'p>>",
                    "drawCallback": function(settings) {
                        var api = this.api();
                        var startIndex = api.context[0]._iDisplayStart;
                        api.column(0).nodes().each(function(cell, i) {
                            cell.innerHTML = startIndex + i + 1;
                        });
                    }
                });
            }
        });
    </script>
@endsection
