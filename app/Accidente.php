<?php namespace App;

use Illuminate\Database\Eloquent\Model;


class Accidente extends Model
{
    protected $table   = 'accidente';
    protected $guarded = ['id'];

    public function incidente()
    {
        return $this->belongsTo('App\Incidente');
    }

    public function causaInm()
    {
        return $this->belongsTo('App\CauInm', 'cau_inm_id');
    }

    public function causaInmTipo()
    {
        return $this->belongsTo('App\CauInmTipo', 'cau_inm_tipo_id');
    }

    public function causaBasica()
    {
        return $this->belongsTo('App\CauBasica', 'cau_basica_id');
    }

    public function causaBasicaFactor()
    {
        return $this->belongsTo('App\CauBasicaFactor', 'cau_basica_factor_id');
    }

    public function causaBasicaTipo()
    {
        return $this->belongsTo('App\CauBasicaTipo', 'cau_basica_tipo_id');
    }

    public function empresa()
    {
        return $this->belongsTo('App\Empresa');
    }

    public function tipo()
    {
        return $this->belongsTo('App\TipoAccidente', 'tipo_accidente_id');
    }

    public function cliente()
    {
        return $this->belongsTo('App\Cliente', 'cliente_id');
    }

    public function equipo()
    {
        return $this->belongsTo('App\Equipo', 'equipo_id');
    }
}
