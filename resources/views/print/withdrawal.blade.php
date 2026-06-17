<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kuitansi Penarikan Saldo #{{ $withdrawal->id }}</title>
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
                <td>ID Penarikan:</td>
                <td class="right">#{{ $withdrawal->id }}</td>
            </tr>
            <tr>
                <td>Tanggal:</td>
                <td class="right">{{ $withdrawal->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td>Nasabah:</td>
                <td class="right">{{ $withdrawal->user->name }}</td>
            </tr>
            <tr>
                <td>No. Rek:</td>
                <td class="right">{{ $withdrawal->user->account_no }}</td>
            </tr>
            <tr>
                <td>Status:</td>
                <td class="right">{{ strtoupper($withdrawal->status) }}</td>
            </tr>
        </table>
    </div>

    <div class="divider"></div>

    <div class="details">
        <table>
            <tr>
                <td>Bank/E-Wallet:</td>
                <td class="right">{{ $withdrawal->bank_name }}</td>
            </tr>
            <tr>
                <td>No. Rek/HP:</td>
                <td class="right">{{ $withdrawal->account_number }}</td>
            </tr>
            <tr>
                <td>Penerima:</td>
                <td class="right">{{ $withdrawal->account_name }}</td>
            </tr>
        </table>
    </div>

    <div class="divider"></div>

    <div class="details">
        <table>
            <tr style="font-weight: bold; font-size: 14px;">
                <td>JUMLAH TARIK:</td>
                <td class="right">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="divider"></div>

    <div class="footer">
        <p>Penarikan saldo berhasil diproses.</p>
        <p>Simpan kuitansi ini sebagai bukti pencairan yang sah.</p>
        <p>Petugas: {{ $withdrawal->validator->name ?? '-' }}</p>
    </div>
</body>
</html>
