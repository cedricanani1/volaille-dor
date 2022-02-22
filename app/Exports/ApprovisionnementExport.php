<?php

namespace App\Exports;

use App\Models\Boutique;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class ApprovisionnementExport implements FromCollection, WithHeadings,WithColumnWidths,WithStyles,WithEvents,WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $boutique =  Boutique::all();
        $product = Product::all();
        return Boutique::all();
    }

    public function headings(): array
    {
        return [
            'Boutiques',
            'Details',
        ];
    }
    public function map($boutique): array
    {
        return [
            [
                $boutique->libelle,
                'Produit','quantité',
            ],

        ];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 35,
            'B' => 45,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            '1'   => ['font' => ['bold' => true]],

            // Styling a specific cell by coordinate.
            '1' => ['font' => ['italic' => true]],

            // Styling an entire column.
            '1'  => ['font' => ['size' => 16]],
        ];
    }
    public function registerEvents(): array
    {

        //$event = $this->getEvent();
        /** @var Sheet $sheet */
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->mergeCells('B1:D1');

                $event->sheet->styleCells(
                    'B1:D1',
                    [
                        'borders' => [
                            'outline' => [
                                'color' => ['argb' => 'FFFF0000'],
                            ],
                        ]
                    ]
                );
            }
        ];
    }
}
