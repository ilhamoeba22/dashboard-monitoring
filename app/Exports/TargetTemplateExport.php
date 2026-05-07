<?php

declare(strict_types=1);

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TargetTemplateExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    /**
     * Return an empty collection as requested (only headers and notes)
     * @return Collection
     */
    public function collection(): Collection
    {
        return collect([]);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'TIPE_DIMENSI',
            'KODE_DIMENSI',
            'NAMA_DIMENSI',
            'TAHUN',
            'JAN',
            'FEB',
            'MAR',
            'APR',
            'MEI',
            'JUN',
            'JUL',
            'AGS',
            'SEP',
            'OKT',
            'NOV',
            'DES',
            'PETUNJUK_PENGISIAN (MOHON DIBACA)'
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // 1. Header Styling
        $sheet->getStyle('1')->getFont()->setBold(true);
        $sheet->getStyle('A1:P1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('F3F4F6');

        // 2. Note Column Styling (Column Q)
        $sheet->getStyle('Q1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FEF3C7'); // Amber 100 for Attention
        $sheet->getStyle('Q1')->getFont()->getColor()->setARGB('92400E'); // Amber 800

        // 3. Add Instructions to specific cells in Column Q
        $instructions = [
            "1. TIPE_DIMENSI: Isi dengan 'ao', 'cabang', atau 'produk'.",
            "2. KODE_DIMENSI: Isi dengan Kode unik (kdao/kdloc/kdprd).",
            "3. NAMA_DIMENSI: Nama deskriptif (Hanya untuk referensi).",
            "4. TAHUN: Format YYYY (contoh: 2026).",
            "5. JAN s/d DES: Isi dengan angka nominal murni.",
            "   PENTING: Jangan gunakan titik (.) atau koma (,) sebagai pemisah ribuan.",
            "6. Satu baris mewakili satu dimensi untuk satu tahun penuh.",
            "7. Simpan file sebagai .xlsx sebelum di-upload."
        ];

        foreach ($instructions as $index => $text) {
            $row = $index + 2;
            $sheet->setCellValue("Q$row", $text);
            $sheet->getStyle("Q$row")->getFont()->setItalic(true);
            $sheet->getStyle("Q$row")->getFont()->setSize(9);
            $sheet->getStyle("Q$row")->getFont()->getColor()->setARGB('4B5563'); // Slate 600
        }

        // 4. Global Alignment
        $sheet->getStyle($sheet->calculateWorksheetDimension())
            ->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
        // 5. Freeze Pane for Header
        $sheet->freezePane('A2');

        return [
            1 => ['font' => ['size' => 11]],
        ];
    }
}
