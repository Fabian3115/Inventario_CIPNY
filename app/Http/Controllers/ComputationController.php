<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Computation;
use Illuminate\Support\Facades\DB;

class ComputationController extends Controller
{
    public function index(Request $request)
    {
        $q = Computation::query();

        $q->when(
            $request->filled('requisition'),
            fn($qq) =>
            $qq->where('requisition', 'like', '%' . $request->requisition . '%')
        );
        $q->when(
            $request->filled('brand'),
            fn($qq) =>
            $qq->where('brand', 'like', '%' . $request->brand . '%')
        );
        $q->when(
            $request->filled('serial'),
            fn($qq) =>
            $qq->whereRaw("`serial_s/n` LIKE ?", ['%' . $request->serial . '%'])
        );
        $q->when(
            $request->filled('type'),
            fn($qq) =>
            $qq->where('type', 'like', '%' . $request->type . '%')
        );

        // Búsqueda rápida (incluye type)
        $q->when($request->filled('q'), function ($qq) use ($request) {
            $term = '%' . $request->q . '%';
            $qq->where(function ($w) use ($term) {
                $w->where('requisition', 'like', $term)
                    ->orWhere('brand', 'like', $term)
                    ->orWhere('type', 'like', $term)
                    ->orWhereRaw("`serial_s/n` LIKE ?", [$term]);
            });
        });

        // Alias para evitar $c->{'serial_s/n'}
        $q->addSelect('*', DB::raw("`serial_s/n` as serial"));

        $computations = $q->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('computo.index', compact('computations'));
    }

    /** Mostrar formulario de creación */
    public function create()
    {
        return view('computo.create');
    }

    /** Guardar nuevo equipo */
    public function store(Request $request)
    {
        // Validación
        $data = $request->validate([
            'requisition'   => ['required', 'string', 'max:255'],
            'brand'         => ['required', 'string', 'max:255'],
            'serial_s/n'    => ['required', 'string', 'max:255'],
            'type'          => ['nullable', 'string', 'max:255'],
        ]);

        // Normalizaciones
        $requisition = self::cleanSpaces($data['requisition']);
        $brand       = self::toTitle($data['brand']);
        $serial      = strtoupper(trim(preg_replace('/\s+/u', '', $data['serial_s/n']))); // sin espacios, MAYÚSCULAS

        // Crear (asignación explícita por el slash en el nombre de columna)
        $c = new Computation();
        $c->requisition = $requisition;
        $c->brand       = $brand;
        $c->{'serial_s/n'} = $serial;
        $c->type        = $data['type'] ?? null;
        $c->save();

        return redirect()
            ->route('computations.index')
            ->with('success', 'Equipo registrado correctamente.');
    }

    /** Helpers */
    private static function toTitle(string $s): string
    {
        $s = self::cleanSpaces($s);
        return mb_convert_case(mb_strtolower($s, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    }

    private static function cleanSpaces(string $s): string
    {
        return preg_replace('/\s+/u', ' ', trim($s));
    }
    public function edit(Computation $computation)
    {
        return view('computo.edit', compact('computation'));
    }

    public function update(Request $request, Computation $computation)
    {
        // Validación
        $data = $request->validate([
            'requisition'    => ['required', 'string', 'max:255'],
            'brand'          => ['required', 'string', 'max:255'],
            'serial_s/n'     => ['required', 'string', 'max:255'],
            'type'           => ['nullable', 'string', 'max:255'],
        ]);

        // Normalizaciones (en caso de que aún no tengas mutators)
        $brand  = self::toTitle($data['brand']);
        $serial = strtoupper(trim(preg_replace('/\s+/u', '', $data['serial_s/n'])));

        $computation->requisition = trim($data['requisition']);
        $computation->brand = $brand;
        // OJO: columna con slash, accedemos con llaves
        $computation->{'serial_s/n'} = $serial;
        $computation->type = $data['type'] ?? null;

        $computation->save();

        return redirect()
            ->route('computations.index')
            ->with('success', 'Equipo actualizado correctamente.');
    }
}
