<?php

namespace App\Http\Controllers;

use App\Accidente;
use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;
use Illuminate\Http\Request;

class InformeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {
        return view('informe.index');
    }

    public function postDescarga(Request $request)
    {
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');
        $empresa = $request->input('empresa');


        $writer = WriterFactory::create(Type::XLSX); // for XLSX files
//        $path   = public_path() . '/inf.xlsx';
//        $writer->openToFile($path);
        $writer->openToBrowser('informe.xlsx'); // stream data directly to the browser


        $q = Accidente::with('causaInm',
            'causaInmTipo', 'equipo',
            'causaBasica', 'causaBasicaFactor',
            'causaBasicaTipo', 'empresa', 'tipo', 'incidente', 'cliente')
            ->where('f_accidente', '<=', $hasta)
            ->where('f_accidente', '>=', $desde);

        if ($empresa !== '') {
            $q->where('empresa_id', $empresa);
        }

        $sol = $q->get();

        $tit = [
            'Clasif Interna de Incidente',
            'Núm de Siniestro',
            'Cliente',
            'Equipo',
            'Hubo Accidentado?',
            'Nombre y Apellido',
            'Documento',
            'Empresa',
            'Lesion / Evento',
            'Descripción',
            'Fecha Accidente',
            'Fecha Alta',
            'Lugar / Equipo',
            'Dias Perdidos',
            'Denuncia ART',
            'Itinere',
            'Tipo Accidente',
            'Tipo Causa Inmediata',
            'Causa Inmediata',
            'Factor Causa Raiz',
            'Tipo Causa Raiz',
            'Causa Básica / Raíz',
            'Accion Correctiva 1',
            'Cumplimiento 1',
            'Accion Correctiva 2',
            'Cumplimiento 2',
            'Accion Correctiva 3',
            'Cumplimiento 3',
            'Cumplido',
            'Alerta de Seguridad'
        ];

        $writer->addRow($tit);
        foreach ($sol as $s) {
            $row = [
                ($s->incidente) ? $s->incidente->desig : '',
                $s->num_siniestro,
                ($s->cliente) ? $s->cliente->rzon_soc : '',
                ($s->equipo) ? $s->equipo->desig : '',
                ($s->accidentado == 'true')? 'SI' : 'NO',
                $s->nom_ape,
                $s->doc,
                ($s->empresa) ? $s->empresa->rzon_soc : '',
                $s->lesion,
                $s->descripcion,
                $s->f_accidente,
                $s->f_alta,
                $s->lugar,
                $s->dias_perdidos,
                ($s->denuncia_art == 1)? 'SI' : 'NO',
                ($s->itinere == 1)? 'SI' : 'NO',
                ($s->tipo) ? $s->tipo->desig : '',
                ($s->causaInmTipo) ? $s->causaInmTipo->desig : '',
                ($s->causaInm) ? $s->causaInm->desig : '',
                ($s->causaBasicaFactor) ? $s->causaBasicaFactor->desig : '',
                ($s->causaBasicaTipo) ? $s->causaBasicaTipo->desig : '',
                ($s->causaBasica) ? $s->causaBasica->desig : '',
                $s->acc_correct_1,
                $s->cumpl_1,
                $s->acc_correct_2,
                $s->cumpl_2,
                $s->acc_correct_3,
                $s->cumpl_3,
                ($s->cumplido == 1) ? 'SI' : 'NO',
                ($s->alerta_seg == 1) ? 'SI' : 'NO'
            ];

            $writer->addRow($row); // add a row at a time
        }
        $writer->close();

    }
}
