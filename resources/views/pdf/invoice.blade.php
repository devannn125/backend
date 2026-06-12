<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $detail->room->hotel->nama_hotel ?? 'Pitullungan.inn' }}</title>
    <style>
        /* Pengaturan Dasar untuk Cetak PDF */
        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            -webkit-print-color-adjust: exact;
        }
        
        /* Container Utama menyerupai kertas */
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 50px 50px 100px 50px;
            position: relative;
            min-height: 100vh;
            box-sizing: border-box;
        }

        /* Bagian Header Atas */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 50px;
        }
        .header-left {
            display: table-cell;
            vertical-align: top;
        }
        .header-right {
            display: table-cell;
            text-align: right;
            vertical-align: top;
        }
        .title {
            font-size: 42px;
            font-weight: 900;
            letter-spacing: 1px;
            margin: 0;
            color: #000000;
        }
        .subtitle {
            font-size: 20px;
            font-weight: 700;
            margin-top: 5px;
            color: #1a1a1a;
        }
        
        /* Ilustrasi Logo bawaan CSS */
        .logo-placeholder {
            display: inline-block;
            width: 70px;
            height: 90px;
            background-color: #0d8f5b;
            border-top-left-radius: 35px;
            border-top-right-radius: 35px;
            position: relative;
        }
        .logo-inner {
            position: absolute;
            bottom: 0;
            left: 15px;
            width: 40px;
            height: 55px;
            background-color: #ffffff;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }
        .logo-door {
            position: absolute;
            bottom: 0;
            left: 13px;
            width: 14px;
            height: 25px;
            background-color: #4a4a4a;
        }

        /* Detail Informasi Pengorder & Hotel */
        .details-container {
            display: table;
            width: 100%;
            margin-bottom: 50px;
        }
        .details-block {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .details-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #000000;
        }
        .details-text {
            font-size: 11px;
            line-height: 1.6;
            color: #4a4a4a;
            margin: 0;
        }

        /* Bagian Tabel Pembelian */
        .purchase-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #000000;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            font-size: 11px;
            margin-bottom: 60px;
        }
        .invoice-table th, .invoice-table td {
            border: 1px solid #bcbcbc;
            padding: 15px 10px;
            vertical-align: middle;
        }
        .invoice-table th {
            font-weight: 700;
            color: #000000;
            background-color: #ffffff;
        }
        .invoice-table td {
            color: #333333;
        }
        .text-left {
            text-align: left;
            padding-left: 15px !important;
        }
        .bg-light-gray {
            background-color: #ffffff;
        }
        .font-bold {
            font-weight: 700;
            color: #000000;
        }

        /* Badge Stempel LINGKARAN PAID Hijau Baru */
        .paid-stamp-container {
            margin-top: 30px;
            margin-left: 15px;
        }
        .paid-stamp {
            display: inline-block;
            width: 90px;
            height: 90px;
            background-color: #0d8f5b; /* Warna Hijau Utama */
            color: #ffffff;            /* Tulisan Putih */
            font-size: 18px;
            font-weight: 800;
            line-height: 90px;         /* Mengunci teks tepat di tengah vertikal */
            text-align: center;        /* Mengunci teks tepat di tengah horizontal */
            border-radius: 50%;        /* Membentuk lingkaran sempurna */
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Footer Bar Hijau di Paling Bawah */
        .footer-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #0d8f5b;
            color: #ffffff;
            text-align: center;
            padding: 15px 0;
            font-size: 11px;
            font-weight: 400;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>

    <div class="invoice-box">
        <div class="header">
            <div class="header-left">
                <h1 class="title">INVOICE</h1>
                <div class="subtitle">Pitullungan.inn</div>
            </div>
            <div class="header-right">
                <div class="logo-placeholder">
                    <div class="logo-inner">
                        <div class="logo-door"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="details-container">
            <div class="details-block">
                <div class="details-title">Orderer Details</div>
                <p class="details-text">
                    <strong>{{ $user->nama ?? 'No Name' }}</strong><br>
                    {{ $user->email ?? '-' }}<br>
                    {{ $user->no_hp ?? '-' }}
                </p>
            </div>
            <div class="details-block">
                <div class="details-title">Hotel Details</div>
                <p class="details-text">
                    {{ $detail->room->hotel->nama_hotel ?? 'Nama Hotel' }}<br>
                    {{ $detail->room->hotel->alamat ?? 'Alamat Hotel' }}
                </p>
            </div>
        </div>

        <div class="purchase-title">Purchase Details</div>
        <table class="invoice-table">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th style="width: 22%;">Hotel Name</th>
                    <th style="width: 35%;">Description</th>
                    <th style="width: 18%;">Unit Price</th>
                    <th style="width: 17%;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td class="text-left">{{ $detail->room->hotel->nama_hotel ?? '-' }}</td>
                    <td class="text-left">
                        {{ $detail->room->roomType->nama_type ?? 'Standard Room' }} - {{ $detail->jumlah_malam ?? 1 }} Night
                    </td>
                    <td>Rp {{ number_format($detail->harga ?? 0, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($booking->total_harga ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right; padding-right: 20px; color: #4a4a4a;">
                        Payment with {{ strtoupper($payment->metode_pembayaran ?? 'QRIS') }}
                    </td>
                    <td class="bg-light-gray font-bold">Total Payment</td>
                    <td class="bg-light-gray font-bold">Rp {{ number_format($booking->total_harga ?? 0, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        @if(($booking->status ?? '') === 'success' || ($payment->status_pembayaran ?? '') === 'success')
        <div class="paid-stamp-container">
            <div class="paid-stamp">PAID</div>
        </div>
        @endif

        <div class="footer-bar">
            Pitullungan.inn
        </div>
    </div>

</body>
</html>