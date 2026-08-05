<?php

namespace App\Models;

namespace App\Http\Controllers;

use App\Models\Departamento;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    public function index()
    {
        $departamentos = Departamento::orderBy('id', 'desc')->get();
        return view('admin.departamentos.index', compact('departamentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:departamentos,nombre',
            'descripcion' => 'nullable|string',
        ]);

        Departamento::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'activo' => true,
        ]);

        return redirect()->back()->with('success', 'Departamento creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $departamento = Departamento::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:100|unique:departamentos,nombre,' . $id,
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        $departamento->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'activo' => $request->has('activo') ? $request->activo : $departamento->activo,
        ]);

        return redirect()->back()->with('success', 'Departamento actualizado correctamente.');
    }

    public function destroy($id)
    {
        $departamento = Departamento::findOrFail($id);
        
        // Evitamos borrar si ya tiene productos vinculados
        if ($departamento->productos()->count() > 0) {
            return redirect()->back()->with('error', 'No se puede eliminar el departamento porque tiene productos asociados.');
        }

        $departamento->delete();
        return redirect()->back()->with('success', 'Departamento eliminado.');
    }
}