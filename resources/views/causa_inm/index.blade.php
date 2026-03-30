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
        <h1 class="ui dividing header">CAUSAS INMEDIATAS</h1>
        <div class="ui form">
            <div class="ui grid three columns">
                <div class="column">
                    <h3>TIPOS</h3>
                    <table class="ui table">
                        <thead>
                            <tr>
                                <th>Designación</th>
                                <th><i class="icon setting"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="v in l.tipos">
                                <td v-on:click="selTipo($index)" v-if="!cargaTipo">
                                    <% v.desig %>
                                    <span v-if="d.tipo == $index" style="float: right"><i class="icon green check"></i></span>
                                </td>
                                <td class="center aligned">
                                    <button class="ui mini button icon red" v-on:click="elimTipo($index,v.id)">
                                        <i class="icon" :class="{loading: indxElimTipo == $index, refresh: indxElimTipo == $index, trash: indxElimTipo != $index }"></i>
                                    </button>
                                </td>

                            </tr>
                            <tr>
                                <td><input type="text" v-model="g.tipo"></td>
                                <td class="center aligned">
                                    <button class="ui mini button icon green" v-on:click="agregaTipo()">
                                        <i class="icon" :class="{loading:agregandoTipo, refresh:agregandoTipo, plus:!agregandoTipo}"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="cargaTipo">
                                <td class="center aligned"><i class="icon loading refresh"></i></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="column">
                    <h3>CAUSAS</h3>
                    <table class="ui table">
                        <thead>
                            <tr>
                                <th>Designación</th>
                                <th><i class="icon setting"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="ui icon input fluid">
                                        <input type="text" placeholder="Filtro..." v-model="bCausa">
                                        <i class="circular search link icon"></i>
                                    </div>
                                </td>
                            </tr>
                            <tr v-for="v in l.causas | filterBy bCausa in 'desig'" class="animated" transition="fade">
                                <td v-on:click="selCausa($index)" v-if="!cargaCausa">
                                    <% v.desig %>
                                    <span v-if="d.causa == $index" style="float: right"><i class="icon green check"></i></span>
                                </td>
                                <td class="center aligned">
                                    <button class="ui mini button icon red" v-on:click="elimCausa($index,v.id)">
                                        <i class="icon" :class="{loading: indxElimCausa == $index, refresh: indxElimCausa == $index, trash: indxElimCausa != $index }"></i>
                                    </button>
                                </td>

                            </tr>
                            <tr>
                                <td><input type="text" v-model="g.causa"></td>
                                <td class="center aligned">
                                    <button class="ui mini button icon green" v-on:click="agregaCausa()">
                                        <i class="icon" :class="{loading:agregandoCausa, refresh:agregandoCausa, plus:!agregandoCausa}"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="cargaCausa">
                                <td class="center aligned"><i class="icon loading refresh"></i></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

@stop
@section('script')
    <script>
        $(document).ready(function () {
            var urlDatos = '{{ URL::to('cauinm/datos') }}';
            var urlTipos = '{{ URL::to('cauinm/trae-tipos') }}';
            var urlCausas = '{{ URL::to('cauinm/trae-causas') }}';
            var urlElimFactor = '{{ URL::to('cauinm/elim-factor') }}';
            var urlElimTipo = '{{ URL::to('cauinm/elim-tipo') }}';
            var urlElimCausa = '{{ URL::to('cauinm/elim-causa') }}';
            var urlAgregaFactor = '{{ URL::to('cauinm/agrega-factor') }}';
            var urlAgregaTipo = '{{ URL::to('cauinm/agrega-tipo') }}';
            var urlAgregaCausa = '{{ URL::to('cauinm/agrega-causa') }}';
            var token = '{{ csrf_token()  }}';
            var viewCont = new Vue({
                el: '.viewCont',
                data: {
                    bCausa: '',
                    cargaTipo: false,
                    cargaCausa: false,
                    agregandoTipo: false,
                    agregandoCausa: false,
                    indxElimTipo: -1,
                    indxElimCausa: -1,
                    d: {
                        tipo: -1,
                        causa: -1
                    },
                    g: {
                        tipo: '',
                        causa: ''
                    },
                    l: {
                        tipos: [],
                        causas: []
                    }
                },
                ready: function () {
                    var self = this;
                    self.traeDatos();
                },
                watch: {
                    'd.tipo': function (val) {
                        var self = this;
                        if (self.d.tipo == -1) {
                            return;
                        }
                        self.l.causas = [];
                        var tipo = self.l.tipos[self.d.tipo].id;
                        self.cargaCausa = true;
                        $.post(urlCausas, {_token: token, tipo: tipo})
                                .then(function (r) {
                                    self.l.causas = r;
                                    self.cargaCausa = false;
                                })
                    }
                },
                methods: {
                    selTipo: function ($index) {
                        var self = this;
                        self.d.tipo = $index;
                    },
                    agregaTipo: function () {
                        var self = this;
                        self.agregandoTipo = true;
                        $.post(urlAgregaTipo, {_token: token, desig: self.g.tipo})
                                .then(function (r) {
                                    if (r.result == true) {
                                        self.l.tipos.push(Vue.util.extend({}, r.datos));
                                    }
                                    self.agregandoTipo = false;
                                })
                    },
                    agregaCausa: function () {
                        var self = this;
                        self.agregandoCausa = true;
                        var tipo = self.l.tipos[self.d.tipo].id;
                        $.post(urlAgregaCausa, {_token: token, desig: self.g.causa, tipo: tipo})
                                .then(function (r) {
                                    if (r.result == true) {
                                        self.l.causas.push(Vue.util.extend({}, r.datos));
                                    }
                                    self.agregandoCausa = false;
                                })
                    },
                    elimTipo: function ($index, id) {
                        var self = this;
                        self.indxElimTipo = $index;
                        $.post(urlElimTipo, {_token: token, id: id})
                                .then(function (r) {
                                    if (r.tipo == 1) {
                                        if ($index !== -1) {
                                            self.l.tipos.splice($index, 1)
                                        }
                                    }
                                    mensaje(r);
                                    self.indxElimTipo = -1;
                                })
                    },
                    elimCausa: function ($index, id) {
                        var self = this;
                        self.indxElimCausa = $index;
                        $.post(urlElimCausa, {_token: token, id: id})
                                .then(function (r) {
                                    if (r.tipo == 1) {
                                        if ($index !== -1) {
                                            self.l.causas.splice($index, 1)
                                        }
                                    }
                                    mensaje(r);
                                    self.indxElimCausa = -1;
                                })
                    },
                    traeDatos: function () {
                        var self = this;
                        self.cargaFactor = true;
                        $.post(urlDatos, {_token: token})
                                .then(function (r) {
                                    $.each(r, function (k, v) {
                                        self.l[k] = v;
                                    });
                                    self.cargaFactor = false;
                                })
                    }
                }

            })
        })
    </script>
@stop