<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class CauBasica extends Model
{
    protected $table   = 'cau_basica';
    protected $guarded = ['id'];

    public function accidente()
    {
        return $this->hasMany('App\Accidente');
    }

    public static function listadoTabla()
    {
        $result = [];
        CauBasica::orderBy('desig')
            ->get()
            ->each(function ($item, $key) use(&$result) {
                $result[$item->id] = $item->desig;
            });
        return $result;
    }

    public static function listado($factor,$tipo)
    {
        $aDatos = CauBasica::orderBy('desig')
            ->where('cau_basica_factor_id', $factor)
            ->where('cau_basica_tipo_id', $tipo)
            ->get()
            ->map(function ($item, $key) {
                return [
                    'text'  => $item->desig,
                    'value' => $item->id
                ];
            });
        return $aDatos;

    }

}
