<?php

namespace App\Exports;

use Carbon\Carbon;
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
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
// (opcional) si tu versión de PhpSpreadsheet soporta tablas:
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;

class MovementsInExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithColumnFormatting,
    WithEvents,
    WithStyles
{
    use Exportable;


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
                m.product_id
            ')
            ->where('m.type', '=', 'entrada');
    }

    public function query()
    {
        $q = $this->baseQuery();

        // Filtros igual a la vista
        if ($s = $this->filters['q'] ?? null) {
            $q->where(function ($qq) use ($s) {
                $qq->where('p.code', 'like', "%{$s}%")
                    ->orWhere('p.description', 'like', "%{$s}%");
            });
        }
        if ($pid = $this->filters['product_id'] ?? null) {
            $q->where('m.product_id', $pid);
        }
        if ($df = $this->filters['date_from'] ?? null) {
            $q->whereDate('m.date_products', '>=', $df);
        }
        if ($dt = $this->filters['date_to'] ?? null) {
            $q->whereDate('m.date_products', '<=', $dt);
        }

        // Orden
        $sort = $this->filters['sort'] ?? 'date_products';
        $dir  = strtolower($this->filters['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        $sortMap = [
            'date_products' => 'm.date_products',
            'p_code'        => 'p.code',
            'p_description' => 'p.description',
            'amount'        => 'm.amount',
        ];
        $q->orderBy($sortMap[$sort] ?? 'm.date_products', $dir);

        return $q;
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

    /** Estilos por rango y celdas (cabecera, alineación, etc.) */
    public function styles(Worksheet $sheet)
    {
        // Cabecera (A1:G1) negrita y centrada
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Encabezado con fondo suave y borde inferior
        $sheet->getStyle('A1:G1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFEFEFEF');
        $sheet->getStyle('A1:G1')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);

        // Alinear cantidad a la derecha
        $sheet->getStyle('D:D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Envolver texto para “Producto”
        $sheet->getStyle('C:C')->getAlignment()->setWrapText(true);

        return [];
    }

    /** Mejora de UX: congelar encabezados, autofiltro, tabla, zebra, etc. */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $s = $event->sheet->getDelegate();

                // Congelar fila 1
                $s->freezePane('A2');

                // Rango usado (A1 .. última columna/fila)
                $lastRow = $s->getHighestRow();
                $lastCol = $s->getHighestColumn();
                $range   = "A1:{$lastCol}{$lastRow}";

                // Autofiltro en todo el rango
                $s->setAutoFilter($range);

                // Zebra (bandas alternadas) con formato condicional
                // Filas de datos empiezan en 2
                if ($lastRow >= 3) {
                    $zebraRange = "A2:{$lastCol}{$lastRow}";
                    $conditionalStyles = $s->getStyle($zebraRange)->getConditionalStyles();

                    $cond = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
                    $cond->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_EXPRESSION);
                    // FILA PAR: =MOD(ROW(),2)=0
                    $cond->addCondition('MOD(ROW(),2)=0');
                    $cond->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF9F9F9');

                    $conditionalStyles[] = $cond;
                    $s->getStyle($zebraRange)->setConditionalStyles($conditionalStyles);
                }

                // Bordes finos alrededor del rango completo
                $s->getStyle($range)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

                // (Opcional) convertir en “Tabla” de Excel (filtros + banding nativo + estilo)
                // Requiere PhpSpreadsheet con soporte de Table (1.25+ aprox.).
                try {
                    $table = new Table('A1:' . $lastCol . $lastRow, 'Entradas');
                    $table->setShowTotalsRow(false);

                    // ⬇️ reemplaza esta línea:
                    // $table->setStyle(TableStyle::TABLE_STYLE_MEDIUM9);

                    // ⬇️ por estas:
                    $ts = new TableStyle();
                    // En la mayoría de versiones:
                    $ts->setTheme(TableStyle::TABLE_STYLE_MEDIUM9);
                    // (si tu versión es más vieja y falla la constante, prueba con el string)
                    // $ts->setTheme('TableStyleMedium9');

                    $table->setStyle($ts);

                    $s->addTable($table);
                } catch (\Throwable $e) {
                    // Si la versión no soporta Table, no pasa nada (ya dejamos autofiltro + zebra)
                }

                // Opcional: ancho mínimo para columnas clave (si el autosize quedó muy pequeño)
                foreach (['A', 'B', 'C'] as $col) {
                    $w = $s->getColumnDimension($col)->getWidth() ?? 0;
                    if ($w < 12) $s->getColumnDimension($col)->setWidth(12);
                }
            },
        ];
    }
}
