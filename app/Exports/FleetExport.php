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
            'Wilayah',
            'Agen',
            'Alamat',
            'Nomor Polisi',
            'Tahun Pembuatan',
            'Usia Kendaraan',
            'Nomor KEUR',
            'Masa Berlaku KEUR',
            'Status KEUR',
            'Masa Berlaku STNK',
            'Masa Berlaku Kendaraan',
            'Status Armada',
            'Nama Supir',
            'Umur Supir',
            'Masa Berlaku SIM',
            'Status SIM',
        ];
    }

    public function map($fleet): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        $activeDriver = $fleet->drivers->first();

        return [
            $rowNumber,
            $fleet->agency?->area?->name ?? '-',
            $fleet->agency?->name ?? '-',
            $fleet->agency?->full_address ?? '-',
            $fleet->license_plate,
            $fleet->year_manufacture,
            $fleet->vehicle_age . ' tahun',
            $fleet->keur_number,
            $fleet->keur_expiry?->format('Y-m-d') ?? '-',
            $fleet->keur_status,
            $fleet->stnk_expiry?->format('Y-m-d') ?? '-',
            $fleet->vehicle_expiry?->format('Y-m-d') ?? '-',
            $fleet->is_active ? 'Aktif' : 'Non-aktif',
            $activeDriver?->name ?? '-',
            $activeDriver?->age ?? '-',
            $activeDriver?->sim_expiry?->format('Y-m-d') ?? '-',
            $activeDriver?->sim_status ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
