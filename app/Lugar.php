<?php namespace App;

use Illuminate\Database\Eloquent\Model;


class Lugar extends Model
{
    protected $table   = 'lugar';
    protected $guarded = ['id'];

    public function accidente()
    {
        return $this->hasMany('App\Accidente');
    }
    
    public static function listado()
    {
        $aDatos = Lugar::orderBy('desig')
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

    public static function listadoTabla()
    {
        $result = [];
        Lugar::orderBy('id')
            ->get()
            ->each(function ($item, $key) use(&$result) {
                $result[$item->id] = $item->desig;
            });
        return $result;
    }
}
