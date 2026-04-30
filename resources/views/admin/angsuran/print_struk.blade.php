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

        /* Header */
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }
        .header h1 { font-size: 14px; margin: 0; }
        .header p { font-size: 10px; margin: 1px 0; }

        /* Tabel Info */
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

        /* QR Section */
        .footer-flex {
            display: flex;
            align-items: center;
            margin-top: 8px;
            padding-bottom: 5px;
        }
        .qr-section {
            flex: 0 0 22mm; 
            text-align: left;
        }
        .qr-section img {
            width: 22mm;
            height: 22mm;
            display: block;
        }
        .thanks-section {
            flex: 1;
            padding-left: 5px;
            font-size: 8.5px;
            line-height: 1.1;
            text-align: left;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 5px;
        }

        .date-line {
            border-top: 1px dashed #ccc;
            padding-top: 2px;
            margin-top: 2px;
            font-size: 8px;
        }

        /* Spacer bawah khusus cetak fisik */
        .print-spacer {
            height: 10mm; 
            display: none;
        }

        /* Pengaturan Cetak */
        @media print {
            @page { size: 58mm auto; margin: 0; }
            body { background: #fff; padding: 0; margin: 0; }
            .receipt-container { width: 58mm; margin: 0; padding: 2mm; box-shadow: none; }
            .button-group { display: none !important; }
            .print-spacer { display: block; }
        }

        /* Tombol UI */
        .button-group { text-align: center; margin-top: 15px; }
        .btn {
            padding: 8px 12px; border: none; border-radius: 4px;
            cursor: pointer; color: #fff; font-weight: bold; margin: 3px;
        }
        .btn-print { background: #333; }
        .btn-download { background: #007bff; }
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

        <div class="footer-flex">
            <div class="qr-section">
                <img crossorigin="anonymous" 
                     src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode('https://kopdes.sibag.us/angsuran/printstruk/' . $tampil['id'] . '/' . $tampil['id_pinjam']) }}" 
                     alt="QR">
            </div>
            <div class="thanks-section">
                <strong>TERIMA KASIH</strong><br>
                Simpan struk ini sebagai bukti pembayaran sah.<br>
                <span style="font-size: 7px;">Validasi Digital YAPUSA</span>
            </div>
        </div>

        <div class="footer">
            <p class="date-line">{{ date('d-m-Y H:i:s') }}</p>
        </div>

        <div class="print-spacer"></div>
    </div>

    <div class="button-group">
        <button class="btn btn-print" onclick="prosesCetak()">Cetak Printer</button>
        <button class="btn btn-download" onclick="prosesDownload()">Download Gambar</button>
        <button class="btn btn-wa" onclick="shareToWA()">WhatsApp</button>
    </div>

    <script>
    // 1. Fungsi KHUSUS PRINTER
    function prosesCetak() {
        window.print();
    }

    // 2. Fungsi KHUSUS DOWNLOAD IMAGE
    function prosesDownload() {
        const area = document.getElementById('receiptArea');
        const spacer = document.querySelector('.print-spacer');
        
        // Sembunyikan spacer agar gambar PNG tidak kepanjangan di bawah
        if (spacer) spacer.style.display = 'none';

        html2canvas(area, { 
            scale: 3, 
            useCORS: true,
            logging: false 
        }).then(canvas => {
            if (spacer) spacer.style.display = ''; // Kembalikan default
            
            const imgData = canvas.toDataURL('image/png');
            const link = document.createElement('a');
            link.download = 'Struk-{{ $tampil["nama"] }}.png';
            link.href = imgData;
            link.click();
        });
    }

    // 3. Fungsi WHATSAPP (Download + Link WA)
    function shareToWA() {
        const area = document.getElementById('receiptArea');
        const spacer = document.querySelector('.print-spacer');
        if (spacer) spacer.style.display = 'none';

        html2canvas(area, { scale: 3, useCORS: true }).then(canvas => {
            if (spacer) spacer.style.display = ''; 
            
            // Download Image Otomatis
            const imgData = canvas.toDataURL('image/png');
            const link = document.createElement('a');
            link.download = 'Struk-{{ $tampil["nama"] }}.png';
            link.href = imgData;
            link.click();

            // Direct ke WA
            let text = encodeURIComponent("Halo {{ $tampil['nama'] }}, ini bukti angsuran Anda. Terimakasih.");
            window.open("https://wa.me/{{ $tampil['no_tlp'] }}?text=" + text, "_blank");
        });
    }
    </script>
</body>

</html>