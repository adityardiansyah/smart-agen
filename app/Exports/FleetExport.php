<?php

namespace App\Exports;

use App\Models\Fleet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FleetExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    protected ?int $areaId = null;

    public function __construct(?int $areaId = null)
    {
        $this->areaId = $areaId;
    }

    public function query()
    {
        return Fleet::with(['agency.area', 'drivers' => function ($q) {
            $q->where('is_active', true);
        }])
            ->when($this->areaId, function ($query) {
                $query->whereHas('agency', function ($q) {
                    $q->where('area_id', $this->areaId);
                });
            })
            ->orderBy('license_plate');
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Agen',
            'Alamat',
            'Kabupaten',
            'Provinsi',
            'Wilayah SBM',
            'Nopol Armada',
            'Tahun Pembuatan Armada',
            'Nomor KEUR',
            'Masa Berlaku KEUR',
            'Status Dokumen KEUR',
            'Masa Berlaku STNK',
            'Status Masa Berlaku STNK',
            'Masa Habis Armada',
            'Status Masa Habis Armada',
            'Nama Supir',
            'Umur Supir',
            'Masa Berlaku SIM',
            'Jumlah Kepemilikan Tabung',
            'ALokasi Harian',
        ];
    }

    public function map($fleet): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        $activeDriver = $fleet->drivers->first();

        return [
            $rowNumber,
            $fleet->agency?->name ?? '-',
            $fleet->agency?->full_address ?? '-',
            $fleet->agency?->region?->city ?? '-',
            $fleet->agency?->area?->name ?? '-',
            $fleet->agency?->region?->region_sbm ?? '-',
            $fleet->license_plate,
            $fleet->year_manufacture,
            $fleet->keur_number,
            $fleet->keur_expiry?->format('Y-m-d') ?? '-',
            $fleet->keur_status,
            $fleet->stnk_expiry?->format('Y-m-d') ?? '-',
            $fleet->stnk_status,
            $fleet->vehicle_age . ' tahun',
            $fleet->vehicle_expiry?->format('Y-m-d') ?? '-',
            $activeDriver?->name ?? '-',
            $activeDriver?->age ?? '-',
            $activeDriver?->sim_status ?? '-',
            $fleet->agency?->cylinder_count ?? '-',
            $fleet->agency?->daily_allocation ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
