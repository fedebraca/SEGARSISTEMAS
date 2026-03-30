<?php namespace App;

use App\Lib\Tool;
use Illuminate\Database\Eloquent\Model;

class CauInm extends Model
{
    protected $table   = 'cau_inm';
    protected $guarded = ['id'];

    public function accidente()
    {
        return $this->hasMany('App\Accidente');
    }

    public static function listadoTabla()
    {
        $result = [];
        CauInm::orderBy('id')
            ->get()
            ->each(function ($item, $key) use(&$result) {
                $result[$item->id] = $item->desig;
            });
        return $result;
    }
    
    public static function listado($tipo)
    {
        $aDatos = CauInm::orderBy('desig')
            ->where('cau_inm_tipo_id', $tipo)
            ->get()
            ->map(function ($item, $key) {
                return [
                    'text'  => $item->desig,
                    'value' => $item->id
                ];
            });
        return $aDatos;

    }

    public static function arbol()
    {
        $aDatos = CauInm::orderBy('desig')
            ->get()
            ->map(function ($item, $key) {
                return [
                    'padre' => $item->padre,
                    'hijo'  => $item->id,
                    'text'  => $item->desig,
                    'id'    => $item->id
                ];
            })
            ->toArray();
        return Tool::get_arbol($aDatos, 0);
    }

}
