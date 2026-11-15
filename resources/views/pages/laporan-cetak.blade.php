<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $data['kop_baris_2'] }} — {{ $data['periode_label'] }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('sikatan/assets/css/app.css') }}" rel="stylesheet">
    <style>
        body {
            background: #f4f1e8;
            padding: 24px;
        }

        .cetak-wrap {
            max-width: 900px;
            margin: 0 auto;
        }

        .cetak-aksi {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: flex-end;
            margin-bottom: 16px;
        }

        @page {
            size: A4;
            margin: 14mm;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .cetak-aksi {
                display: none !important;
            }

            .report-paper {
                border: none;
                padding: 0;
            }

            .table-responsive {
                overflow: visible !important;
            }
        }
    </style>
</head>

<body>
    <div class="cetak-wrap">
        <div class="cetak-aksi">
            <button class="btn btn-outline-secondary" type="button" onclick="window.close()">Tutup</button>
            <button class="btn btn-brand" type="button" onclick="window.print()">
                <i class="bi bi-printer"></i>Cetak / Simpan PDF
            </button>
        </div>

        @include('pages.laporan-preview', [
            'data' => $data,
            'isi' => $isi,
            'pengaturan' => $pengaturan,
        ])
    </div>

    <script>
        window.addEventListener('load', function() {
            window.setTimeout(function() {
                window.print();
            }, 300);
        });
    </script>
</body>

</html>
