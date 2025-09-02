<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MovementsTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'product_code',
            'date_products',
            'type',
            'amount',
            'delivered_to',
            'area',
            'taken_by',
        ];
    }

    public function array(): array
    {
        // Filas de ejemplo (opcional, puedes dejar [] para vacía)
        return [
            [1001, '2025-09-01', 'entrada', 50, null, null, null],
            [1001, '2025-09-02', 'salida',  10, 'Juan Pérez', 'Producción', 'Ana Gómez'],
        ];
    }
}
