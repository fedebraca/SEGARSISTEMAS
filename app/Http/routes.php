<?php
Route::group(['middleware' => ['web']], function () {

    Route::controllers([
        'auth'          => 'Auth\AuthController',
        'password'      => 'Auth\PasswordController',
        'accidente'     => 'AccidenteController',
        'incidente'     => 'IncidenteController',
        'empresa'       => 'EmpresaController',
        'tipoaccidente' => 'TipoAccidenteController',
        'lugar'         => 'LugarController',
        'indicador'     => 'IndicadorController',
        'cauinm'        => 'CauInmController',
        'caubasica'     => 'CauBasicaController',
        'usuario'       => 'UsuarioController',
        'horas'         => 'HorasHombreController',
        'riesgo'        => 'RiesgoController',
        'informe'       => 'InformeController',
        'cliente'       => 'ClienteController',
        'equipo'        => 'EquipoController',
    ]);

});

Route::auth();
Route::get('/', function () {
    if (Auth::check()) {
        if (Gate::allows('v-accidente')) {
            return view("home");
        } elseif (Gate::allows('v-riesgo')) {
            return view("riesgo.listado");
        }

    } else {
        return view("welcome");
    }
});
Route::any('{path?}', function () {
    if (Auth::check()) {
        return view("home");
    } else {
        return view("welcome");
    }

})->where("path", ".+");

