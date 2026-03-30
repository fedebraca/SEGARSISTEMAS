<?php
namespace App\Lib;

use DateTime;
use DB;

class Tool
{
    public static function get_arbol($items, $padre)
    {
        $childs = array();

        foreach ($items as &$item) {
            $childs[$item['padre']][] = &$item;
        }
        unset($item);

        foreach ($items as &$item) {
            if (isset($childs[$item['hijo']])) {
                $item['children'] = $childs[$item['hijo']];
            }
        }

        if (isset($childs[$padre])) {
            return $childs[$padre];
        }
    }

    public static function fConvert($fecha, $format_desde, $format_hasta)
    {
        $myDateTime    = DateTime::createFromFormat($format_desde, $fecha);
        $newDateString = ($myDateTime) ? $myDateTime->format($format_hasta) : '';
        return $newDateString;
    }

    public static function filtro($q, $filtro, $cols)
    {
        if ($filtro) {
            foreach ($filtro as $campo => $v) {
                if ($cols[$campo]['tipo'] == 'bool') {
                    $v = ($v == 'true') ? 1 : 0;
                }
                if ($cols[$campo]['tipo'] == 'rango') {
                    if (isset($v['desde']) && isset($v['hasta'])) {
                        $q->having(DB::raw($cols[$campo]['campo']), '>=', $v['desde'])
                            ->having(DB::raw($cols[$campo]['campo']), '<=', $v['hasta']);
                    }
                } elseif (isset($cols[$campo]['campos'])) {
                    foreach ($cols[$campo]['campos'] as $itm) {
                        $q->orWhere($itm, 'like', '%' . $v . '%');
                    }
                } elseif ($cols[$campo]['tipo'] == 'rangoFecha') {
                    if (isset($v['desde']) && isset($v['hasta'])) {
                        $q->orWhere(function ($query) use ($v, $cols, $campo) {
                            $query->where(DB::raw($cols[$campo]['campo']), '>=', $v['desde'])
                                ->where(DB::raw($cols[$campo]['campo']), '<=', $v['hasta']);
                        });

                    }
                } elseif ($cols[$campo]['tipo'] == 'sel') {
                    $q->where($cols[$campo]['campo'], $v);
                } else {
                    $q->where($cols[$campo]['campo'], 'like', '%' . $v . '%');
                }

            }
        }
    }

    public static function orden($q, $orden)
    {
        if ($orden) {
            foreach ($orden as $campo => $dir) {
                $q->orderBy($campo, $dir);
            }
        }
    }

}

