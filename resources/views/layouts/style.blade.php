<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Kas Desa Babatan')</title>
    <link rel="icon" type="image/png" href="{{ asset('sikatan/img/logo-sikatan.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('sikatan/img/logo-sikatan.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('sikatan/img/logo-sikatan.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('sikatan/assets/css/app.css') }}" rel="stylesheet">

    <!-- CSS DataTables Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    <style>
        /* 1. Area Filter (Tampil & Cari) */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #6c757d;
        }

        /* 2. Gaya Kotak Pencarian & Dropdown (Oval) */
        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            border-radius: 50px !important;
            padding: 0.375rem 1.25rem !important;
            border: 1px solid #ced4da !important;
            background-color: #f8f9fa !important;
            transition: all 0.2s ease-in-out;
            box-shadow: none !important;
        }

        .dataTables_wrapper .dataTables_length select {
            padding-right: 2rem !important;
            /* Ruang untuk panah dropdown */
        }

        .dataTables_wrapper .dataTables_filter input:focus,
        .dataTables_wrapper .dataTables_length select:focus {
            background-color: #ffffff !important;
            border-color: var(--ks-green) !important;
            outline: none;
        }

        /* 3. Garis Pembatas Atas Tabel */
        .dataTables_wrapper .row:first-child {
            padding-bottom: 1rem;
            margin-bottom: 1rem;
            border-bottom: 1px dashed #dee2e6;
        }

        /* 4. TOMBOL PAGINATION (Bersih Tanpa Kotak Dobel) */
        .dataTables_wrapper .dataTables_paginate {
            display: flex;
            gap: 0.25rem;
            justify-content: flex-end;
        }

        /* Kontainer luar: hapus semua border & background */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border: none !important;
            background: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
            box-shadow: none !important;
        }

        /* Link dalam: Jadikan ini sebagai bentuk oval tunggal */
        .dataTables_wrapper .dataTables_paginate .paginate_button a {
            display: block;
            padding: 0.375rem 1rem !important;
            border-radius: 50px !important;
            border: 1px solid #ced4da !important;
            background-color: #f8f9fa !important;
            color: #495057 !important;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
        }

        /* Hover effect */
        .dataTables_wrapper .dataTables_paginate .paginate_button a:hover {
            background-color: #e9ecef !important;
            color: #000 !important;
            border-color: #adb5bd !important;
        }

        /* Tombol Aktif (Halaman Saat Ini) */
        .dataTables_wrapper .dataTables_paginate .paginate_button.current a,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current a:hover {
            background-color: var(--ks-green) !important;
            color: #fff !important;
            border-color: var(--ks-green) !important;
        }

        /* Tombol Disable (Kembali/Lanjut) */
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled a {
            opacity: 0.5;
            background-color: transparent !important;
            border-color: transparent !important;
            cursor: not-allowed;
        }

        /* 5. Merapikan Tabel & Tombol Aksi */
        table.dataTable.no-footer {
            border-bottom: 1px solid #dee2e6;
        }

        .btn-sm.btn-outline-secondary,
        .btn-sm.btn-outline-warning,
        .btn-sm.btn-outline-danger {
            padding: 0.25rem 0.5rem;
            border-color: transparent;
        }

        .btn-sm.btn-outline-secondary:hover {
            background: #e9ecef;
        }

        .btn-sm.btn-outline-warning:hover {
            background: #fff3cd;
        }

        .btn-sm.btn-outline-danger:hover {
            background: #f8d7da;
        }
    </style>
</head>
