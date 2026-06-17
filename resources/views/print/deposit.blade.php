<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kuitansi Setoran Sampah #{{ $deposit->id }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 80mm;
            margin: 0 auto;
            padding: 10px;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 0;
            font-size: 16px;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        .details table {
            width: 100%;
        }
        .details td {
            vertical-align: top;
        }
        .right {
            text-align: right;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
        }
        .no-print {
            margin-bottom: 20px;
            text-align: center;
        }
        .btn-print {
            padding: 8px 16px;
            background: #10B981;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-family: sans-serif;
            font-weight: bold;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                width: 100%;
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">CETAK KUITANSI</button>
    </div>

    <div class="header">
        <h2>BANK SAMPAH FAPERTA</h2>
        <div>Bogor, Jawa Barat</div>
        <div>Telp: 08123456789</div>
    </div>

    <div class="divider"></div>

    <div class="details">
        <table>
            <tr>
                <td>ID Setoran:</td>
                <td class="right">#{{ $deposit->id }}</td>
            </tr>
            <tr>
                <td>Tanggal:</td>
                <td class="right">{{ $deposit->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td>Nasabah:</td>
                <td class="right">{{ $deposit->user->name }}</td>
            </tr>
            <tr>
                <td>No. Rek:</td>
                <td class="right">{{ $deposit->user->account_no }}</td>
            </tr>
            <tr>
                <td>Status:</td>
                <td class="right">{{ strtoupper($deposit->status) }}</td>
            </tr>
            <tr>
                <td>Tipe:</td>
                <td class="right">{{ $deposit->is_donation ? 'DONASI/SEDEKAH' : 'TABUNGAN' }}</td>
            </tr>
        </table>
    </div>

    <div class="divider"></div>

    <div class="details">
        <table style="width: 100%;">
            <thead>
                <tr style="text-align: left;">
                    <th>Item</th>
                    <th class="right">Berat</th>
                    <th class="right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($deposit->items as $item)
                <tr>
                    <td>{{ $item->trashPrice->name }}</td>
                    <td class="right">{{ number_format($item->weight, 2) }} {{ $item->trashPrice->unit }}</td>
                    <td class="right">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="divider"></div>

    <div class="details">
        <table>
            <tr style="font-weight: bold; font-size: 13px;">
                <td>TOTAL BERAT:</td>
                <td class="right">{{ number_format($deposit->weight_total, 2) }} kg/L</td>
            </tr>
            <tr style="font-weight: bold; font-size: 13px;">
                <td>TOTAL RUPIAH:</td>
                <td class="right">Rp {{ number_format($deposit->total_price, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="divider"></div>

    <div class="footer">
        <p>Terima kasih telah memilah sampah dan menabung bersama kami!</p>
        <p>Petugas: {{ $deposit->validator->name ?? '-' }}</p>
    </div>
</body>
</html>
