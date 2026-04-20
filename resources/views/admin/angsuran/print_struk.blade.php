<!DOCTYPE html>
<html>

<head>
    <title>Cetak Struk - {{ $tampil['id_pinjam'] }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        /* Ukuran Thermal 58mm */
        body {
            font-family: 'Courier New', Courier, monospace;
            background: #eee;
            margin: 0;
            padding: 0;
        }

        .receipt-container {
            width: 58mm;
            background: #fff;
            padding: 5mm;
            margin: 0 auto;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 3px;
            margin-bottom: 5px;
        }

        .header h1 {
            font-size: 16px;
            margin: 0;
        }

        .header p {
            font-size: 10px;
            margin: 2px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin: 3px 0;
        }

        .info-label {
            color: #555;
        }

        .info-value {
            text-align: right;
            font-weight: bold;
        }

        .highlight {
            font-size: 13px;
        }

        .barcode-container {
            text-align: center;
            margin-top: 10px;
        }

        .footer {
            text-align: center;
            font-size: 9px;
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 5px;
        }

        /* Sembunyikan tombol saat print kertas */
        @media print {
            .button-group {
                display: none;
            }

            body {
                background: #fff;
            }

            .receipt-container {
                box-shadow: none;
                width: 100%;
            }
        }

        .button-group {
            text-align: center;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: #fff;
            margin: 5px;
            font-size: 12px;
        }

        .btn-print {
            background: #333;
        }

        .btn-wa {
            background: #25D366;
        }
    </style>
</head>

<body>

    <div class="receipt-container" id="receiptArea">
        <div class="header">
            <h1>YAPUSA</h1>
            <p>Koperasi Simpan Pinjam</p>
            <p>*** BUKTI PEMBAYARAN ***</p>
        </div>

        <div class="info-row">
            <span class="info-label">No. Pinjam:</span>
            <span class="info-value">{{ $tampil['id_pinjam'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Nama:</span>
            <span class="info-value">{{ $tampil['nama'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Cicilan Ke:</span>
            <span class="info-value">{{ $tampil['ke'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal:</span>
            <span class="info-value">{{ date('d/m/y H:i', strtotime($tampil['tgl'])) }}</span>
        </div>
        <div class="info-row highlight" style="margin-top: 5px; border-top: 1px solid #000;">
            <span class="info-label">TOTAL:</span>
            <span class="info-value">Rp {{ number_format($tampil['nominal'], 0, ',', '.') }}</span>
        </div>

        <div class="barcode-container">
            <img src="https://chart.googleapis.com/chart?chs=100x100&cht=qr&chl={{ $tampil['id_pinjam'] }}" style="width: 80px;">
        </div>

        <div class="footer">
            <p>Simpan bukti ini sebagai<br>alat bukti pembayaran sah.<br>Terima Kasih</p>
        </div>
    </div>

    <div class="button-group">
        <button class="btn btn-print" onclick="window.print()">Cetak Kertas</button>
        <button class="btn btn-wa" onclick="shareToWA()">Share ke WhatsApp</button>
    </div>

    <script>
        function shareToWA() {
            const area = document.getElementById('receiptArea');
            html2canvas(area).then(canvas => {
                // Konversi ke Base64 Image
                const imgData = canvas.toDataURL('image/png');

                // Karena WA API tidak bisa langsung kirim file gambar via Link, 
                // Opsinya adalah download gambar atau arahkan ke teks.
                // UNTUK SHARE GAMBAR: User harus download dulu lalu attach di WA.

                const link = document.createElement('a');
                link.download = 'Struk-{{ $tampil["nama"] }}.png';
                link.href = imgData;
                link.click();

                // Beritahu user
                alert("Gambar struk berhasil di-download. Silahkan lampirkan ke WhatsApp nasabah.");

                let text = encodeURIComponent("Halo {{ $tampil['nama'] }}, berikut bukti pembayaran angsuran ke-{{ $tampil['ke'] }} Anda.");
                window.open("https://wa.me/{{ $tampil['no_tlp'] }}?text=" + text, "_blank");
            });
        }
    </script>
</body>

</html>