<?php

namespace App\Exports;

use App\Models\Stock;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithMapping;

class StockExport implements FromCollection, WithHeadings,WithColumnWidths,WithStyles,WithEvents,WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection ()
    {
        return Product::all();
    }
    public function headings(): array
    {
        return [
            'Produit',
            'Quantite',
        ];
    }
    public function map($product): array
    {
        return [
            $product->title,
        ];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 55,
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
        return [
            AfterSheet::class => function (AfterSheet $event) {

                /** @var Sheet $sheet */
                $sheet = $event->sheet;

                /**
                 * validation for bulkuploadsheet
                 */

                $sheet->setCellValue('B5', "SELECT ITEM");
                $configs = "DUS800, DUG900+3xRRUS, DUW2100, 2xMU, SIU, DUS800+3xRRUS, DUG900+3xRRUS, DUW2100";
                $objValidation = $sheet->getCell('B5')->getDataValidation();
                $objValidation->setType(DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Value is not in list.');
                $objValidation->setPromptTitle('Pick from list');
                $objValidation->setPrompt('Please pick a value from the drop-down list.');
                $objValidation->setFormula1('"' . $configs . '"');

            }
        ];
    }
}
