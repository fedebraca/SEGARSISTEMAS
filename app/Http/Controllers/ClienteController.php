<?php

namespace App\Http\Controllers;

use Gate;
use App\Cliente;
use Validator;
use Illuminate\Http\Request;

class ClienteController extends Controller
{

    public function __construct()
    {
        if (Gate::denies('cliente')) {
            abort(403);
        }
    }
    public function getGestion()
    {
        return view('cliente.index');
    }

    public function postTabla(Request $request)
    {
        $mostrando = $request->input('mostrando');
        $b         = $request->input('busqueda');
        $result    = Cliente::where('rzon_soc', 'like', '%' . $b . '%')
            ->paginate($mostrando);
        return response()->json($result);
    }

    public function postActivar(Request $request)
    {
        $id           = $request->input('id');
        $estado       = $request->input('estado');
        $reg         = Cliente::find($id);
        $reg->activo = $estado;
        $result       = $reg->save();
        if ($result) {
            return response()->json(['result' => 'success']);
        } else {
            return response()->json(['result' => 'error']);
        }
    }

    public function postEliminar(Request $request)
    {
        $id     = $request->input('id');
        $reg   = Cliente::find($id);
        $result = $reg->delete();
        if ($result) {
            return response()->json(['result' => 'success']);
        } else {
            return response()->json(['result' => 'error']);
        }
    }

    public function postEditar(Request $request)
    {
        $id          = $request->input('datos.id');
        $reg        = Cliente::find($id);
        $reg->rzon_soc = $request->input('datos.rzon_soc');
        $result      = $reg->save();
        if ($result) {
            return response()->json(['result' => 'success']);
        } else {
            return response()->json(['result' => 'error']);
        }
    }

    public function postAgregar(Request $request)
    {
        $datos  = $request->input('datos');
        $reg   = Cliente::create($datos);
        $result = $reg->save();
        if ($result) {
            return response()->json(['result' => 'success']);
        } else {
            return response()->json(['result' => 'error']);
        }
    }

    public function postListado(Request $request){
        $tipo  = $request->input('tipo');
        $reg = Cliente::listado($tipo);
        return response()->json($reg);
    }

}


