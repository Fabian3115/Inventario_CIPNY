<?php

namespace App\Exports;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MovementsOutExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithColumnFormatting,
    WithEvents,
    WithStyles,
    WithTitle,
    Responsable
{
    use Exportable;

    public string $fileName = 'salidas.xlsx';

    public function __construct(protected array $filters = []) {}

    protected function baseQuery(): Builder
    {
        return DB::table('movements as m')
            ->join('products as p', 'p.id', '=', 'm.product_id')
            ->selectRaw('
                m.date_products,
                p.code as p_code,
                p.description as p_description,
                m.amount,
                p.extent as p_extent,
                p.warehouse as p_warehouse,
                m.area,
                m.delivered_to,
                m.taken_by,
                m.product_id
            ')
            ->where('m.type', '=', 'salida');
    }

    public function query()
    {
        $q = $this->baseQuery();

        if ($s = $this->filters['q'] ?? null) {
            $q->where(function ($qq) use ($s) {
                $qq->where('p.code', 'like', "%{$s}%")
                   ->orWhere('p.description', 'like', "%{$s}%");
            });
        }
        if ($pid = $this->filters['product_id'] ?? null) {
            $q->where('m.product_id', $pid);
        }
        if ($area = $this->filters['area'] ?? null) {
            $q->where('m.area', $area);
        }
        if ($df = $this->filters['date_from'] ?? null) {
            $q->whereDate('m.date_products', '>=', $df);
        }
        if ($dt = $this->filters['date_to'] ?? null) {
            $q->whereDate('m.date_products', '<=', $dt);
        }
        if ($dto = $this->filters['delivered_to'] ?? null) {
            $q->where('m.delivered_to', 'like', "%{$dto}%");
        }
        if ($tb = $this->filters['taken_by'] ?? null) {
            $q->where('m.taken_by', 'like', "%{$tb}%");
        }

        $sort = $this->filters['sort'] ?? 'date_products';
        $dir  = strtolower($this->filters['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $sortMap = [
            'date_products' => 'm.date_products',
            'p_code'        => 'p.code',
            'p_description' => 'p.description',
            'amount'        => 'm.amount',
            'area'          => 'm.area',
            'delivered_to'  => 'm.delivered_to',
            'taken_by'      => 'm.taken_by',
        ];
        return $q->orderBy($sortMap[$sort] ?? 'm.date_products', $dir);
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Código',
            'Producto',
            'Cantidad',
            'Unidad',
            'Almacén',
            'Área',
            'Entregado a',
            'Registrado por',
            'ID Producto',
        ];
    }

    public function map($row): array
    {
        return [
            $row->date_products ? Carbon::parse($row->date_products) : null,
            $row->p_code,
            $row->p_description,
            (float) $row->amount,
            $row->p_extent ?? '—',
            $row->p_warehouse ?? '—',
            $row->area ?? '—',
            $row->delivered_to ?? '—',
            $row->taken_by ?? '—',
            $row->product_id,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_YYYYMMDD2, // Fecha
            'D' => NumberFormat::FORMAT_NUMBER_00,      // Cantidad
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
        $sheet->getStyle('A1:J1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:J1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFEFEFEF');
        $sheet->getStyle('A1:J1')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('D:D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('C:C')->getAlignment()->setWrapText(true);
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $s = $event->sheet->getDelegate();

                // Congelar encabezado
                $s->freezePane('A2');

                // Rango
                $lastRow = $s->getHighestRow();
                $lastCol = $s->getHighestColumn();
                $range   = "A1:{$lastCol}{$lastRow}";

                // Autofiltro
                $s->setAutoFilter($range);

                // Zebra
                if ($lastRow >= 3) {
                    $zebraRange = "A2:{$lastCol}{$lastRow}";
                    $cond = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
                    $cond->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_EXPRESSION);
                    $cond->addCondition('MOD(ROW(),2)=0');
                    $cond->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF9F9F9');
                    $s->getStyle($zebraRange)->setConditionalStyles([$cond]);
                }

                // Fila de totales (suma Cantidad en D)
                $totalRow = $lastRow + 1;
                $s->setCellValue("C{$totalRow}", 'TOTAL');
                $s->setCellValue("D{$totalRow}", "=SUM(D2:D{$lastRow})");
                $s->getStyle("C{$totalRow}:D{$totalRow}")->getFont()->setBold(true);
                $s->getStyle("A{$totalRow}:{$lastCol}{$totalRow}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);

                // Borde exterior del rango completo
                $s->getStyle($range)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

                // Mínimos anchos en A-C
                foreach (['A','B','C'] as $col) {
                    $w = $s->getColumnDimension($col)->getWidth() ?? 0;
                    if ($w < 12) $s->getColumnDimension($col)->setWidth(12);
                }
            },
        ];
    }

    public function title(): string
    {
        return 'Salidas';
    }
}
