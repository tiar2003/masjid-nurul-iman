<?php

namespace App\Services;

class WordDocumentGenerator
{
  public function generateFromTemplate(string $templatePath, array $values): string
  {
    if (! is_file($templatePath)) {
      throw new \RuntimeException('File template DOCX tidak ditemukan.');
    }

    $tempFile = tempnam(sys_get_temp_dir(), 'masjid_template_');
    if ($tempFile === false || ! copy($templatePath, $tempFile)) {
      throw new \RuntimeException('Tidak dapat menyalin template DOCX.');
    }

    $zip = new \ZipArchive();
    if ($zip->open($tempFile) !== true) {
      unlink($tempFile);
      throw new \RuntimeException('Template DOCX tidak dapat dibuka.');
    }

    for ($index = 0; $index < $zip->numFiles; $index++) {
      $entryName = $zip->getNameIndex($index);
      if (! is_string($entryName) || ! preg_match('#^word/(document|header\d+|footer\d+)\.xml$#', $entryName)) {
        continue;
      }

      $xml = $zip->getFromIndex($index);
      if ($xml === false) {
        continue;
      }

      $xml = $this->replacePlaceholders($xml, $values);
      if (($values['_template_key'] ?? null) === 'ZAKAT_EDARAN_2025') {
        $xml = $this->replaceZakatCircularValues($xml, $values);
      }
      $zip->addFromString($entryName, $xml);
    }

    $this->replaceOfficialHeaders($zip);

    $zip->close();

    return $tempFile;
  }

    public function generateFromText(string $text, string $filename): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'masjid_docx_');
        if ($tempFile === false) {
            throw new \RuntimeException('Tidak dapat membuat file sementara DOCX.');
        }

        $zip = new \ZipArchive();
        $opened = $zip->open($tempFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        if ($opened !== true) {
            unlink($tempFile);
            throw new \RuntimeException('Tidak dapat membuat arsip DOCX.');
        }

        $xml = $this->buildDocumentXml($text);

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml());
        $zip->addFromString('_rels/.rels', $this->relsXml());
        $zip->addFromString('docProps/core.xml', $this->coreXml());
        $zip->addFromString('docProps/app.xml', $this->appXml());
        $zip->addFromString('word/document.xml', $xml);
        $zip->addFromString('word/styles.xml', $this->stylesXml());
        $zip->addFromString('word/_rels/document.xml.rels', $this->documentRelsXml());
        $zip->close();

        return $tempFile;
    }

      private function replacePlaceholders(string $xml, array $values): string
      {
        foreach ($values as $key => $value) {
          $escapedValue = htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
          foreach (['{{' . $key . '}}', '{' . $key . '}'] as $placeholder) {
            $xml = str_replace($placeholder, $escapedValue, $xml);
            $characters = preg_split('//u', $placeholder, -1, PREG_SPLIT_NO_EMPTY);
            $pattern = implode('(?:<[^>]+>)*', array_map(static fn ($character) => preg_quote($character, '/'), $characters));
            $xml = preg_replace('/' . $pattern . '/u', $escapedValue, $xml) ?? $xml;
          }
        }

        return $xml;
      }

      private function replaceZakatCircularValues(string $xml, array $values): string
      {
        $issueDate = $this->indonesianDate($values['issue_date'] ?? null);
        $periodEnd = $this->indonesianDate($values['period_end'] ?? null);
        $hijriYear = trim((string) ($values['hijri_year'] ?? ''));
        $hijriYear = preg_replace('/\s*H$/i', '', $hijriYear) . ' H';
        $replacements = [
          'Semarang, 10 Maret 2026' => 'Semarang, ' . $issueDate,
          '10/IX / Pan – Ramadhan / MNI / 26' => (string) ($values['letter_number'] ?? ''),
          '1447 H' => $hijriYear,
          '3 kg perjiwa' => $this->numberValue($values['fitrah_kg_per_person'] ?? null) . ' kg perjiwa',
          '2,5 %' => $this->numberValue($values['zakat_mal_rate_percent'] ?? null) . ' %',
          '2,8 kg' => $this->numberValue($values['fidyah_kg_per_day'] ?? null) . ' kg',
          '50.000' => number_format((float) ($values['fidyah_money_per_day'] ?? 0), 0, ',', '.'),
          '20.00-22.00 WIB' => (string) ($values['service_hours'] ?? ''),
          'Selasa tanggal 17 Maret 2026' => $this->indonesianWeekday($values['period_end'] ?? null) . ' tanggal ' . $periodEnd,
          'malam 29 Ramadhan 1447 H' => (string) ($values['closing_hijri_day'] ?? '') . ' ' . $hijriYear,
          'Masjid Nurul Iman RW IX Kel. Krapyak' => (string) ($values['venue'] ?? ''),
            'H. Sugeng Tiyarto, SH.MH' => (string) ($values['chairperson_name'] ?? ''),
            'Awaludin Gymnastiar' => (string) ($values['secretary_name'] ?? ''),
        ];

        foreach ($replacements as $from => $to) {
          if ($to !== '') {
            $xml = $this->replaceTextFragment($xml, $from, $to);
          }
        }

        return $xml;
      }

      private function replaceTextFragment(string $xml, string $from, string $to): string
      {
        $escaped = htmlspecialchars($to, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $characters = preg_split('//u', $from, -1, PREG_SPLIT_NO_EMPTY);
        $pattern = implode('(?:<[^>]+>)*', array_map(static fn ($character) => preg_quote($character, '/'), $characters));

        return preg_replace('/' . $pattern . '/u', $escaped, $xml) ?? $xml;
      }

      private function indonesianDate(?string $date): string
      {
        if (blank($date)) {
          return '';
        }

        $months = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        $timestamp = strtotime($date);
        return date('d', $timestamp) . ' ' . $months[(int) date('n', $timestamp)] . ' ' . date('Y', $timestamp);
      }

      private function indonesianWeekday(?string $date): string
      {
        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        return blank($date) ? '' : $days[(int) date('w', strtotime($date))];
      }

      private function numberValue($value): string
      {
        $formatted = number_format((float) $value, 2, '.', '');
        return rtrim(rtrim($formatted, '0'), '.');
      }

    private function replaceOfficialHeaders(\ZipArchive $zip): void
    {
        $archivePath = storage_path('app/referensi-masjid/MASJID.zip');
        if (! is_file($archivePath)) {
            return;
        }

        $archive = new \ZipArchive();
        if ($archive->open($archivePath) !== true) {
            return;
        }

        $letterhead = $archive->getFromName('MASJID/KOP FIX.png');
        $archive->close();
        if ($letterhead === false) {
            return;
        }

        for ($index = 0; $index < $zip->numFiles; $index++) {
            $entryName = $zip->getNameIndex($index);
            if (! is_string($entryName) || ! preg_match('#^word/header\d+\.xml$#', $entryName)) {
                continue;
            }

            $headerNumber = preg_replace('/^word\/(header\d+)\.xml$/', '$1', $entryName);
            $relationshipPath = 'word/_rels/' . $headerNumber . '.xml.rels';
            $zip->addFromString('word/media/masjid-kop-fix.png', $letterhead);
            $zip->addFromString($entryName, $this->officialHeaderXml());
            $zip->addFromString($relationshipPath, $this->officialHeaderRelationships());
        }
    }

    private function officialHeaderXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:hdr xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing">
  <w:p>
    <w:pPr><w:jc w:val="center"/></w:pPr>
    <w:r>
      <w:drawing>
        <wp:inline distT="0" distB="0" distL="0" distR="0">
          <wp:extent cx="5486400" cy="910635"/>
          <wp:docPr id="1" name="KOP FIX" descr="Kop resmi Masjid Nurul Iman"/>
          <a:graphic>
            <a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture">
              <pic:pic>
                <pic:nvPicPr><pic:cNvPr id="0" name="masjid-kop-fix.png"/><pic:cNvPicPr/></pic:nvPicPr>
                <pic:blipFill><a:blip r:embed="rId1"/><a:stretch><a:fillRect/></a:stretch></pic:blipFill>
                <pic:spPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="5486400" cy="910635"/></a:xfrm><a:prstGeom prst="rect"><a:avLst/></a:prstGeom></pic:spPr>
              </pic:pic>
            </a:graphicData>
          </a:graphic>
        </wp:inline>
      </w:drawing>
    </w:r>
  </w:p>
</w:hdr>
XML;
    }

    private function officialHeaderRelationships(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/masjid-kop-fix.png"/>
</Relationships>
XML;
    }

    private function buildDocumentXml(string $text): string
    {
        $content = htmlspecialchars($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $content = str_replace(["\r\n", "\r", "\n"], "\n", $content);

        $paragraphs = array_values(array_filter(preg_split('/\n/', $content) ?: [], static fn ($line) => trim($line) !== ''));
        $bodyXml = '';

        if ($paragraphs === []) {
            $paragraphs = [''];
        }

        foreach ($paragraphs as $paragraph) {
            $bodyXml .= '<w:p><w:r><w:t xml:space="preserve">' . $paragraph . '</w:t></w:r></w:p>';
        }

        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:wpc="http://schemas.microsoft.com/office/word/2010/wordprocessingCanvas" xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:wp14="http://schemas.microsoft.com/office/word/2010/wordprocessingDrawing" xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing" xmlns:w10="urn:schemas-microsoft-com:office:word" xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" xmlns:w14="http://schemas.microsoft.com/office/word/2010/wordml" xmlns:w15="http://schemas.microsoft.com/office/word/2012/wordml" xmlns:wpg="http://schemas.microsoft.com/office/word/2010/wordprocessingGroup" xmlns:wpi="http://schemas.microsoft.com/office/word/2010/wordprocessingInk" xmlns:wne="http://schemas.microsoft.com/office/word/2006/wordml" xmlns:wps="http://schemas.microsoft.com/office/word/2010/wordprocessingShape" mc:Ignorable="w14 w15 wp14">
  <w:body>
    {$bodyXml}
    <w:sectPr>
      <w:pgSz w:w="12240" w:h="15840"/>
      <w:pgMar w:top="1440" w:right="1440" w:bottom="1440" w:left="1440" w:header="720" w:footer="720" w:gutter="0"/>
    </w:sectPr>
  </w:body>
</w:document>
XML;
    }

    private function stylesXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:style w:type="paragraph" w:default="1" w:styleId="Normal">
    <w:name w:val="Normal"/>
    <w:qFormat/>
    <w:rPr>
      <w:rFonts w:ascii="Calibri" w:hAnsi="Calibri"/>
      <w:sz w:val="22"/>
    </w:rPr>
  </w:style>
</w:styles>
XML;
    }

    private function contentTypesXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
  <Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>
  <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
  <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
</Types>
XML;
    }

    private function relsXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
</Relationships>
XML;
    }

    private function coreXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <dc:title>Generated document</dc:title>
  <dc:creator>Masjid Nurul Iman</dc:creator>
  <cp:lastModifiedBy>Masjid Nurul Iman</cp:lastModifiedBy>
  <dcterms:created xsi:type="dcterms:W3CDTF">2026-01-01T00:00:00Z</dcterms:created>
  <dcterms:modified xsi:type="dcterms:W3CDTF">2026-01-01T00:00:00Z</dcterms:modified>
</cp:coreProperties>
XML;
    }

    private function appXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">
  <Application>Microsoft Office Word</Application>
</Properties>
XML;
    }

    private function documentRelsXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>
XML;
    }
}
