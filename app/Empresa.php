<?php namespace App;

use Illuminate\Database\Eloquent\Model;


class Empresa extends Model
{
    protected $table   = 'empresa';
    protected $guarded = ['id'];

    public function accidente()
    {
        return $this->hasMany('App\Accidente');
    }

    public static function listado()
    {
        $aDatos = Empresa::orderBy('rzon_soc')
            ->where('activo', 1)
            ->get()
            ->map(function ($item, $key) {
                return [
                    'text'  => $item->rzon_soc,
                    'value' => $item->id
                ];
            });
        return $aDatos;

    }

    public static function listadoTabla()
    {
        $result = [];
        Empresa::orderBy('id')
            ->get()
            ->each(function ($item, $key) use(&$result) {
                $result[$item->id] = $item->rzon_soc;
            });
        return $result;
    }
}
