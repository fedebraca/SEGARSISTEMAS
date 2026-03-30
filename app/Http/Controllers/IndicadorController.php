<?php

namespace App\Http\Controllers;

use App\Accidente;
use App\Cliente;
use App\Empresa;
use App\Equipo;
use App\HorasHombre;
use App\Lib\Tool;
use DB;
use Illuminate\Http\Request;
use Validator;

class IndicadorController extends Controller
{

    public function getPanel()
    {
        return view('indicador.panel');
    }

    public function postConfigEmpresas()
    {
        $empresas = Empresa::listado();
        return response()->json($empresas);
    }

    public function postConfigClientes(Request $request)
    {
        $empresa = $request->input('empresa');
        $aDatos = Cliente::select('cliente.id', 'cliente.rzon_soc')
            ->orderBy('rzon_soc')
            ->join('accidente', function ($join) use ($empresa) {
                $join->on('accidente.cliente_id', '=', 'cliente.id')
                    ->where('accidente.empresa_id', '=', $empresa);
            })
            ->distinct()
            ->get()
            ->map(function ($item, $key) {
                return [
                    'text'  => $item->rzon_soc,
                    'value' => $item->id
                ];
            });
        $aDatos[] = [
            'text'  => 'TODOS',
            'value' => ''
        ];
        return response()->json($aDatos);
    }

    public function postConfigEquipos(Request $request)
    {
        $empresa = $request->input('empresa');
        $cliente = $request->input('cliente');
        $aDatos = Equipo::select('equipo.id', 'equipo.desig')
            ->orderBy('desig')
            ->join('accidente', function ($join) use ($empresa, $cliente) {
                $join->on('accidente.cliente_id', '=', 'equipo.id')
                    ->where('accidente.empresa_id', '=', $empresa)
                    ->where('accidente.cliente_id', '=', $cliente);
            })
            ->distinct()
            ->get()
            ->map(function ($item, $key) {
                return [
                    'text'  => $item->desig,
                    'value' => $item->id
                ];
            });
        $aDatos[] = [
            'text'  => 'TODOS',
            'value' => ''
        ];
        return response()->json($aDatos);
    }

    public function postPanel(Request $request)
    {
        $equipo = $request->input('d.equipo');
        $empresa = $request->input('d.empresa');
        $cliente = $request->input('d.cliente');
        $desde = $request->input('d.desde');
        $hasta = $request->input('d.hasta');
        $desdeMes = intval(Tool::fConvert($desde, 'Y-m-d', 'm'));
        $desdeAno = intval(Tool::fConvert($desde, 'Y-m-d', 'Y'));
        $hastaMes = intval(Tool::fConvert($hasta, 'Y-m-d', 'm'));
        $hastaAno = intval(Tool::fConvert($hasta, 'Y-m-d', 'Y'));
//        $desde   = Tool::fConvert($desde, 'd/m/Y', 'Y-m-d');
//        $hasta   = Tool::fConvert($hasta, 'd/m/Y', 'Y-m-d');
        $empNom = 'TODAS';
        $cliNom = 'TODOS';
        $equiNom = 'TODOS';

        //        DB::enableQueryLog();

        ///////////////// total de accidentes calc
        $totAccidentesCal = Accidente::where('f_accidente', '>=', $desde)
            ->where('f_accidente', '<=', $hasta)
            ->where('incidente.calc', 1)
            ->leftJoin('incidente', 'incidente_id', '=', 'incidente.id')
            ->count();
        ///////////////// total de accidentes
        $totAccidentes = Accidente::where('f_accidente', '>=', $desde)
            ->where('f_accidente', '<=', $hasta)
            ->count();
        ///////////////// total de accidentes en empresa activa
        $totAccidentesEmp = $this->totAccidenteSelEmpresa($empresa, $desde, $hasta);
        ///////////////// total de accidentes en cliente activo
        $totAccidentesCli = $this->totAccidenteSelCliente($empresa, $cliente, $desde, $hasta);
        ///////////////// total de accidentes en equipo activo
        $totAccidentesEqu = $this->totAccidenteSelEquipo($empresa, $cliente, $equipo, $desde, $hasta);
        ///////////////// total de accidentes por empresa
        $accEmp = Accidente::select(DB::raw('count(empresa_id) as total'), 'empresa.rzon_soc')
            ->join('empresa', 'empresa_id', '=', 'empresa.id')
            ->where('f_accidente', '>=', $desde)
            ->where('f_accidente', '<=', $hasta)
            ->groupBy('empresa_id')
            ->get();
        ///////////////// indice de frecuencia de accidentes
        $totHsHombre = HorasHombre::whereRaw(' mes + ano >= ' . ($desdeMes + $desdeAno))
            ->whereRaw(' mes + ano <= ' . ($hastaMes + $hastaAno))
            ->sum('cant');
        if ($totHsHombre == 0) {
            $indFrecTot = 0;
        } else {
            $indFrecTot = ($totAccidentesCal * 1000000) / $totHsHombre;
        }
        ///////////////// total de dias perdidos
        $diasPerdidos = Accidente::where('f_accidente', '>=', $desde)
            ->where('f_accidente', '<=', $hasta)
            ->sum('dias_perdidos');
        ///////////////// grafico de torta 1 accidentes por tipo
        $qAccTipo = Accidente::select(DB::raw('count(tipo_accidente_id) as total'), 'tipo_accidente.desig')
            ->join('tipo_accidente', 'tipo_accidente_id', '=', 'tipo_accidente.id')
            ->where('f_accidente', '>=', $desde)
            ->where('f_accidente', '<=', $hasta)
            ->groupBy('tipo_accidente_id');
        if ($empresa) {
            $qAccTipo->where('empresa_id', $empresa);
            $empNom = Empresa::find($empresa)->rzon_soc;
        }
        if ($cliente) {
            $qAccTipo->where('cliente_id', $cliente);
        }
        if ($equipo) {
            $qAccTipo->where('equipo_id', $equipo);
        }
        $accTipo = $qAccTipo->get()
            ->map(function ($item, $key) {
                return [
                    'name' => $item->desig,
                    'y'    => $item->total
                ];
            });
        ///////////////// grafico de torta 2 accidentes por tipo
        $qAccTipoTipo = Accidente::select(DB::raw('count(cau_inm_tipo_id) as total'), 'cau_inm_tipo.desig')
            ->join('cau_inm_tipo', 'cau_inm_tipo_id', '=', 'cau_inm_tipo.id')
            ->where('f_accidente', '>=', $desde)
            ->where('f_accidente', '<=', $hasta)
            ->groupBy('cau_inm_tipo_id');
        if ($empresa) {
            $qAccTipoTipo->where('empresa_id', $empresa);
            $empNom = Empresa::find($empresa)->rzon_soc;
        }
        if ($cliente) {
            $qAccTipoTipo->where('cliente_id', $cliente);
        }
        if ($equipo) {
            $qAccTipoTipo->where('equipo_id', $equipo);
        }
        $accTipoTipo = $qAccTipoTipo->get()
            ->map(function ($item, $key) {
                return [
                    'name' => $item->desig,
                    'y'    => $item->total
                ];
            });
        ///////////////// grafico de torta 3 accidentes por factor de causa basica
        $qAccCauBasicaFactor = Accidente::select(DB::raw('count(cau_basica_factor_id) as total'), 'cau_basica_factor.desig')
            ->join('cau_basica_factor', 'cau_basica_factor_id', '=', 'cau_basica_factor.id')
            ->where('f_accidente', '>=', $desde)
            ->where('f_accidente', '<=', $hasta)
            ->groupBy('cau_basica_factor_id');
        if ($empresa) {
            $qAccCauBasicaFactor->where('empresa_id', $empresa);
            $empNom = Empresa::find($empresa)->rzon_soc;
        }
        if ($cliente) {
            $qAccCauBasicaFactor->where('cliente_id', $cliente);
        }
        if ($equipo) {
            $qAccCauBasicaFactor->where('equipo_id', $equipo);
        }
        $accCauBasicaFactor = $qAccCauBasicaFactor->get()
            ->map(function ($item, $key) {
                return [
                    'name' => $item->desig,
                    'y'    => $item->total
                ];
            });
        ///////////////// grafico de torta 5 accidentes por cliente
        $qAccCliente = Accidente::select(DB::raw('count(cliente_id) as total'), 'cliente.rzon_soc')
            ->join('cliente', 'cliente_id', '=', 'cliente.id')
            ->where('f_accidente', '>=', $desde)
            ->where('f_accidente', '<=', $hasta)
            ->groupBy('cliente_id');
        if ($cliente) {
            $qAccCliente->where('cliente_id', $cliente)
                ->where('empresa_id', $empresa);
            $cliNom = Cliente::find($cliente)->rzon_soc;
        }
        $accCliente = $qAccCliente->get()
            ->map(function ($item, $key) {
                return [
                    'name' => $item->rzon_soc,
                    'y'    => $item->total
                ];
            });
        ///////////////// grafico de torta 6 accidentes por equipo
        $qAccEquipo = Accidente::select(DB::raw('count(equipo_id) as total'), 'equipo.desig')
            ->join('equipo', 'equipo_id', '=', 'equipo.id')
            ->where('f_accidente', '>=', $desde)
            ->where('f_accidente', '<=', $hasta)
            ->groupBy('equipo_id');
        if ($equipo) {
            $qAccEquipo->where('equipo_id', $equipo)
                ->where('empresa_id', $empresa)
                ->where('cliente_id', $cliente);
            $equiNom = Equipo::find($equipo)->desig;
        }
        $accEquipo = $qAccEquipo->get()
            ->map(function ($item, $key) {
                return [
                    'name' => $item->desig,
                    'y'    => $item->total
                ];
            });

        $grafIindFrecEmp = $this->graficoIndices('frecuencia', 'empresa_id', $desdeMes, $desdeAno, $hastaMes, $hastaAno, $desde, $hasta);
        $grafIindGravEmp = $this->graficoIndices('gravedad', 'empresa_id', $desdeMes, $desdeAno, $hastaMes, $hastaAno, $desde, $hasta);
        $grafIindFrecCli = $this->graficoIndices('frecuencia', 'cliente_id', $desdeMes, $desdeAno, $hastaMes, $hastaAno, $desde, $hasta);
        $grafIindGravCli = $this->graficoIndices('gravedad', 'cliente_id', $desdeMes, $desdeAno, $hastaMes, $hastaAno, $desde, $hasta);
        $grafIindFrecEqu = $this->graficoIndices('frecuencia', 'equipo_id', $desdeMes, $desdeAno, $hastaMes, $hastaAno, $desde, $hasta);
        $grafIindGravEqu = $this->graficoIndices('gravedad', 'equipo_id', $desdeMes, $desdeAno, $hastaMes, $hastaAno, $desde, $hasta);

        ///////////////// acciones correctivas a realizar
        $accRealizar[] = ['nom' => '1', 'tot' => Accidente::where('cumpl_1', '<', 100)
            ->where('f_accidente', '>=', $desde)
            ->where('f_accidente', '<=', $hasta)
            ->count('cumpl_1')];
        $accRealizar[] = ['nom' => '2', 'tot' => Accidente::where('cumpl_2', '<', 100)
            ->where('f_accidente', '>=', $desde)
            ->where('f_accidente', '<=', $hasta)
            ->count('cumpl_2')];
        $accRealizar[] = ['nom' => '3', 'tot' => Accidente::where('cumpl_3', '<', 100)
            ->where('f_accidente', '>=', $desde)
            ->where('f_accidente', '<=', $hasta)
            ->count('cumpl_3')];
        $accRealizarColl = collect($accRealizar);
        $accRealizarTot = $accRealizarColl->sum('tot');
        ///////////////// acciones correctivas realizadas
        $accRealizadas[] = ['nom' => '1', 'tot' => Accidente::where('cumpl_1', 100)
            ->where('f_accidente', '>=', $desde)
            ->where('f_accidente', '<=', $hasta)
            ->count('cumpl_1')];
        $accRealizadas[] = ['nom' => '2', 'tot' => Accidente::where('cumpl_2', 100)
            ->where('f_accidente', '>=', $desde)
            ->where('f_accidente', '<=', $hasta)
            ->count('cumpl_2')];
        $accRealizadas[] = ['nom' => '3', 'tot' => Accidente::where('cumpl_3', 100)
            ->where('f_accidente', '>=', $desde)
            ->where('f_accidente', '<=', $hasta)
            ->count('cumpl_3')];
        $accRealizadasColl = collect($accRealizadas);
        $accRealizadasTot = $accRealizadasColl->sum('tot');
        ///////////////// indice de gravedad de accidentes
        //Total de días perdidos (por accidente o enfermedad profesional) x 1000 / total de Horas Hombre Expuestas
        if ($totHsHombre == 0) {
            $indGrav = 0;
        } else {
            $indGrav = ($diasPerdidos * 1000) / $totHsHombre;
        }

        $indEmp = $this->indices('empresa_id', $empresa, $desdeMes, $desdeAno, $hastaMes, $hastaAno, $desde, $hasta);
        $indCli = $this->indices('cliente_id', $cliente, $desdeMes, $desdeAno, $hastaMes, $hastaAno, $desde, $hasta);
        $indEqu = $this->indices('equipo_id', $equipo, $desdeMes, $desdeAno, $hastaMes, $hastaAno, $desde, $hasta);

//        $queries    = DB::getQueryLog();
//        $last_query = end($queries);
        return response()->json([
            'totAccidentes'      => $totAccidentes,
            'accEmp'             => $accEmp,
            'diasPerdidos'       => $diasPerdidos,
            'accTipo'            => $accTipo,
            'accCliente'         => $accCliente,
            'accTipoTipo'        => $accTipoTipo,
            'accCauBasicaFactor' => $accCauBasicaFactor,
            'empNom'             => $empNom,
            'cliNom'             => $cliNom,
            'accRealizar'        => $accRealizar,
            'accRealizadas'      => $accRealizadas,
            'accRealizarTot'     => $accRealizarTot,
            'accRealizadasTot'   => $accRealizadasTot,
            'indFrecTot'         => round($indFrecTot, 2),
            'totAccidentesEmp'   => $totAccidentesEmp,
            'totAccidentesCli'   => $totAccidentesCli,
            'totAccidentesEqu'   => $totAccidentesEqu,
            'indFrecTotEmp'      => round($indEmp['frecuencia'], 2),
            'indFrecTotCli'      => round($indCli['frecuencia'], 2),
            'indFrecTotEqu'      => round($indEqu['frecuencia'], 2),
            'indGrav'            => round($indGrav, 2),
            'indGravEmp'         => round($indEmp['gravedad'], 2),
            'indGravCli'         => round($indCli['gravedad'], 2),
            'indGravEqu'         => round($indEqu['gravedad'], 2),
            'accEquipo'          => $accEquipo,
            'equiNom'            => $equiNom,
            'grafIindFrecEmp'    => $grafIindFrecEmp,
            'grafIindGravEmp'    => $grafIindGravEmp,
            'grafIindFrecCli'    => $grafIindFrecCli,
            'grafIindGravCli'    => $grafIindGravCli,
            'grafIindFrecEqu'    => $grafIindFrecEqu,
            'grafIindGravEqu'    => $grafIindGravEqu,
            'grafHsEmp'          => $this->graficoHoras('empresa', $empresa, $desdeMes, $desdeAno, $hastaMes, $hastaAno),
            'grafHsCli'          => $this->graficoHoras('cliente', $cliente, $desdeMes, $desdeAno, $hastaMes, $hastaAno),
            'grafHsEqu'          => $this->graficoHoras('equipo', $equipo, $desdeMes, $desdeAno, $hastaMes, $hastaAno),
        ]);
    }

    private function indices($tipo, $variable, $desdeMes, $desdeAno, $hastaMes, $hastaAno, $desde, $hasta)
    {
        $totHsHombreEmp = HorasHombre::whereRaw(' mes + ano >= ' . ($desdeMes + $desdeAno))
            ->whereRaw(' mes + ano <= ' . ($hastaMes + $hastaAno))
            ->where($tipo, $variable)
            ->sum('cant');
        //frecuencia de accidente
        if ($totHsHombreEmp == 0) {
            $indFrecTotEmp = 0;
        } else {
            $totAccidentesCalEmp = $this->totAccidentesCalEmp($tipo, $variable, $desde, $hasta);
            $indFrecTotEmp = ($totAccidentesCalEmp * 1000000) / $totHsHombreEmp;
        }
        // gravedad de accidente
        if ($totHsHombreEmp == 0) {
            $indGravEmp = 0;
        } else {
            $diasPerdidosEmp = $this->diasPerdidos($tipo, $variable, $desde, $hasta);
            $indGravEmp = ($diasPerdidosEmp * 1000) / $totHsHombreEmp;
        }


        return ['frecuencia' => $indFrecTotEmp, 'gravedad' => $indGravEmp];
    }

    private function totAccidentesCalEmp($tipo, $variable, $desde, $hasta)
    {
        return Accidente::where('f_accidente', '>=', $desde)
            ->where('f_accidente', '<=', $hasta)
            ->where('incidente.calc', 1)
            ->where($tipo, $variable)
            ->leftJoin('incidente', 'incidente_id', '=', 'incidente.id')
            ->count();
    }

    private function diasPerdidos($tipo, $variable, $desde, $hasta)
    {
        return Accidente::where('f_accidente', '>=', $desde)
            ->where('f_accidente', '<=', $hasta)
            ->where($tipo, $variable)
            ->sum('dias_perdidos');
    }

    private function totAccidenteSelEmpresa($emp, $desde, $hasta)
    {
        return Accidente::where('f_accidente', '>=', $desde)
            ->where('f_accidente', '<=', $hasta)
            ->where('empresa_id', $emp)
            ->count();
    }

    private function totAccidenteSelCliente($emp, $cli, $desde, $hasta)
    {
        return Accidente::where('f_accidente', '>=', $desde)
            ->where('f_accidente', '<=', $hasta)
            ->where('empresa_id', $emp)
            ->where('cliente_id', $cli)
            ->count();
    }

    private function totAccidenteSelEquipo($emp, $cli, $eq, $desde, $hasta)
    {
        return Accidente::where('f_accidente', '>=', $desde)
            ->where('f_accidente', '<=', $hasta)
            ->where('empresa_id', $emp)
            ->where('cliente_id', $cli)
            ->where('equipo_id', $eq)
            ->count();
    }

    private function graficoIndices($grafico, $tipo, $desdeMes, $desdeAno, $hastaMes, $hastaAno, $desde, $hasta)
    {

        if ($tipo == 'empresa_id') {
            $variables = Empresa::select('id', 'rzon_soc as desig');
        } elseif ($tipo == 'cliente_id') {
            $variables = Cliente::select('id', 'rzon_soc as desig');
        } elseif ($tipo == 'equipo_id') {
            $variables = Equipo::select('id', 'desig');
        }

        return $variables->get()
            ->map(function ($item, $key) use ($grafico, $tipo, $desdeMes, $desdeAno, $hastaMes, $hastaAno, $desde, $hasta) {
                $valor = $this->indices($tipo, $item->id, $desdeMes, $desdeAno, $hastaMes, $hastaAno, $desde, $hasta);
                return [
                    'name' => $item->desig,
                    'y'    => $valor[$grafico]
                ];
            });


    }

    private function graficoHoras($tipo, $variable, $desdeMes, $desdeAno, $hastaMes, $hastaAno)
    {
        $q = HorasHombre::whereRaw(' mes + ano >= ' . ($desdeMes + $desdeAno))
            ->whereRaw(' mes + ano <= ' . ($hastaMes + $hastaAno))
            ->orderBy('mes', 'ano');

        if ($tipo == 'empresa') {
            $q->where('empresa_id', $variable);
        } elseif ($tipo == 'cliente') {
            $q->where('cliente_id', $variable);
        } elseif ($tipo == 'equipo') {
            $q->where('equipo_id', $variable);
        }

        return $q->get()
            ->map(function ($item, $key) {
                return [
                    'name' => $item->mes . '-' . $item->ano,
                    'y'    => $item->cant
                ];
            });
    }

}