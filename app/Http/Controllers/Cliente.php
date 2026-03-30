<?php namespace App;

use Illuminate\Database\Eloquent\Model;


class Cliente extends Model
{
    protected $table = 'cliente';
    protected $guarded = ['id'];

    public static function listado()
    {
        $aDatos = Producto::orderBy('rzon_soc')
            ->get()
            ->map(function ($item, $key) {
                return [
                    'text'  => $item->rzon_soc,
                    'value' => $item->id
                ];
            });
        return $aDatos;

    }
}
