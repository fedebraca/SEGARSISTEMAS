@extends('layout.default')
@section('title')
    @parent
@stop
@section('js')
@stop
@section('css')
@stop
@section('cont')
    <div class="viewCont" v-cloak>
        <h1 class="ui dividing header">Crear Usuario</h1>
        <div class="ui segment" :class="{loading: cargando}">
            <div class="ui form">
                <div class="fields">
                    <div class="field">
                        <label>Nombre</label>
                        <input type="text" name="name" v-model="d.name">
                    </div>
                    <div class="field">
                        <label>Email</label>
                        <input type="text" name="email" v-model="d.email">
                    </div>
                    <div class="field">
                        <label>Contraseña</label>
                        <input type="password" name="password" v-model="d.password">
                    </div>
                    <div class="field">
                        <label>Repita Contraseña</label>
                        <input type="password" name="password_rep" v-model="d.password_rep">
                    </div>
                    <div class="field">
                        <label>Tipo</label>
                        <div class="ui ui search selection dropdown" v-select="d.tipo">
                            <input type="hidden" name="tipo">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Tipo</div>
                            <div class="menu">
                                <div v-for="itm in tipos" class="item" data-value="<% itm.value %>"><% itm.text %></div>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label>Vista</label>
                        <div class="ui ui search selection dropdown" v-select="d.vista">
                            <input type="hidden" name="vista">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Vista</div>
                            <div class="menu">
                                <div v-for="itm in vistas" class="item" data-value="<% itm.value %>"><% itm.text %></div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="ui button icon green labeled" v-on:click="crear()"><i class="icon save"></i> Guardar</button>
            </div>
        </div>
    </div>
@stop
@section('script')
    <script>
        $(document).ready(function () {
            var urlCrear = '{{ URL::to('usuario/crear') }}';
            var token = '{{ csrf_token()  }}';

            var viewCont = new Vue({
                el: '.viewCont',
                data: {
                    cargando: false,
                    formulario: {},
                    d: {
                        name: '',
                        email: '',
                        tipo: '',
                        password: '',
                        password_rep: '',
                        vista: ''
                    },
                    tipos: [
                        {'text': 'SUPERVISOR', 'value': 'sup'},
                        {'text': 'ADMINISTRADOR', 'value': 'adm'}
                    ],
                    vistas: [
                        {'text': 'ACCIDENTE', 'value': 'a'},
                        {'text': 'RIESGO', 'value': 'r'},
                        {'text': 'AMBOS', 'value': 'm'}
                    ]

                },
                ready: function () {
                    var self = this;
                    self.formulario = $('.ui.form')
                            .form({
                                on: 'blur',
                                inline: true,
                                fields: {
                                    tipo: 'empty',
                                    name: 'empty',
                                    email: 'empty',
                                    password: 'empty',
                                    password_rep: 'match[password]'
                                }
                            });
                },
                methods: {
                    crear: function () {
                        var self = this;
                        var valida = self.formulario.form('is valid');
                        if (!valida) {
                            mensaje({tipo: 2, 'txt': 'Errores de validación del formulario'});
                            return false;
                        }
                        self.cargando = true;
                        $.post(urlCrear, {'_token': token, d: self.d})
                                .then(function (r) {
                                    mensaje(r);
                                    if (r.tipo == 1) {
                                        self.formulario.form('clear');
                                    }
                                    self.cargando = false;
                                }, function () {
                                    self.cargando = false;
                                })
                    }
                }
            });

        });
    </script>
@stop