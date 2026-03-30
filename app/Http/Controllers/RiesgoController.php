<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Riesgo;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Gate;

class RiesgoController extends Controller
{

    public function getListado()
    {
        if (Gate::denies('riesgo-listado')) {
            abort(403);
        }
        return view('riesgo.listado');
    }

    public function getAgregar()
    {
        if (Gate::denies('riesgo-agregar')) {
            abort(403);
        }
        return view('riesgo.index', ['oper' => 'add']);
    }

    public function postConfig(Request $request)
    {
        $id     = $request->input('id');
        $cli    = Cliente::listado();
        $riesgo = [];
        if ($id) {
            $riesgo = Riesgo::with('cliente')
                ->find($id);
        }
        return response()->json(['clientes' => $cli, 'riesgo' => $riesgo]);
    }

    public function getEditar($id)
    {
        if (Gate::denies('riesgo-editar')) {
            abort(403);
        }
        return view('riesgo.index', ['id' => $id, 'oper' => 'edit']);
    }

    public function postTabla(Request $request)
    {
        $mostrando = $request->input('mostrando');
        $b         = $request->input('busqueda');
        $result    = Riesgo::select('*','riesgo.id as ident')
            ->leftJoin('cliente', 'riesgo.cliente_id', '=', 'cliente.id')
            ->where('rzon_soc', 'like', '%' . $b . '%')
            ->orWhere('desc', 'like', '%' . $b . '%')
            ->orWhere('coment', 'like', '%' . $b . '%')
            ->orWhere('lugar', 'like', '%' . $b . '%')
            ->with('cliente')
            ->paginate($mostrando);
        return response()->json($result);
    }


    public function postEliminar(Request $request)
    {
        if (Gate::denies('riesgo-eliminar')) {
            return response()->json(['txt' => 'No posee los permisos necesarios', 'tipo' => 2]);
        }
        
        $id     = $request->input('id');
        $itm    = Riesgo::find($id);
        $result = $itm->delete();


        if ($result) {
            return response()->json(['txt' => 'Registro eliminado', 'tipo' => 1]);
        } else {
            return response()->json(['txt' => 'Error al eliminar', 'tipo' => 2]);
        }
    }

    public function postGuardar(Request $request)
    {
        $upload_success = false;
        $datos = $request->except(['file', 'cliente','_token']);
        if ($request->hasFile('file')) {
            $archivos = $request->file('file');
            foreach ($archivos as $archivo) {
                $archNom = $archivo->getClientOriginalName();
                $upload_success = $archivo->move(base_path() . '/public/adjuntos/', $archNom);
            }
        }

        if ($upload_success) {
            $datos['archivo'] = $archNom;
        }

        $acc = Riesgo::where('id', $datos['id'])->update($datos);
        if ($acc) {
            return response()->json(['txt' => 'Registro guardado', 'tipo' => 1]);
        } else {
            return response()->json(['txt' => 'Error al guardar', 'tipo' => 2]);
        }
    }

    public function postAgregar(Request $request)
    {
        $upload_success = true;
        $datos          = $request->except('file');
        $archNom        = '';
        if ($request->hasFile('file')) {
            $archivos = $request->file('file');
            foreach ($archivos as $archivo) {
                $archNom        = $archivo->getClientOriginalName();
                $upload_success = $archivo->move(base_path() . '/public/adjuntos/', $archNom);
            }
        }
        if ($upload_success) {
            $datos['archivo'] = $archNom;
            $riesgo           = Riesgo::create($datos);
            if ($riesgo->save()) {
                return response()->json(['txt' => 'Registro agregado', 'tipo' => 1]);
            } else {
                return response()->json(['txt' => 'Error al agregar', 'tipo' => 2]);
            }
        }
    }

}


