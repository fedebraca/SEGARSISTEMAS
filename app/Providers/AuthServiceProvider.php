<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);

        $gate->before(function ($user, $ability) {
            if ($user->tipo === 'adm') {
                return true;
            }
        });
        $gate->define('accidente-descarga', function ($user) {

            return $user->tipo === 'sup';
        });
        $gate->define('accidente-listado', function ($user) {
            return $user->tipo === 'sup';
        });
        $gate->define('accidente-agregar', function ($user) {
            return $user->tipo === 'sup';
        });
        $gate->define('riesgo-listado', function ($user) {
            return $user->tipo === 'sup';
        });
        $gate->define('riesgo-agregar', function ($user) {
            return $user->tipo === 'sup';
        });
        $gate->define('riesgo-editar', function ($user) {
            return $user->tipo === 'sup';
        });
        $gate->define('menu-administracion', function ($user) {
            return $user->tipo !== 'sup';
        });

        $gate->define('v-riesgo', function ($user) {
            return ($user->vista === 'r' || $user->vista === 'm') ? true : false;
        });
        $gate->define('v-accidente', function ($user) {
            return ($user->vista === 'a' || $user->vista === 'm') ? true : false;
        });
        $gate->define('v-ambos', function ($user) {
            return ($user->vista === 'm') ? true : false;
        });
    }
}
