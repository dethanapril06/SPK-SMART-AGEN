<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Laporan' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #435ebe;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header h2 {
            font-size: 14px;
            color: #435ebe;
            margin-bottom: 3px;
        }

        .header h3 {
            font-size: 12px;
            margin-bottom: 3px;
        }

        .header p {
            font-size: 10px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        thead tr {
            background-color: #435ebe;
            color: white;
        }

        th {
            padding: 7px 8px;
            text-align: left;
            font-size: 10px;
        }

        td {
            padding: 6px 8px;
            font-size: 10px;
            border-bottom: 1px solid #eee;
        }

        tr:nth-child(even) {
            background-color: #f8f9ff;
        }

        .badge {
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-secondary {
            background: #f1f5f9;
            color: #475569;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
            color: #999;
        }

        .info-box {
            background: #f8f9ff;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 12px;
            font-size: 10px;
        }

        .info-box span {
            font-weight: bold;
            color: #435ebe;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>SPK SMART AGEN — BEJUBIS@ LAKUPANDAI</h2>
        <h3>{{ $title ?? 'Laporan' }}</h3>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    @yield('content')

    <div class="footer">
        Sistem Pendukung Keputusan Penerimaan Agen &mdash; {{ now()->year }}
    </div>
</body>

</html>
