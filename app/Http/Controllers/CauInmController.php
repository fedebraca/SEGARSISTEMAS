<?php

namespace App\Http\Controllers;

use App\CauInm;
use App\CauInmTipo;
use Illuminate\Http\Request;

class CauInmController extends Controller
{

    public function getGestion()
    {
        return view('causa_inm.index');
    }

    public function postDatos()
    {
        $tipos = CauInmTipo::orderBy('desig')->get();
        return response()->json(['tipos' => $tipos]);
    }

    public function postTraeCausas(Request $request)
    {
        $tipo   = $request->input('tipo');
        $causas = CauInm::where('cau_inm_tipo_id', $tipo)
            ->orderBy('desig')
            ->get();
        return response()->json($causas);
    }

    public function postElimTipo(Request $request)
    {
        $id  = $request->input('id');
        $reg = CauInmTipo::find($id);
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

    public function postElimCausa(Request $request)
    {
        $id  = $request->input('id');
        $reg = CauInm::find($id);
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

    public function postAgregaTipo(Request $request)
    {
        $desig                      = $request->input('desig');
        $tipo                       = new CauInmTipo();
        $tipo->desig                = $desig;
        if ($tipo->save()) {
            return response()->json(['result' => true, 'datos' => $tipo]);
        } else {
            return response()->json(['result' => false]);
        }
    }

    public function postAgregaCausa(Request $request)
    {
        $desig                       = $request->input('desig');
        $tipo                        = $request->input('tipo');
        $causa                       = new CauInm();
        $causa->desig                = $desig;
        $causa->cau_inm_tipo_id   = $tipo;
        if ($causa->save()) {
            return response()->json(['result' => true, 'datos' => $causa]);
        } else {
            return response()->json(['result' => false]);
        }
    }


    public function postListado(Request $request)
    {
        $tipo   = $request->input('tipo');
        $result = CauInm::listado($tipo);
        return response()->json($result);
    }

}


