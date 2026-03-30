<?php

namespace App\Http\Controllers;

use Gate;
use App\Incidente;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class IncidenteController extends Controller
{
    public function __construct()
    {
        if (Gate::denies('incidente')) {
            abort(403);
        }
    }

    public function getGestion()
    {
        return view('incidente.index');
    }

    public function postTabla(Request $request)
    {
        $mostrando = $request->input('mostrando');
        $b         = $request->input('busqueda');
        $result    = Incidente::where('id', 'like', '%' . $b . '%')
            ->orWhere('desig', 'like', '%' . $b . '%')
            ->paginate($mostrando);
        return response()->json($result);
    }

    public function postActivaCalc(Request $request)
    {
        $id        = $request->input('id');
        $calc      = $request->input('calc');
        $reg       = Incidente::find($id);
        $reg->calc = $calc;
        $result    = $reg->save();
        if ($result) {
            return response()->json(['result' => 'success']);
        } else {
            return response()->json(['result' => 'error']);
        }
    }

    public function postActivar(Request $request)
    {
        $id          = $request->input('id');
        $estado      = $request->input('estado');
        $reg         = Incidente::find($id);
        $reg->activo = $estado;
        $result      = $reg->save();
        if ($result) {
            return response()->json(['result' => 'success']);
        } else {
            return response()->json(['result' => 'error']);
        }
    }

    public function postEliminar(Request $request)
    {
        $id  = $request->input('id');
        $reg = Incidente::find($id);
        $tot = count($reg->accidente);

        if ($tot > 0) {
            return response()->json(['txt' => 'Error al eliminar. El registro posee otros vinculados', 'tipo' => 2]);
        }
        $result = $reg->delete();
        if ($result) {
            return response()->json(['txt' => 'Registro eliminado', 'tipo' => 1]);
        } else {
            return response()->json(['txt' => 'Error al eliminar', 'tipo' => 2]);
        }
    }

    public function postEditar(Request $request)
    {
        $id         = $request->input('datos.id');
        $reg        = Incidente::find($id);
        $reg->desig = $request->input('datos.desig');
        $result     = $reg->save();
        if ($result) {
            return response()->json(['result' => 'success']);
        } else {
            return response()->json(['result' => 'error']);
        }
    }

    public function postAgregar(Request $request)
    {
        $datos  = $request->input('datos');
        $reg    = Incidente::create($datos);
        $result = $reg->save();
        if ($result) {
            return response()->json(['result' => 'success']);
        } else {
            return response()->json(['result' => 'error']);
        }
    }

    public function postListado(Request $request)
    {
        $tipo = $request->input('tipo');
        $reg  = Incidente::listado($tipo);
        return response()->json($reg);
    }

}


