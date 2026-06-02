<?php

namespace App\Services;

use App\Models\Bimtek;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SertifikatTemplateService
{
    /**
     * Path to standard template
     */
    const STANDARD_TEMPLATE_PATH = 'resources/templates/sertifikat_standar.docx';

    /**
     * Available placeholders and their descriptions
     */
    public static function getAvailablePlaceholders(): array
    {
        return [
            '${NAMA_PESERTA}' => 'Nama lengkap peserta (uppercase)',
            '${NIP}' => 'NIP peserta',
            '${INSTANSI}' => 'Instansi/Dinas peserta',
            '${NOMOR_SERTIFIKAT}' => 'Nomor sertifikat unik',
            '${JUDUL_BIMTEK}' => 'Judul kegiatan bimtek',
            '${TANGGAL_MULAI}' => 'Tanggal mulai (format: d F Y)',
            '${TANGGAL_SELESAI}' => 'Tanggal berakhir (format: d F Y)',
            '${LOKASI}' => 'Lokasi penyelenggaraan',
            '${TANGGAL_TERBIT}' => 'Tanggal penerbitan sertifikat (format: d F Y)',
        ];
    }

    /**
     * Extract text content from DOCX file for rendering template
     * DOCX files are ZIP archives containing XML files
     */
    public static function extractFromDocx(string $docxPath): string
    {
        if (! file_exists($docxPath)) {
            return '';
        }

        try {
            $zip = new \ZipArchive;
            if ($zip->open($docxPath) !== true) {
                return '';
            }

            // Read document.xml from DOCX (main document content)
            $xml = $zip->getFromName('word/document.xml');
            $zip->close();

            if (! $xml) {
                return '';
            }

            // Parse XML and extract text
            $dom = new \DOMDocument;
            @$dom->loadXML($xml);

            // Extract all text from <w:t> elements (Word text elements)
            $xpath = new \DOMXPath($dom);
            $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

            $textNodes = $xpath->query('//w:t');
            $text = '';

            foreach ($textNodes as $node) {
                $text .= $node->nodeValue;
            }

            return $text;
        } catch (\Exception $e) {
            Log::error('Error extracting DOCX: '.$e->getMessage());

            return '';
        }
    }

    /**
     * Render standard template as HTML with data replacement
     * Uses professional HTML layout that matches the DOCX template design
     */
    public static function renderAsHtml($peserta, Bimtek $bimtek, string $nomorSertifikat, string $tanggalTerbit): string
    {
        $tanggalTerbitDate = Carbon::parse($tanggalTerbit)->locale('id');

        $nama = strtoupper($peserta->name);
        $nip = $peserta->nip ?? '-';
        $instansi = $peserta->instansi ?? '-';
        $judul = $bimtek->judul_final;
        $tglMulai = $bimtek->tanggal_mulai_aktual?->locale('id')->translatedFormat('d F Y') ?? '-';
        $tglSelesai = $bimtek->tanggal_selesai_aktual?->locale('id')->translatedFormat('d F Y') ?? '-';
        $lokasi = $bimtek->lokasi_aktual ?? '-';
        $tglTerbit = $tanggalTerbitDate->translatedFormat('d F Y');

        $logoBase64 = '';
        $logoPath = public_path('images/logo-bbpmp.png');
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath));
        }

        $logoHtml = $logoBase64 !== ''
            ? '<div class="logo-wrap"><img src="'.$logoBase64.'" alt="Logo BBPMP"></div>'
            : '';

        return <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat - {$nama}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page { size: A4 landscape; margin: 10mm; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #1f2937;
        }
        .certificate {
            width: 277mm;
            min-height: 186mm;
            margin: 0 auto;
            background: white;
            padding: 12mm 8mm 2mm 8mm;
            display: flex;
            flex-direction: column;
        }
        .content {
            width: 100%;
        }
        
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 4mm;
            padding-bottom: 3mm;
            border-bottom: 2px solid #003366;
        }
        .logo-wrap {
            text-align: center;
            margin-bottom: 2.5mm;
        }
        .logo-wrap img {
            width: 18mm;
            height: 18mm;
            object-fit: contain;
        }
        .org-name {
            font-size: 14px;
            font-weight: bold;
            color: #003366;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .org-sub {
            font-size: 13px;
            font-weight: bold;
            color: #003366;
            margin-bottom: 2px;
        }
        .year {
            font-size: 10px;
            color: #666;
        }
        
        /* Title */
        .title {
            text-align: center;
            font-size: 46px;
            font-weight: bold;
            color: #0066CC;
            margin: 1mm 0 1mm 0;
            letter-spacing: 1px;
        }
        .divider {
            width: 100px;
            margin: 0 auto 2.5mm auto;
            border-top: 2px solid #0066CC;
        }
        .cert-number {
            text-align: center;
            color: #003366;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 3mm;
        }
        
        /* Main Content */
        .intro {
            text-align: center;
            font-size: 13px;
            margin-bottom: 1.5mm;
        }
        .recipient-name {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            color: #0f172a;
            text-decoration: underline;
            margin: 1mm 0 3mm 0;
            letter-spacing: 0.5px;
        }
        
        /* Details Table */
        .details {
            margin: 0 auto 3mm auto;
            font-size: 13px;
            text-align: center;
            line-height: 1.5;
        }
        .detail-line {
            margin: 0.8mm 0;
        }
        .detail-label {
            font-weight: 700;
            color: #333;
        }
        .detail-value {
            color: #333;
        }
        
        /* Certificate Body */
        .body {
            text-align: center;
            margin: 0 auto;
            max-width: 94%;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        /* Signature Section */
        .signature-section {
            margin-top: auto;
            padding-top: 6mm;
            text-align: center;
        }
        .date-issued {
            font-size: 12px;
            margin-bottom: 2.5mm;
            color: #333;
        }
        .signature-title {
            font-size: 12px;
            color: #333;
            margin-bottom: 1mm;
        }
        .signature-placeholder {
            font-size: 9px;
            color: #999;
            font-style: italic;
            margin-bottom: 10mm;
        }
        .signature-name {
            font-size: 12px;
            font-weight: bold;
            color: #003366;
            line-height: 1.4;
        }
        
    </style>
</head>
<body>
    <div class="certificate">
        <div class="content">
        <!-- Header -->
        <div class="header">
            {$logoHtml}
            <div class="org-name">Balai Besar Penjaminan Mutu Pendidikan</div>
            <div class="org-sub">BBPMP Sumatera Barat</div>
            <div class="year">Tahun 2026</div>
        </div>
        
        <!-- Title -->
        <div class="title">SERTIFIKAT</div>
        <div class="divider"></div>
        <div class="cert-number">Nomor Sertifikat: {$nomorSertifikat}</div>
        
        <!-- Main Content -->
        <div class="intro">Menyatakan bahwa</div>
        <div class="recipient-name">{$nama}</div>
        
        <!-- Details -->
        <div class="details">
            <div class="detail-line"><span class="detail-label">NIP:</span> <span class="detail-value">{$nip}</span></div>
            <div class="detail-line"><span class="detail-label">Instansi:</span> <span class="detail-value">{$instansi}</span></div>
        </div>
        
        <!-- Body -->
        <div class="body">
            <p>Telah mengikuti kegiatan bimtek berjudul <strong>{$judul}</strong> yang diselenggarakan oleh Balai Besar Penjaminan Mutu Pendidikan Sumatera Barat, dari tanggal <strong>{$tglMulai}</strong> sampai dengan <strong>{$tglSelesai}</strong> di <strong>{$lokasi}</strong>.</p>
        </div>
        
        <!-- Signature -->
        <div class="signature-section">
            <div class="date-issued">Padang, {$tglTerbit}</div>
            <div class="signature-title">Kepala BBPMP Provinsi Sumatera Barat</div>
            <div class="signature-placeholder">[Tanda Tangan Digital / Stempel]</div>
            <div class="signature-name">Dr. H. Muslihuddin, M.Pd<br>NIP. 197104102002121001</div>
        </div>

        </div>
    </div>
</body>
</html>
HTML;
    }
}
