<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{
    public function edit()
    {
        $empresa = Empresa::first(); // puede ser null
        return view('empresa.edit', compact('empresa'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        Empresa::create($data);

        return redirect()->route('empresa.edit')->with('success', 'Empresa creada correctamente.');
    }

    public function update(Request $request)
    {
        $empresa = Empresa::firstOrFail();
        $data = $this->validateData($request);

        if ($request->hasFile('logo')) {
            if ($empresa->logo_path) {
                Storage::delete($empresa->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        $empresa->update($data);

        return redirect()->back()->with('success', 'Empresa actualizada correctamente.');
    }

    private function validateData(Request $request)
    {
        return $request->validate([
            'nombre' => 'required|string|max:255',
            'cif' => 'required|string|max:20',
            'direccion' => 'required|string|max:255',
            'codigo_postal' => 'required|string|max:10',
            'ciudad' => 'required|string|max:100',
            'provincia' => 'required|string|max:100',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email',
            'web' => 'nullable|url',
            'mapa' => 'nullable|string',
            'notas' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);
    }
}
