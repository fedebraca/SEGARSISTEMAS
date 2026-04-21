<?php

namespace App\Http\Controllers;

use App\Accidente;
use App\CauBasica;
use App\CauBasicaFactor;
use App\CauInm;
use App\CauInmTipo;
use App\Cliente;
use App\Empresa;
use App\Equipo;
use App\Incidente;
use App\Lib\Tool;
use App\TipoAccidente;
use Gate;
use Illuminate\Http\Request;
use Validator;

class AccidenteController extends Controller
{
    public function getAgregar()
    {
        if (Gate::denies('accidente-agregar')) {
            abort(403);
        }
        return view('accidente.index', ['oper' => 'add']);
    }

    public function postAgregar(Request $request)
    {
        $upload_success = true;
        $datos = $request->except(['file', 'empresa', 'tipo', 'causa_inm', 'causa_basica', 'causa_basica_factor', 'causa_basica_tipo', 'equipo']);
        $archNoms = ['', '', ''];
        if ($request->hasFile('file')) {
            $archivos = $request->file('file');
            foreach ($archivos as $i => $archivo) {
                if ($i >= 3) break;
                $archNoms[$i] = $archivo->getClientOriginalName();
                $upload_success = $archivo->move(base_path() . '/public/adjuntos/', $archNoms[$i]);
            }
        }
        if ($upload_success) {
            $datos['archivo'] = $archNoms[0];
            $datos['archivo2'] = $archNoms[1];
            $datos['archivo3'] = $archNoms[2];
            $acc = Accidente::create($datos);
            if ($acc->save()) {
                return response()->json(['txt' => 'Registro agregado', 'tipo' => 1]);
            } else {
                return response()->json(['txt' => 'Error al agregar', 'tipo' => 2]);
            }

        }
    }

    public function getEditar($id)
    {
        if (Gate::denies('accidente-editar')) {
            abort(403);
        }
        return view('accidente.index', ['id' => $id, 'oper' => 'edit']);
    }

    public function postGuardar(Request $request)
    {
        $upload_success = false;
        $datos = $request->except(['file', 'empresa', 'cliente', '_token',
            'tipo', 'causa_inm', 'causa_basica', 'causa_basica_factor',
            'causa_basica_tipo', 'equipo', 'causa_inm_tipo']);
        $archNoms = ['', '', ''];
        if ($request->hasFile('file')) {
            $archivos = $request->file('file');
            foreach ($archivos as $i => $archivo) {
                if ($i >= 3) break;
                $archNoms[$i] = $archivo->getClientOriginalName();
                $upload_success = $archivo->move(base_path() . '/public/adjuntos/', $archNoms[$i]);
            }
        }

        if ($upload_success) {
            $datos['archivo'] = $archNoms[0];
            $datos['archivo2'] = $archNoms[1];
            $datos['archivo3'] = $archNoms[2];
        }

        $acc = Accidente::where('id', $datos['id'])->update($datos);
        if ($acc) {
            return response()->json(['txt' => 'Registro guardado', 'tipo' => 1]);
        } else {
            return response()->json(['txt' => 'Error al guardar', 'tipo' => 2]);
        }

    }

    public function getListado()
    {
        if (Gate::denies('accidente-listado')) {
            abort(403);
        }
        return view('accidente.listado');
    }

    public function postTabla(Request $request)
    {
//                DB::enableQueryLog();
        $orden = $request->input('orden');
        $filtro = $request->input('filtro');

        $empresas = Empresa::listadoTabla();
        $tipoAcc = TipoAccidente::listadoTabla();
        $cauInm = CauInm::listadoTabla();
        $cauBasica = CauBasica::listadoTabla();
        $incid = Incidente::listadoTabla();
        $equipo = Equipo::listadoTabla();
        $cliente = [];
        Cliente::orderBy('rzon_soc')
            ->get()
            ->each(function ($item, $key) use (&$cliente) {
                $cliente[$item->id] = $item->rzon_soc;
            });
        $sino = [1 => 'SI', 0 => 'NO'];
        $tf = ['true' => 'SI', 'false' => 'NO'];
        $cumpl = ['' => 'TODO', 25 => '25', 50 => '50', 75 => '75', 100 => '100'];


        $cols = [
            'cliente_id'        => ['tit' => 'Cliente', 'tipo' => 'sel', 'campo' => 'cliente_id', 'align' => 'center', 'op' => $cliente],
            'incidente_id'      => ['tit' => 'Clasif', 'tipo' => 'sel', 'campo' => 'incidente_id', 'align' => 'center', 'op' => $incid],
            'num_siniestro'     => ['tit' => 'Num Siniestro', 'tipo' => 'texto', 'campo' => 'num_siniestro', 'align' => 'center'],
            'accidentado'       => ['tit' => 'Accidentado?', 'tipo' => 'sel', 'campo' => 'accidentado', 'align' => 'center', 'op' => $tf],
            //            'nom_ape'           => ['tit' => 'Nom y Ape', 'tipo' => 'texto', 'campo' => 'nom_ape', 'align' => 'left'],
            //            'doc'               => ['tit' => 'Doc', 'tipo' => 'texto', 'campo' => 'doc', 'align' => 'center'],
            'empresa_id'        => ['tit' => 'Empresa', 'tipo' => 'sel', 'campo' => 'empresa_id', 'align' => 'center', 'op' => $empresas],
            'lesion'            => ['tit' => 'Lesion', 'tipo' => 'texto', 'campo' => 'lesion', 'align' => 'center'],
            //            'descripcion'       => ['tit' => 'Descripcion', 'tipo' => 'texto', 'campo' => 'descripcion', 'align' => 'center'],
            'f_accidente'       => ['tit' => 'F. Accidente', 'tipo' => 'rangoFecha', 'campo' => 'f_accidente', 'align' => 'center'],
            //            'f_alta'            => ['tit' => 'F. Alta', 'tipo' => 'texto', 'rangoFecha' => 'f_alta', 'align' => 'center'],
            'lugar'             => ['tit' => 'Lugar', 'tipo' => 'texto', 'campo' => 'lugar', 'align' => 'center'],
            'equipo_id'         => ['tit' => 'Equipo', 'tipo' => 'sel', 'campo' => 'equipo_id', 'align' => 'center', 'op' => $equipo],
            'dias_perdidos'     => ['tit' => 'Dias Perdidos', 'tipo' => 'texto', 'campo' => 'dias_perdidos', 'align' => 'center'],
            'denuncia_art'      => ['tit' => 'Denuncia ART', 'tipo' => 'sel', 'campo' => 'denuncia_art', 'align' => 'center', 'op' => $sino],
            //            'itinere'           => ['tit' => 'Itinere', 'tipo' => 'sel', 'campo' => 'itinere', 'align' => 'center', 'op' => $sino],
            'tipo_accidente_id' => ['tit' => 'Tipo Accidente', 'tipo' => 'sel', 'campo' => 'tipo_accidente_id', 'align' => 'center', 'op' => $tipoAcc],
            'cau_inm_id'        => ['tit' => 'Causa Inm', 'tipo' => 'sel', 'campo' => 'cau_inm_id', 'align' => 'center', 'op' => $cauInm],
            'cau_basica_id'     => ['tit' => 'Causa Basica', 'tipo' => 'sel', 'campo' => 'cau_basica_id', 'align' => 'center', 'op' => $cauBasica],
            'acc_correct_1'     => ['tit' => 'Acc Corr 1', 'tipo' => 'texto', 'campo' => 'acc_correct_1', 'align' => 'center'],
            'cumpl_1'           => ['tit' => 'Cumpl 1', 'tipo' => 'sel', 'campo' => 'cumpl_1', 'align' => 'center', 'op' => $cumpl],
            'acc_correct_2'     => ['tit' => 'Acc Corr 2', 'tipo' => 'texto', 'campo' => 'acc_correct_2', 'align' => 'center'],
            'cumpl_2'           => ['tit' => 'Cumpl 2', 'tipo' => 'sel', 'campo' => 'cumpl_2', 'align' => 'center', 'op' => $cumpl],
            'acc_correct_3'     => ['tit' => 'Acc Corr 3', 'tipo' => 'texto', 'campo' => 'acc_correct_3', 'align' => 'center'],
            'cumpl_3'           => ['tit' => 'Cumpl 3', 'tipo' => 'sel', 'campo' => 'cumpl_3', 'align' => 'center', 'op' => $cumpl],
            'cumplido'          => ['tit' => 'Cumplido', 'tipo' => 'texto', 'campo' => 'cumplido', 'align' => 'center', 'op' => $sino],
            'alerta_seg'        => ['tit' => 'Alerta', 'tipo' => 'sel', 'campo' => 'alerta_seg', 'align' => 'center', 'op' => $sino],
            'archivo'           => ['tit' => 'Archivo 1', 'tipo' => 'archivo', 'campo' => 'archivo', 'align' => 'center'],
            'archivo2'          => ['tit' => 'Archivo 2', 'tipo' => 'archivo', 'campo' => 'archivo2', 'align' => 'center'],
            'archivo3'          => ['tit' => 'Archivo 3', 'tipo' => 'archivo', 'campo' => 'archivo3', 'align' => 'center']
        ];

        if (Gate::denies('accidente-descarga')) {
            unset($cols['archivo']);
            unset($cols['archivo2']);
            unset($cols['archivo3']);
        }

        $q = Accidente::select('cliente_id', 'accidente.id', 'incidente_id',
            'num_siniestro',
            'accidentado',
            'nom_ape',
            'doc',
            'empresa_id',
            'lesion',
            'descripcion',
            'f_accidente',
            'f_alta',
            'lugar',
            'dias_perdidos',
            'denuncia_art',
            'itinere',
            'tipo_accidente_id',
            'cau_inm_id',
            'cau_basica_id',
            'acc_correct_1',
            'acc_correct_2',
            'acc_correct_3',
            'cumpl_1',
            'cumpl_2',
            'cumpl_3',
            'cumplido',
            'alerta_seg',
            'archivo',
            'archivo2',
            'archivo3',
            'equipo_id'
        )
            ->leftJoin('empresa', 'empresa_id', '=', 'empresa.id')
            ->leftJoin('tipo_accidente', 'tipo_accidente_id', '=', 'tipo_accidente.id')
            ->leftJoin('cau_inm', 'cau_inm_id', '=', 'cau_inm.id')
            ->leftJoin('equipo', 'equipo_id', '=', 'equipo.id')
            ->leftJoin('cau_basica', 'cau_basica_id', '=', 'cau_basica.id')
            ->orderBy('f_accidente');

        Tool::orden($q, $orden);
        Tool::filtro($q, $filtro, $cols);

        $result = $q->paginate(4);
//        $queries = DB::getQueryLog();
//        $last_query = end($queries);
        return response()->json(['datos' => $result, 'cols' => $cols]);
    }

    public function postConfig(Request $request)
    {
        $id = $request->input('id');
        $accidente = [];
        if ($id) {
            $accidente = Accidente::with('empresa')
                ->with('tipo')
                ->with('causaInm')
                ->with('causaInmTipo')
                ->with('causaBasica')
                ->with('causaBasicaFactor')
                ->with('causaBasicaTipo')
                ->with('cliente')
                ->with('equipo')
                ->find($id);
        }
        $incidente = Incidente::listado();
        $tipoAccidente = TipoAccidente::listado();
        $empresa = Empresa::listado();
        $cauInmTipos = CauInmTipo::listado();
        $cauBasicaFactr = CauBasicaFactor::listado();
        $cliente = Cliente::listado();
        $equipo = Equipo::listado();
        return response()->json([
            'incidentes'     => $incidente,
            'tipoAccidentes' => $tipoAccidente,
            'empresas'       => $empresa,
            'cauInmTipos'    => $cauInmTipos,
            'cauBasicaFactr' => $cauBasicaFactr,
            'accidente'      => $accidente,
            'clientes'       => $cliente,
            'equipos'        => $equipo,
        ]);
    }


    public function postEliminar(Request $request)
    {
        if (Gate::denies('accidente-eliminar')) {
            return response()->json(['txt' => 'No posee los permisos necesarios', 'tipo' => 2]);
        }
        $id = $request->input('id');
        $acc = Accidente::find($id);
        if ($acc->delete()) {
            return response()->json(['txt' => 'Registro Eliminado', 'tipo' => 1]);
        } else {
            return response()->json(['txt' => 'Error al eliminar', 'tipo' => 2]);
        }
    }

}