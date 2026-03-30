<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class CauBasicaFactor extends Model
{
    protected $table   = 'cau_basica_factor';
    protected $guarded = ['id'];

    public function accidente()
    {
        return $this->hasMany('App\Accidente');
    }
    
    public static function listado()
    {
        $aDatos = CauBasicaFactor::orderBy('desig')
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
