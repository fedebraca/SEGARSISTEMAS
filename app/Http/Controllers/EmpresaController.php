<?php

namespace App\Http\Controllers;

use Gate;
use App\Empresa;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class EmpresaController extends Controller
{
    public function __construct()
    {
        if (Gate::denies('empresa')) {
            abort(403);
        }
    }

    public function getGestion()
    {

        return view('empresa.index');
    }

    public function postTabla(Request $request)
    {
        $mostrando = $request->input('mostrando');
        $b         = $request->input('busqueda');
        $result    = Empresa::where('id', 'like', '%' . $b . '%')
            ->orWhere('rzon_soc', 'like', '%' . $b . '%')
            ->paginate($mostrando);
        return response()->json($result);
    }

    public function postActivar(Request $request)
    {
        $id           = $request->input('id');
        $estado       = $request->input('estado');
        $reg         = Empresa::find($id);
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
        $id  = $request->input('id');
        $reg = Empresa::find($id);
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
        $id          = $request->input('datos.id');
        $reg        = Empresa::find($id);
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
        $reg   = Empresa::create($datos);
        $result = $reg->save();
        if ($result) {
            return response()->json(['result' => 'success']);
        } else {
            return response()->json(['result' => 'error']);
        }
    }

    public function postListado(Request $request){
        $tipo  = $request->input('tipo');
        $reg = Empresa::listado($tipo);
        return response()->json($reg);
    }

}


