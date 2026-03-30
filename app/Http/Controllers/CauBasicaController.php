<?php

namespace App\Http\Controllers;

use DB;
use App\CauBasica;
use App\CauBasicaFactor;
use App\CauBasicaTipo;
use Illuminate\Http\Request;

class CauBasicaController extends Controller
{

    public function getGestion()
    {
        return view('causa_basica.index');
    }

    public function postDatos()
    {
        $factores = CauBasicaFactor::orderBy('desig')->get();
        return response()->json(['factores' => $factores]);
    }

    public function postTraeTipos(Request $request)
    {
        $factor = $request->input('factor');
        $tipos  = CauBasicaTipo::where('cau_basica_factor_id', $factor)->orderBy('desig')->get();
        return response()->json($tipos);
    }

    public function postTraeCausas(Request $request)
    {
        $factor = $request->input('factor');
        $tipo   = $request->input('tipo');
        $causas = CauBasica::where('cau_basica_factor_id', $factor)
            ->where('cau_basica_tipo_id', $tipo)
            ->orderBy('desig')
            ->get();
        return response()->json($causas);
    }

    public function postElimFactor(Request $request)
    {
        $id = $request->input('id');

        $tot1 = CauBasicaTipo::where('cau_basica_factor_id', $id)->count();

        if ($tot1 > 0) {
            return response()->json(['txt' => 'Error al eliminar. El registro posee TIPOS vinculados', 'tipo' => 2]);
        }

        $tot2 = CauBasica::where('cau_basica_factor_id', $id)->count();
        if ($tot2 > 0) {
            return response()->json(['txt' => 'Error al eliminar. El registro posee CAUSAS vinculadas', 'tipo' => 2]);
        }

        $reg = CauBasicaFactor::find($id);
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

    public function postElimTipo(Request $request)
    {
        $id = $request->input('id');

        $tot1 = CauBasica::where('cau_basica_tipo_id', $id)->count();
        if ($tot1 > 0) {
            return response()->json(['txt' => 'Error al eliminar. El registro posee CAUSAS vinculadas', 'tipo' => 2]);
        }

        $reg = CauBasicaTipo::find($id);
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
        $reg = CauBasica::find($id);
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

    public function postAgregaFactor(Request $request)
    {
        $desig         = $request->input('desig');
        $factor        = new CauBasicaFactor();
        $factor->desig = $desig;
        if ($factor->save()) {
            return response()->json(['result' => true, 'datos' => $factor]);
        } else {
            return response()->json(['result' => false]);
        }
    }

    public function postAgregaTipo(Request $request)
    {
        $desig                      = $request->input('desig');
        $factor                     = $request->input('factor');
        $tipo                       = new CauBasicaTipo();
        $tipo->desig                = $desig;
        $tipo->cau_basica_factor_id = $factor;
        if ($tipo->save()) {
            return response()->json(['result' => true, 'datos' => $tipo]);
        } else {
            return response()->json(['result' => false]);
        }
    }

    public function postAgregaCausa(Request $request)
    {
        $desig                       = $request->input('desig');
        $factor                      = $request->input('factor');
        $tipo                        = $request->input('tipo');
        $causa                       = new CauBasica();
        $causa->desig                = $desig;
        $causa->cau_basica_factor_id = $factor;
        $causa->cau_basica_tipo_id   = $tipo;
        if ($causa->save()) {
            return response()->json(['result' => true, 'datos' => $causa]);
        } else {
            return response()->json(['result' => false]);
        }
    }

    public function postListadoTipo(Request $request)
    {
        $factor = $request->input('factor');
        $result = CauBasicaTipo::listado($factor);
        return response()->json($result);
    }

    public function postListado(Request $request)
    {
        $factor = $request->input('factor');
        $tipo   = $request->input('tipo');
        $result = CauBasica::listado($factor, $tipo);
        return response()->json($result);
    }
}


