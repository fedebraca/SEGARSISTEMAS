<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class CauInmTipo extends Model
{
    protected $table   = 'cau_inm_tipo';
    protected $guarded = ['id'];

    public function accidente()
    {
        return $this->hasMany('App\Accidente');
    }

    public static function listado()
    {
        $aDatos = CauInmTipo::orderBy('desig')
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
