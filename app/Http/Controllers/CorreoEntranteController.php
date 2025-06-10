<?php

namespace App\Http\Controllers;

use App\Models\CorreoEntrante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CorreoEntranteController extends Controller
{

    public function index(Request $request)
    {
        $query = CorreoEntrante::query();

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('remitente', 'like', '%' . $request->buscar . '%')
                  ->orWhere('asunto', 'like', '%' . $request->buscar . '%');
            });
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        $correos = $query->latest()->paginate(15)->appends($request->all());

        $categorias = [
            "Solicitud de presupuesto",
            "Consulta técnica",
            "Petición de información",
            "Incidencia postventa",
            "Otro"
        ];

        return view('correos.index', compact('correos', 'categorias'));
    }


    public function index_old()
    {
        $correos = CorreoEntrante::latest()->paginate(15);
        return view('correos.index', compact('correos'));
    }

    public function show(CorreoEntrante $correo)
    {
        return view('correos.show', compact('correo'));
    }

    public function destroy(CorreoEntrante $correo)
    {
        if ($correo->adjunto_path) {
            Storage::delete($correo->adjunto_path);
        }

        $correo->delete();

        return redirect()->route('correos.index')->with('success', 'Correo eliminado.');
    }
    // app/Http/Controllers/CorreoController.php
    public function nuevos()
    {
        $correos = CorreoEntrante::where('notificado', false)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        //CorreoEntrante::whereIn('id', $correos->pluck('id'))->update(['notificado' => true]);

        return response()->json($correos);
    }



}

