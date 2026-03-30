<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class CauBasicaTipo extends Model
{
    protected $table   = 'cau_basica_tipo';
    protected $guarded = ['id'];

    public function accidente()
    {
        return $this->hasMany('App\Accidente');
    }
    
    public static function listado($factor)
    {
        $aDatos = CauBasicaTipo::orderBy('desig')
            ->where('cau_basica_factor_id', $factor)
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
