<?php

namespace App\Http\Controllers;

use App\Models\AutorizacionCaja;
use App\Models\CorteCaja;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AutorizacionCajaController extends Controller
{
    /**
     * El cajero solicita autorización o valida con contraseña si el dueño está presente
     */
    public function solicitar(Request $request)
    {
        $request->validate([
            'corte_caja_id' => 'required',
            'efectivo_real' => 'required|numeric',
            'faltante'      => 'required|numeric',
        ]);

        // Si el dueño está presente y envió contraseña
        if ($request->filled('admin_password')) {
            $admin = Usuario::where('rol', 'admin')
                ->get()
                ->first(function ($user) use ($request) {
                    return Hash::check($request->admin_password, $user->password_hash ?? $user->password);
                });

            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contraseña de administrador incorrecta.'
                ], 401);
            }

            // Crear autorización aprobada inmediatamente
            $autorizacion = AutorizacionCaja::create([
                'corte_caja_id'      => $request->corte_caja_id,
                'usuario_solicita_id'=> Auth::id(),
                'usuario_autoriza_id'=> $admin->id,
                'efectivo_real'      => $request->efectivo_real,
                'faltante'           => abs($request->faltante),
                'estado'             => 'aprobada',
            ]);

            return response()->json([
                'success' => true,
                'aprobado_inmediato' => true,
                'message' => 'Autorizado correctamente por ' . $admin->nombre
            ]);
        }

        // Si el dueño NO está presente, crear solicitud PENDIENTE
        $autorizacion = AutorizacionCaja::create([
            'corte_caja_id'      => $request->corte_caja_id,
            'usuario_solicita_id'=> Auth::id(),
            'usuario_autoriza_id'=> null,
            'efectivo_real'      => $request->efectivo_real,
            'faltante'           => abs($request->faltante),
            'estado'             => 'pendiente',
        ]);

        return response()->json([
            'success' => true,
            'aprobado_inmediato' => false,
            'message' => 'Solicitud enviada al Administrador. Esperando aprobación...'
        ]);
    }

    /**
     * El cajero consulta si el administrador ya aprobó desde el historial
     */
    public function estado(Request $request)
    {
        $corteId = $request->get('corte_caja_id');
        
        $autorizacion = AutorizacionCaja::where('corte_caja_id', $corteId)
            ->latest()
            ->first();

        if (!$autorizacion) {
            return response()->json(['estado' => 'ninguna']);
        }

        return response()->json([
            'estado' => $autorizacion->estado,
            'autorizado_por' => $autorizacion->autoriza->nombre ?? 'Administrador'
        ]);
    }

    /**
     * El Admin hace clic en "AUTORIZAR" en la vista Historial de Caja
     */
    public function aprobar($id)
    {
        $solicitud = AutorizacionCaja::findOrFail($id);
        
        $solicitud->update([
            'estado'              => 'aprobada',
            'usuario_autoriza_id' => Auth::id()
        ]);

        // Si existe el corte de caja vinculado, cerrarlo formalmente
        if ($solicitud->corte) {
            $solicitud->corte->update([
                'monto_final'  => $solicitud->efectivo_real,
                'fecha_cierre' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Corte de caja autorizado correctamente.');
    }

    /**
     * El Admin hace clic en "RECHAZAR" en la vista Historial de Caja
     */
    public function rechazar($id)
    {
        $solicitud = AutorizacionCaja::findOrFail($id);
        
        $solicitud->update([
            'estado'              => 'rechazada',
            'usuario_autoriza_id' => Auth::id()
        ]);

        return redirect()->back()->with('error', 'Solicitud de corte rechazada.');
    }
}