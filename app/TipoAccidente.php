<?php namespace App;

use Illuminate\Database\Eloquent\Model;


class TipoAccidente extends Model
{
    protected $table   = 'tipo_accidente';
    protected $guarded = ['id'];

    public function accidente()
    {
        return $this->hasMany('App\Accidente');
    }

    public static function listadoTabla()
    {
        $result = [];
        TipoAccidente::orderBy('desig')
            ->get()
            ->each(function ($item, $key) use(&$result) {
                $result[$item->id] = $item->desig;
            });
        return $result;
    }

    public static function listado()
    {
        $aDatos = TipoAccidente::orderBy('desig')
            ->where('activo', 1)
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
