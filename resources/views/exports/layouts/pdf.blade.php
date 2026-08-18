<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('docTitle', 'Laporan') – {{ config('app.name') }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            background: #ffffff;
            line-height: 1.5;
        }

        .page-number::after {
            content: counter(page) ' / ' counter(pages);
        }

        .footer {
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            font-size: 8px;
            color: #6b7280;
        }

        .footer-left {
            font-style: italic;
        }

        .footer-right {
            text-align: right;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
    @stack('styles')
</head>
<body>
    @yield('content')

    @yield('after')

    <div class="footer">
        <div class="footer-left">
            @yield('footerLeft', 'Laporan ini digenerate secara otomatis oleh sistem. Harap diverifikasi sebelum digunakan sebagai dokumen resmi.')
        </div>
        <div class="footer-right">
            Halaman <span class="page-number"></span>
        </div>
    </div>
</body>
</html>
