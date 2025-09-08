<?php

namespace App\Http\Controllers;

use App\Models\ComputedMovement;
use App\Models\Computation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComputedMovementController extends Controller
{
    public function index(Request $request)
    {
        $q = ComputedMovement::query()->with('computation');

        // Filtros
        $q->when(
            $request->filled('computation_id'),
            fn($qq) =>
            $qq->where('computation_id', $request->computation_id)
        );
        $q->when(
            $request->filled('delivered_to'),
            fn($qq) =>
            $qq->where('delivered_to', 'like', '%' . $request->delivered_to . '%')
        );
        $q->when(
            $request->filled('area'),
            fn($qq) =>
            $qq->where('area', 'like', '%' . $request->area . '%')
        );
        $q->when(
            $request->filled('taken_by'),
            fn($qq) =>
            $qq->where('taken_by', 'like', '%' . $request->taken_by . '%')
        );
        $q->when(
            $request->filled('seat'),
            fn($qq) =>
            $qq->where('seat', 'like', '%' . $request->seat . '%')
        );
        $q->when(
            $request->filled('date_from'),
            fn($qq) =>
            $qq->whereDate('movement_date', '>=', $request->date_from)
        );
        $q->when(
            $request->filled('date_to'),
            fn($qq) =>
            $qq->whereDate('movement_date', '<=', $request->date_to)
        );

        // Búsqueda en datos del equipo
        $q->when($request->filled('q'), function ($qq) use ($request) {
            $term = '%' . $request->q . '%';
            $qq->whereHas('computation', function ($wc) use ($term) {
                $wc->where('requisition', 'like', $term)
                    ->orWhere('brand', 'like', $term)
                    ->orWhereRaw("`serial_s/n` LIKE ?", [$term]);
            });
        });

        // Paginación real (no usar get())
        $movements = $q->orderByDesc('movement_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        // Lista para el select de equipos
        $computations = Computation::orderBy('requisition')
            ->get(['id', 'requisition', 'brand']);

        return view('computo.movimientos.index', compact('movements', 'computations'));
    }


    /** Mostrar formulario de creación */
    public function create()
    {
        // Lista para el select de equipos
        $computations = Computation::orderBy('requisition')->get(); // id, requisition, brand, (y usar {'serial_s/n'} en la vista)
        return view('computo.movimientos.create', compact('computations'));
    }

    /** Guardar nuevo movimiento */
    public function store(Request $request)
    {
        // Validación (type fijo a 'salida' por tu enum)
        $data = $request->validate([
            'computation_id' => ['required', 'exists:computations,id'],
            'movement_date'  => ['required', 'date'],
            'type'           => ['required', 'in:salida'],
            'amount'         => ['required', 'integer', 'min:1'],
            'delivered_to'   => ['required', 'string', 'max:255'],
            'area'           => ['required', 'string', 'max:255'],
            'taken_by'       => ['required', 'string', 'max:255'],
            'seat'           => ['required', 'string', 'max:255'],
            'observation'    => ['nullable', 'string', 'max:500'],
        ]);

        // Normalizaciones suaves para texto libre
        $data['delivered_to'] = self::toTitle($data['delivered_to']);
        $data['area']         = self::toTitle($data['area']);
        $data['taken_by']     = self::toTitle($data['taken_by']);
        $data['seat']         = self::toTitle($data['seat']);
        $data['observation']  = self::toTitle($data['observation']);

        // Crear (usa fillable en el modelo o asignación explícita)
        ComputedMovement::create($data);

        return redirect()
            ->route('computed_movements.index')
            ->with('success', 'Movimiento registrado correctamente.');
    }

    /** Helpers */
    private static function toTitle(string $s): string
    {
        $s = preg_replace('/\s+/u', ' ', trim($s));
        return mb_convert_case(mb_strtolower($s, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    }

    public function edit(ComputedMovement $movement)
    {
        // Para permitir cambiar de equipo en la edición, pasamos lista:
        $computations = Computation::orderBy('requisition')->get(['id', 'requisition', 'brand']);
        // Agregar serial si lo quieres en el combo:
        // ->get(['id','requisition','brand', \DB::raw("`serial_s/n` as serial")]);

        return view('computo.movimientos.edit', compact('movement', 'computations'));
    }

    public function update(Request $request, ComputedMovement $movement)
    {
        // Aunque en DB el enum solo permite 'salida', validamos por consistencia
        $data = $request->validate([
            'computation_id' => ['required', 'exists:computations,id'],
            'movement_date'  => ['required', 'date'],
            'type'           => ['required', 'in:salida'],
            'amount'         => ['required', 'integer', 'min:1'],
            'delivered_to'   => ['required', 'string', 'max:255'],
            'area'           => ['required', 'string', 'max:255'],
            'taken_by'       => ['required', 'string', 'max:255'],
            'seat'           => ['required', 'string', 'max:255'],
            'observation'    => ['nullable', 'string', 'max:500'],
        ]);

        $movement->update($data);

        return redirect()
            ->route('computed_movements.index')
            ->with('success', 'Movimiento actualizado correctamente.');
    }
}
