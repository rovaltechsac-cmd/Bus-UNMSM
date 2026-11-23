<?php

namespace App\Http\Controllers;

use App\Models\Paradero;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $paraderos = Paradero::query()
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'descripcion', 'latitud', 'longitud', 'orden', 'activo']);

        return view('admin.index', [
            'pagina' => 'admin',
            'paraderos' => $paraderos,
        ]);
    }

    public function storeParadero(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:120', Rule::unique('paraderos', 'nombre')],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'latitud' => ['required', 'numeric', 'between:-90,90'],
            'longitud' => ['required', 'numeric', 'between:-180,180'],
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

        $datos['orden'] = (int) (Paradero::query()->max('orden') ?? 0) + 1;
        $datos['activo'] = true;

        Paradero::query()->create($datos);

        return to_route('admin.index')
            ->with('estado', 'Paradero agregado correctamente.');
    }
}
