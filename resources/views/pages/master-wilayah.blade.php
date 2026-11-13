@extends('layouts.app')

@section('title', 'Master Wilayah RW/RT &mdash; Kas Desa Babatan')

@section('topbarTitle', 'Master data')
@section('topbarSubtitle', 'Wilayah RW/RT')

@section('content')
<div class="mb-4">
        <h1 class="page-title">Master data</h1>
        <p class="page-subtitle">Data acuan yang jarang berubah: wilayah, pos dana, dan kategori.</p>
      </div>

      <div class="subnav mb-4">
        <a class="tab-app active" href="{{ route('master-wilayah') }}" aria-current="page">Wilayah RW/RT</a>
        <a class="tab-app" href="{{ route('master-pos-dana') }}">Pos dana</a>
        <a class="tab-app" href="{{ route('master-kategori') }}">Kategori</a>
      </div>

      <div class="row row-cols-1 row-cols-sm-3 g-3 mb-4">
        <div class="col">
          <div class="card stat-card h-100">
            <div class="card-body">
              <span class="stat-label">Total RW</span>
              <span class="stat-value">2</span>
              <span class="stat-sub">Wilayah terdata</span>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card stat-card h-100">
            <div class="card-body">
              <span class="stat-label">Total RT</span>
              <span class="stat-value">15</span>
              <span class="stat-sub">Tersebar di 2 RW</span>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card stat-card h-100">
            <div class="card-body">
              <span class="stat-label">Total KK</span>
              <span class="stat-value">556</span>
              <span class="stat-sub">Kepala keluarga terdata</span>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h2 class="section-title">Wilayah RW dan RT</h2>
            <button class="btn btn-brand" type="button" data-bs-toggle="modal" data-bs-target="#modalRt">
              <i class="bi bi-plus-lg"></i>Tambah RT
            </button>
          </div>

          <div class="table-responsive">
            <table class="table-app">
              <thead>
                <tr>
                  <th>RT</th>
                  <th>Penarik iuran</th>
                  <th class="num">Jumlah KK</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr class="group-row">
                  <td colspan="4">RW 01 &mdash; Ketua: Bapak Slamet (contoh)</td>
                </tr>
                <tr>
                  <td>RT 01</td>
                  <td>Pak Joko</td>
                  <td class="num">38</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalRt">Ubah</a></td>
                </tr>
                <tr>
                  <td>RT 02</td>
                  <td>Pak Budi</td>
                  <td class="num">40</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalRt">Ubah</a></td>
                </tr>
                <tr>
                  <td>RT 03</td>
                  <td>Pak Agus</td>
                  <td class="num">35</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalRt">Ubah</a></td>
                </tr>
                <tr>
                  <td>RT 04</td>
                  <td>Pak Wahyu</td>
                  <td class="num">41</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalRt">Ubah</a></td>
                </tr>
                <tr>
                  <td>RT 05</td>
                  <td>Pak Eko</td>
                  <td class="num">29</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalRt">Ubah</a></td>
                </tr>
                <tr>
                  <td>RT 06</td>
                  <td>Pak Imam</td>
                  <td class="num">47</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalRt">Ubah</a></td>
                </tr>
                <tr>
                  <td>RT 07</td>
                  <td>Pak Darto</td>
                  <td class="num">33</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalRt">Ubah</a></td>
                </tr>

                <tr class="group-row">
                  <td colspan="4">RW 02 &mdash; Ketua: Bapak Hartono (contoh)</td>
                </tr>
                <tr>
                  <td>RT 01</td>
                  <td>Pak Rudi</td>
                  <td class="num">36</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalRt">Ubah</a></td>
                </tr>
                <tr>
                  <td>RT 02</td>
                  <td>Pak Heri</td>
                  <td class="num">40</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalRt">Ubah</a></td>
                </tr>
                <tr>
                  <td>RT 03</td>
                  <td>Pak Sugeng</td>
                  <td class="num">31</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalRt">Ubah</a></td>
                </tr>
                <tr>
                  <td>RT 04</td>
                  <td>Pak Anwar</td>
                  <td class="num">45</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalRt">Ubah</a></td>
                </tr>
                <tr>
                  <td>RT 05</td>
                  <td>Pak Bambang</td>
                  <td class="num">38</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalRt">Ubah</a></td>
                </tr>
                <tr>
                  <td>RT 06</td>
                  <td>Pak Teguh</td>
                  <td class="num">27</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalRt">Ubah</a></td>
                </tr>
                <tr>
                  <td>RT 07</td>
                  <td>Pak Yanto</td>
                  <td class="num">42</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalRt">Ubah</a></td>
                </tr>
                <tr>
                  <td>RT 08</td>
                  <td>Pak Mulyadi</td>
                  <td class="num">34</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalRt">Ubah</a></td>
                </tr>
              </tbody>
            </table>
          </div>

          <p class="hint mt-3">Nama penarik hanya catatan, bukan akun. Nomor telepon tidak disimpan.</p>
        </div>
      </div>
    

  <div class="modal fade" id="modalRt" tabindex="-1" aria-labelledby="modalRtLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="modal-title fs-5" id="modalRtLabel">Tambah / ubah RT</h2>
          <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <form onsubmit="return false">
          <div class="modal-body d-flex flex-column gap-3">
            <div>
              <label class="form-label-app" for="rtRw">RW</label>
              <select class="form-select" id="rtRw">
                <option>RW 01</option>
                <option>RW 02</option>
              </select>
            </div>
            <div>
              <label class="form-label-app" for="rtNomor">Nomor RT</label>
              <input class="form-control" id="rtNomor" type="text" placeholder="Contoh: RT 08">
            </div>
            <div>
              <label class="form-label-app" for="rtPenarik">Penarik iuran</label>
              <input class="form-control" id="rtPenarik" type="text" placeholder="Nama penarik">
            </div>
            <div>
              <label class="form-label-app" for="rtKk">Jumlah KK</label>
              <input class="form-control" id="rtKk" type="number" min="0" value="30">
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline-brand" type="button" data-bs-dismiss="modal">Batal</button>
            <button class="btn btn-brand" type="submit">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
