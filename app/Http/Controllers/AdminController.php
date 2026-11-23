<?php

namespace App\Http\Controllers;

use App\Models\Paradero;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        return view('admin.index', [
            'pagina' => 'admin',
            'paraderos' => $this->paraderosOrdenados(),
        ]);
    }

    public function editParadero(Paradero $paradero): View
    {
        return view('admin.editar', [
            'pagina' => 'admin',
            'paradero' => $paradero,
            'paraderos' => $this->paraderosOrdenados(),
        ]);
    }

    public function storeParadero(Request $request): RedirectResponse
    {
        $datos = $this->validarParadero($request);

        $datos['orden'] = (int) (Paradero::query()->max('orden') ?? 0) + 1;
        $datos['activo'] = true;

        Paradero::query()->create($datos);

        return to_route('admin.index')
            ->with('estado', 'Paradero agregado correctamente.');
    }

    public function updateParadero(Request $request, Paradero $paradero): RedirectResponse
    {
        $datos = $this->validarParadero($request, $paradero);

        $datos['activo'] = $request->boolean('activo');

        $paradero->update($datos);

        return to_route('admin.index')
            ->with('estado', 'Paradero actualizado correctamente.');
    }

    public function updateEstadoParadero(Request $request, Paradero $paradero): RedirectResponse
    {
        $request->validate([
            'activo' => ['required', 'boolean'],
        ]);

        $activo = $request->boolean('activo');

        $paradero->update(['activo' => $activo]);

        return to_route('admin.index')
            ->with('estado', $activo ? 'Paradero activado correctamente.' : 'Paradero desactivado correctamente.');
    }

    public function destroyParadero(Paradero $paradero): RedirectResponse
    {
        $paradero->delete();

        return to_route('admin.index')
            ->with('estado', 'Paradero eliminado correctamente.');
    }

    /** @return Collection<int, Paradero> */
    private function paraderosOrdenados(): Collection
    {
        return Paradero::query()
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'descripcion', 'latitud', 'longitud', 'orden', 'activo']);
    }

    /**
     * @return array<string, mixed>
     */
    private function validarParadero(Request $request, ?Paradero $paradero = null): array
    {
        $reglaNombre = Rule::unique('paraderos', 'nombre');

        if ($paradero !== null) {
            $reglaNombre->ignore($paradero);
        }

        return $request->validate([
            'nombre' => ['required', 'string', 'max:120', $reglaNombre],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'latitud' => ['required', 'numeric', 'between:-90,90'],
            'longitud' => ['required', 'numeric', 'between:-180,180'],
            'activo' => ['nullable', 'boolean'],
        ], [
            'nombre.required' => 'Escribe el nombre del paradero.',
            'nombre.unique' => 'Ya existe un paradero con ese nombre.',
            'latitud.required' => 'Indica la latitud del paradero.',
            'latitud.numeric' => 'La latitud debe ser un número.',
            'latitud.between' => 'La latitud debe estar entre -90 y 90.',
            'longitud.required' => 'Indica la longitud del paradero.',
            'longitud.numeric' => 'La longitud debe ser un número.',
            'longitud.between' => 'La longitud debe estar entre -180 y 180.',
        ]);
    }
}
