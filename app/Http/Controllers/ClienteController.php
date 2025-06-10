<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::latest()->paginate(15);
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateCliente($request);
        Cliente::create($data);
        return redirect()->route('clientes.index')->with('success', 'Cliente creado correctamente.');
    }

    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $data = $this->validateCliente($request);
        $cliente->update($data);
        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado.');
    }

    private function validateCliente(Request $request)
    {
        return $request->validate([
            'nombre_empresa' => 'required|string|max:255',
            'cif' => 'required|string|max:20|unique:clientes,cif,' . $request->route('cliente')?->id,
            'persona_contacto' => 'required|string|max:255',
            'email' => 'required|email',
            'telefono' => 'required|string|max:20',
            'telefono_secundario' => 'nullable|string|max:20',
            'direccion' => 'required|string|max:255',
            'codigo_postal' => 'required|string|max:10',
            'ciudad' => 'required|string|max:100',
            'provincia' => 'required|string|max:100',
            'pais' => 'nullable|string|max:100',
            'web' => 'nullable|url',
            'sector' => 'nullable|string|max:100',
            'notas' => 'nullable|string',
        ]);
    }
}
