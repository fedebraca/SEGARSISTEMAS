<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Empresa;
use App\Equipo;
use Gate;
use App\HorasHombre;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use DB;

class HorasHombreController extends Controller
{
    public function __construct()
    {
        if (Gate::denies('horas_hombre')) {
            abort(403);
        }
    }

    public function getEmpresa()
    {

        return view('horas_hombre.index', ['tipo' => 'e', 'tit' => 'Empresa']);
    }

    public function getCliente()
    {

        return view('horas_hombre.index', ['tipo' => 'c', 'tit' => 'Cliente']);
    }

    public function getEquipo()
    {

        return view('horas_hombre.index', ['tipo' => 'q', 'tit' => 'Equipo']);
    }

    public function postTabla(Request $request)
    {
//        DB::connection()->enableQueryLog();
        $mostrando = $request->input('mostrando');
        $b         = $request->input('busqueda');
        $tipo      = $request->input('tipo');
        $empresas  = Empresa::listado();
        $clientes  = Cliente::listado();
        $equipos   = Equipo::listado();
        $result    = HorasHombre::select('empresa.rzon_soc as empresa_txt', 'empresa_id',
            'cliente.rzon_soc as cliente_txt', 'cliente_id',
            'equipo.desig as equipo_txt', 'equipo_id',
            'horas_hombre.id', 'mes', 'ano', 'cant')
            ->leftJoin('empresa', 'empresa_id', '=', 'empresa.id')
            ->leftJoin('cliente', 'cliente_id', '=', 'cliente.id')
            ->leftJoin('equipo', 'equipo_id', '=', 'equipo.id')
            ->where(function ($query) use ($b) {
                $query->where('horas_hombre.id', 'like', '%' . $b . '%')
                    ->orWhere('empresa.rzon_soc', 'like', '%' . $b . '%')
                    ->orWhere('equipo.desig', 'like', '%' . $b . '%')
                    ->orWhere('mes', 'like', '%' . $b . '%')
                    ->orWhere('ano', 'like', '%' . $b . '%');
            })
            ->where('tipo', $tipo)
            ->paginate($mostrando);

//        $queries = DB::getQueryLog();
        return response()->json(['tabla' => $result, 'list' => ['empresas' => $empresas, 'clientes' => $clientes, 'equipos' => $equipos]]);
    }


    public function postEliminar(Request $request)
    {
        $id     = $request->input('id');
        $reg    = HorasHombre::find($id);
        $result = $reg->delete();
        if ($result) {
            return response()->json(['result' => 'success']);
        } else {
            return response()->json(['result' => 'error']);
        }
    }

    public function postEditar(Request $request)
    {
        $id              = $request->input('datos.id');
        $reg             = HorasHombre::find($id);
        $reg->empresa_id = $request->input('datos.empresa_id');
        $reg->cliente_id = $request->input('datos.cliente_id');
        $reg->equipo_id  = $request->input('datos.equipo_id');
        $reg->cant       = $request->input('datos.cant');
        $reg->mes        = $request->input('datos.mes');
        $reg->ano        = $request->input('datos.ano');
        $result          = $reg->save();
        if ($result) {
            return response()->json(['result' => 'success']);
        } else {
            return response()->json(['result' => 'error']);
        }
    }

    public function postAgregar(Request $request)
    {
        $datos  = $request->input('datos');
        $reg    = HorasHombre::create($datos);
        $result = $reg->save();
        if ($result) {
            return response()->json(['result' => 'success']);
        } else {
            return response()->json(['result' => 'error']);
        }
    }


}


