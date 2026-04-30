<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Struk - {{ $tampil['id_pinjam'] }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        /* Reset & Base */
        * { box-sizing: border-box; }
        body {
            font-family: 'Courier New', Courier, monospace;
            background: #eee;
            margin: 0;
            padding: 10px;
            color: #000;
        }

        /* Container Struk */
        .receipt-container {
            width: 58mm;
            background: #fff;
            padding: 2mm;
            margin: 0 auto;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        /* Header rapi & hemat ruang */
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }
        .header h1 { font-size: 14px; margin: 0; }
        .header p { font-size: 10px; margin: 1px 0; }

        /* Tabel Info agar titik dua sejajar */
        .info-table {
            width: 100%;
            font-size: 10px;
            border-collapse: collapse;
        }
        .info-table td { padding: 2px 0; vertical-align: top; }
        .label { width: 35%; }
        .sep { width: 5%; }
        .val { width: 60%; font-weight: bold; text-align: right; }

        /* Highlight Total */
        .total-box {
            margin-top: 6px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 4px 0;
            font-size: 11px;
            font-weight: bold;
        }

        .qr-section {
            text-align: center;
            margin: 10px 0;
        }
        .qr-section img {
            width: 30mm; /* Ukuran optimal 58mm */
            height: 30mm;
            display: inline-block;
        }

        .footer {
            text-align: center;
            font-size: 9px;
            line-height: 1.2;
        }

        /* Spacer bawah agar tidak terpotong saat sobek kertas */
        .print-spacer {
            height: 25mm; 
            display: none; /* Hanya muncul saat print */
        }

        /* Pengaturan Cetak */
        @media print {
            @page { size: 58mm auto; margin: 0; }
            body { background: #fff; padding: 0; margin: 0; }
            .receipt-container { width: 58mm; margin: 0; padding: 2mm; box-shadow: none; }
            .button-group { display: none; }
            .print-spacer { display: block; }
        }

        /* Tombol UI */
        .button-group { text-align: center; margin-top: 15px; }
        .btn {
            padding: 8px 12px; border: none; border-radius: 4px;
            cursor: pointer; color: #fff; font-weight: bold; margin: 3px;
        }
        .btn-print { background: #333; }
        .btn-wa { background: #25D366; }
    </style>
</head>

<body>

    <div class="receipt-container" id="receiptArea">
        <div class="header">
            <h1>YAPUSA</h1>
            <p>Koperasi Simpan Pinjam</p>
            <p>** BUKTI BAYAR ANGSURAN **</p>
        </div>

        <table class="info-table">
            <tr>
                <td class="label">No.Pinjam</td>
                <td class="sep">:</td>
                <td class="val">{{ $tampil['id_pinjam'] }}</td>
            </tr>
            <tr>
                <td class="label">Nama</td>
                <td class="sep">:</td>
                <td class="val">{{ $tampil['nama'] }}</td>
            </tr>
            <tr>
                <td class="label">Tenor</td>
                <td class="sep">:</td>
                <td class="val">{{ $tampil['ke'] }} / {{ $tampil['angsuran'] }}</td>
            </tr>
            <tr>
                <td class="label">Waktu</td>
                <td class="sep">:</td>
                <td class="val">{{ date('d/m/y H:i', strtotime($tampil['tgl'])) }}</td>
            </tr>
        </table>

        <div class="total-box">
            <span style="float: left;">TOTAL BAYAR</span>
            <span style="float: right;">Rp {{ number_format($tampil['nominal'], 0, ',', '.') }}</span>
            <div style="clear: both;"></div>
        </div>

        <div class="qr-section">
            <img crossorigin="anonymous" 
                 src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $tampil['id_pinjam'] }}|{{ $tampil['ke'] }}" 
                 alt="QR Code">
            <p style="font-size: 8px; margin-top: 4px;">Validasi Digital YAPUSA</p>
        </div>

        <div class="footer">
            <p>TERIMA KASIH<br>Simpan struk ini sebagai bukti sah.</p>
            <p style="border-top: 1px dashed #ccc; padding-top: 3px;">{{ date('d-m-Y H:i:s') }}</p>
        </div>

        <div class="print-spacer"></div>
    </div>

    <div class="button-group">
        <button class="btn btn-print" onclick="window.print()">Cetak Struk</button>
        <button class="btn btn-wa" onclick="shareToWA()">WhatsApp</button>
    </div>

    <script>
        function shareToWA() {
            const area = document.getElementById('receiptArea');
            // Menghilangkan spacer saat ambil screenshot untuk WA
            const spacer = document.querySelector('.print-spacer');
            spacer.style.display = 'none';

            html2canvas(area, { scale: 2, useCORS: true }).then(canvas => {
                spacer.style.display = ''; // Kembalikan display spacer
                const imgData = canvas.toDataURL('image/png');
                const link = document.createElement('a');
                link.download = 'Struk-{{ $tampil["nama"] }}.png';
                link.href = imgData;
                link.click();

                let text = encodeURIComponent("Halo {{ $tampil['nama'] }}, ini bukti angsuran ke-{{ $tampil['ke'] }}. Terimakasih.");
                window.open("https://wa.me/{{ $tampil['no_tlp'] }}?text=" + text, "_blank");
            });
        }
    </script>
</body>

</html>