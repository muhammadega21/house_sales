<?php

declare(strict_types=1);

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Main export class – menggunakan multiple sheets.
 */
class LaporanPenjualanExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(
        private readonly array $filters = []
    ) {}

    /** @return array<int, object> */
    public function sheets(): array
    {
        return [
            new LaporanPenjualanRingkasanSheet($this->filters),
            new LaporanPenjualanDetailSheet($this->filters),
            new LaporanPenjualanBreakdownSheet($this->filters),
        ];
    }
}

// =============================================================================
// Sheet 1 – Ringkasan
// =============================================================================

class LaporanPenjualanRingkasanSheet implements
    FromCollection,
    WithTitle,
    WithHeadings,
    ShouldAutoSize,
    WithStyles,
    WithEvents
{
    use Exportable;

    private array $summaryData = [];
    private array $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Ringkasan';
    }

    public function headings(): array
    {
        return ['Metrik', 'Nilai'];
    }

    public function collection(): Collection
    {
        $start = isset($this->filters['periode_mulai'])
            ? Carbon::parse($this->filters['periode_mulai'])->startOfDay()
            : Carbon::now()->startOfMonth();

        $end = isset($this->filters['periode_selesai'])
            ? Carbon::parse($this->filters['periode_selesai'])->endOfDay()
            : Carbon::now()->endOfMonth();

        $allowedStatus = ['akad', 'serah_terima'];
        if (!empty($this->filters['status']) && in_array($this->filters['status'], $allowedStatus, true)) {
            $allowedStatus = [$this->filters['status']];
        }

        $query = DB::table('status_penjualan as sp')
            ->join('unit_rumah as u', 'u.id', '=', 'sp.id_unit')
            ->join('booking as b', 'b.id', '=', 'sp.id_booking')
            ->whereIn('sp.status_saat_ini', $allowedStatus)
            ->whereBetween('sp.tanggal_perubahan', [$start, $end]);

        if (!empty($this->filters['id_perumahan'])) {
            $query->where('u.id_perumahan', (int) $this->filters['id_perumahan']);
        }
        if (!empty($this->filters['kategori'])) {
            $query->where('u.kategori', $this->filters['kategori']);
        }
        if (!empty($this->filters['id_marketing'])) {
            $query->where('b.id_marketing', (int) $this->filters['id_marketing']);
        }

        $rows             = $query->select('u.harga_jual', 'u.kategori')->get();
        $totalUnit        = $rows->count();
        $totalNilai       = (float) $rows->sum('harga_jual');
        $rataRata         = $totalUnit > 0 ? $totalNilai / $totalUnit : 0;
        $unitSubsidi      = $rows->where('kategori', 'subsidi')->count();
        $nilaiSubsidi     = (float) $rows->where('kategori', 'subsidi')->sum('harga_jual');
        $unitNonSubsidi   = $rows->where('kategori', 'non_subsidi')->count();
        $nilaiNonSubsidi  = (float) $rows->where('kategori', 'non_subsidi')->sum('harga_jual');

        return collect([
            ['Periode Laporan', $start->format('d/m/Y') . ' – ' . $end->format('d/m/Y')],
            ['Dihasilkan Pada', now()->format('d/m/Y H:i')],
            ['', ''],
            ['Total Unit Terjual', $totalUnit],
            ['Total Nilai Penjualan', 'Rp ' . number_format($totalNilai, 0, ',', '.')],
            ['Rata-rata Harga per Unit', 'Rp ' . number_format($rataRata, 0, ',', '.')],
            ['', ''],
            ['--- Kategori Subsidi ---', ''],
            ['  Unit Subsidi Terjual', $unitSubsidi],
            ['  Total Nilai Subsidi', 'Rp ' . number_format($nilaiSubsidi, 0, ',', '.')],
            ['', ''],
            ['--- Kategori Non-Subsidi ---', ''],
            ['  Unit Non-Subsidi Terjual', $unitNonSubsidi],
            ['  Total Nilai Non-Subsidi', 'Rp ' . number_format($nilaiNonSubsidi, 0, ',', '.')],
        ]);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getColumnDimension('A')->setWidth(40);
                $event->sheet->getColumnDimension('B')->setWidth(30);
            },
        ];
    }
}

// =============================================================================
// Sheet 2 – Detail Penjualan
// =============================================================================

class LaporanPenjualanDetailSheet implements
    FromCollection,
    WithTitle,
    WithHeadings,
    ShouldAutoSize,
    WithStyles,
    WithEvents
{
    use Exportable;

    public function __construct(private readonly array $filters = []) {}

    public function title(): string
    {
        return 'Detail Penjualan';
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Booking',
            'Tanggal Booking',
            'Tanggal Status',
            'Status',
            'Kode Unit',
            'Tipe Rumah',
            'Kategori',
            'Perumahan',
            'Harga Jual (Rp)',
            'Nama Konsumen',
            'NIK',
            'Marketing',
        ];
    }

    public function collection(): Collection
    {
        $start = isset($this->filters['periode_mulai'])
            ? Carbon::parse($this->filters['periode_mulai'])->startOfDay()
            : Carbon::now()->startOfMonth();

        $end = isset($this->filters['periode_selesai'])
            ? Carbon::parse($this->filters['periode_selesai'])->endOfDay()
            : Carbon::now()->endOfMonth();

        $allowedStatus = ['akad', 'serah_terima'];
        if (!empty($this->filters['status']) && in_array($this->filters['status'], $allowedStatus, true)) {
            $allowedStatus = [$this->filters['status']];
        }

        $query = DB::table('status_penjualan as sp')
            ->join('booking as b', 'b.id', '=', 'sp.id_booking')
            ->join('unit_rumah as u', 'u.id', '=', 'sp.id_unit')
            ->join('konsumen as k', 'k.id', '=', 'sp.id_konsumen')
            ->join('perumahan as p', 'p.id', '=', 'u.id_perumahan')
            ->join('users as m', 'm.id', '=', 'b.id_marketing')
            ->whereIn('sp.status_saat_ini', $allowedStatus)
            ->whereBetween('sp.tanggal_perubahan', [$start, $end]);

        if (!empty($this->filters['id_perumahan'])) {
            $query->where('u.id_perumahan', (int) $this->filters['id_perumahan']);
        }
        if (!empty($this->filters['kategori'])) {
            $query->where('u.kategori', $this->filters['kategori']);
        }
        if (!empty($this->filters['id_marketing'])) {
            $query->where('b.id_marketing', (int) $this->filters['id_marketing']);
        }

        $rows = $query->orderBy('sp.tanggal_perubahan', 'desc')
            ->select([
                'b.kode_booking',
                'b.tanggal_booking',
                'sp.tanggal_perubahan',
                'sp.status_saat_ini',
                'u.kode_unit',
                'u.tipe_rumah',
                'u.kategori',
                'p.nama_perumahan',
                'u.harga_jual',
                'k.nama_lengkap as nama_konsumen',
                'k.nik',
                'm.nama_lengkap as nama_marketing',
            ])
            ->get();

        return $rows->values()->map(fn($row, $i) => [
            $i + 1,
            $row->kode_booking,
            Carbon::parse($row->tanggal_booking)->format('d/m/Y'),
            Carbon::parse($row->tanggal_perubahan)->format('d/m/Y'),
            strtoupper(str_replace('_', ' ', $row->status_saat_ini)),
            $row->kode_unit,
            $row->tipe_rumah,
            strtoupper(str_replace('_', ' ', $row->kategori)),
            $row->nama_perumahan,
            (float) $row->harga_jual,
            $row->nama_konsumen,
            $row->nik,
            $row->nama_marketing,
        ]);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet      = $event->sheet->getDelegate();
                $lastRow    = $sheet->getHighestRow();
                $lastCol    = $sheet->getHighestColumn();

                // Format kolom harga jual (kolom J = index 10)
                $sheet->getStyle('J2:J' . $lastRow)
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                // Border seluruh tabel
                $sheet->getStyle('A1:' . $lastCol . $lastRow)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // Zebra striping
                for ($row = 2; $row <= $lastRow; $row++) {
                    if ($row % 2 === 0) {
                        $sheet->getStyle('A' . $row . ':' . $lastCol . $row)
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('F0F4FF');
                    }
                }
            },
        ];
    }
}

// =============================================================================
// Sheet 3 – Breakdown per Bulan
// =============================================================================

class LaporanPenjualanBreakdownSheet implements
    FromCollection,
    WithTitle,
    WithHeadings,
    ShouldAutoSize,
    WithStyles
{
    use Exportable;

    public function __construct(private readonly array $filters = []) {}

    public function title(): string
    {
        return 'Breakdown per Bulan';
    }

    public function headings(): array
    {
        return ['Bulan', 'Total Unit', 'Total Nilai (Rp)', 'Subsidi (Unit)', 'Non-Subsidi (Unit)'];
    }

    public function collection(): Collection
    {
        $start = isset($this->filters['periode_mulai'])
            ? Carbon::parse($this->filters['periode_mulai'])->startOfDay()
            : Carbon::now()->startOfMonth();

        $end = isset($this->filters['periode_selesai'])
            ? Carbon::parse($this->filters['periode_selesai'])->endOfDay()
            : Carbon::now()->endOfMonth();

        $allowedStatus = ['akad', 'serah_terima'];
        if (!empty($this->filters['status']) && in_array($this->filters['status'], $allowedStatus, true)) {
            $allowedStatus = [$this->filters['status']];
        }

        $query = DB::table('status_penjualan as sp')
            ->join('unit_rumah as u', 'u.id', '=', 'sp.id_unit')
            ->join('booking as b', 'b.id', '=', 'sp.id_booking')
            ->whereIn('sp.status_saat_ini', $allowedStatus)
            ->whereBetween('sp.tanggal_perubahan', [$start, $end])
            ->select([
                DB::raw("DATE_FORMAT(sp.tanggal_perubahan, '%Y-%m') as bulan"),
                DB::raw('COUNT(*) as total_unit'),
                DB::raw('SUM(u.harga_jual) as total_nilai'),
                DB::raw("SUM(CASE WHEN u.kategori = 'subsidi' THEN 1 ELSE 0 END) as subsidi"),
                DB::raw("SUM(CASE WHEN u.kategori = 'non_subsidi' THEN 1 ELSE 0 END) as non_subsidi"),
            ]);

        if (!empty($this->filters['id_perumahan'])) {
            $query->where('u.id_perumahan', (int) $this->filters['id_perumahan']);
        }
        if (!empty($this->filters['id_marketing'])) {
            $query->where('b.id_marketing', (int) $this->filters['id_marketing']);
        }

        $rows = $query->groupBy('bulan')->orderBy('bulan')->get();

        return $rows->map(fn($row) => [
            Carbon::parse($row->bulan . '-01')->translatedFormat('F Y'),
            (int) $row->total_unit,
            (float) $row->total_nilai,
            (int) $row->subsidi,
            (int) $row->non_subsidi,
        ]);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
