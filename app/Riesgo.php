<?php namespace App;

use Illuminate\Database\Eloquent\Model;


class Riesgo extends Model
{
    protected $table = 'riesgo';
    protected $guarded = ['id'];

    public function cliente()
    {
        return $this->belongsTo('App\Cliente');
    }
    
    public static function listado()
    {
        $aDatos = Producto::orderBy('desc')
            ->get()
            ->map(function ($item, $key) {
                return [
                    'text'  => $item->desc,
                    'value' => $item->id
                ];
            });
        return $aDatos;

    }
}
