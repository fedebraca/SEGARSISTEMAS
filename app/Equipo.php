<?php namespace App;

use Illuminate\Database\Eloquent\Model;


class Equipo extends Model
{
    protected $table   = 'equipo';
    protected $guarded = ['id'];

    public function accidente()
    {
        return $this->hasMany('App\Accidente');
    }
    
    public static function listado()
    {
        $aDatos = Equipo::orderBy('desig')
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
        Equipo::orderBy('desig')
            ->get()
            ->each(function ($item, $key) use(&$result) {
                $result[$item->id] = $item->desig;
            });
        return $result;
    }
}
