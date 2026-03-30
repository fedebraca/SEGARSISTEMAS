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
        <h1 class="ui dividing header">CAUSAS BASICAS</h1>
        <div class="ui form">
            <div class="ui grid three columns">
                <div class="column">
                    <h3>FACTORES</h3>
                    <table class="ui table selectable ">
                        <thead>
                            <tr class="center aligned">
                                <th>Designación</th>
                                <th><i class="icon setting"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="v in l.factores">
                                <td v-on:click="selFactor($index)" v-if="!cargaFactor">
                                    <% v.desig %>
                                    <span v-if="d.factor == $index" style="float: right"><i class="icon green check"></i></span>
                                </td>
                                <td class="center aligned">
                                    <button class="ui mini button icon red" v-on:click="elimFactor($index,v.id)">
                                        <i class="icon" :class="{loading: indxElimFact == $index, refresh: indxElimFact == $index, trash: indxElimFact != $index }"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="text" v-model="g.factor"></td>
                                <td class="center aligned">
                                    <button class="ui mini button icon green" v-on:click="agregaFactor()">
                                        <i class="icon" :class="{loading:agregandoFactor, refresh:agregandoFactor, plus:!agregandoFactor}"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="cargaFactor">
                                <td class="center aligned"><i class="icon loading refresh"></i></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
                            <tr>
                                <td>
                                    <div class="ui icon input fluid">
                                        <input type="text" placeholder="Filtro..." v-model="bTipo">
                                        <i class="circular search link icon"></i>
                                    </div>
                                </td>
                            </tr>
                            <tr v-for="v in l.tipos | filterBy bTipo in 'desig'" class="animated" transition="fade">
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
            var urlDatos = '{{ URL::to('caubasica/datos') }}';
            var urlTipos = '{{ URL::to('caubasica/trae-tipos') }}';
            var urlCausas = '{{ URL::to('caubasica/trae-causas') }}';
            var urlElimFactor = '{{ URL::to('caubasica/elim-factor') }}';
            var urlElimTipo = '{{ URL::to('caubasica/elim-tipo') }}';
            var urlElimCausa = '{{ URL::to('caubasica/elim-causa') }}';
            var urlAgregaFactor = '{{ URL::to('caubasica/agrega-factor') }}';
            var urlAgregaTipo = '{{ URL::to('caubasica/agrega-tipo') }}';
            var urlAgregaCausa = '{{ URL::to('caubasica/agrega-causa') }}';
            var token = '{{ csrf_token()  }}';
            var viewCont = new Vue({
                el: '.viewCont',
                data: {
                    bCausa:'',
                    bTipo:'',
                    cargaFactor: false,
                    cargaTipo: false,
                    cargaCausa: false,
                    agregandoFactor: false,
                    agregandoTipo: false,
                    agregandoCausa: false,
                    indxElimFact: -1,
                    indxElimTipo: -1,
                    indxElimCausa: -1,
                    d: {
                        factor: -1,
                        tipo: -1,
                        causa: -1
                    },
                    g: {
                        factor: '',
                        tipo: '',
                        causa: ''
                    },
                    l: {
                        factores: [],
                        tipos: [],
                        causas: []
                    }
                },
                ready: function () {
                    var self = this;
                    self.traeDatos();
                },
                watch: {
                    'd.factor': function (val) {
                        var self = this;
                        self.cargaTipo = true;
                        self.l.tipos = [];
                        self.l.causas = [];
                        var factor = self.l.factores[self.d.factor].id;
                        $.post(urlTipos, {_token: token, factor: factor})
                                .then(function (r) {
                                    self.l.tipos = r;
                                    self.d.tipo = -1;
                                    self.cargaTipo = false;
                                })
                    },
                    'd.tipo': function (val) {
                        var self = this;
                        if (self.d.tipo == -1) {
                            return;
                        }
                        self.l.causas = [];
                        var factor = self.l.factores[self.d.factor].id;
                        var tipo = self.l.tipos[self.d.tipo].id;
                        self.cargaCausa = true;
                        $.post(urlCausas, {_token: token, factor: factor, tipo: tipo})
                                .then(function (r) {
                                    self.l.causas = r;
                                    self.cargaCausa = false;
                                })
                    }
                },
                methods: {
                    selFactor: function ($index) {
                        var self = this;
                        self.d.factor = $index;
                    },
                    selTipo: function ($index) {
                        var self = this;
                        self.d.tipo = $index;
                    },
                    agregaFactor: function () {
                        var self = this;
                        self.agregandoFactor = true;
                        $.post(urlAgregaFactor, {_token: token, desig: self.g.factor})
                                .then(function (r) {
                                    if (r.result == true) {
                                        self.l.factores.push(Vue.util.extend({}, r.datos));
                                    }
                                    self.agregandoFactor = false;
                                })
                    },
                    agregaTipo: function () {
                        var self = this;
                        self.agregandoTipo = true;
                        var factor = self.l.factores[self.d.factor].id;
                        $.post(urlAgregaTipo, {_token: token, desig: self.g.tipo, factor: factor})
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
                        var factor = self.l.factores[self.d.factor].id;
                        var tipo = self.l.tipos[self.d.tipo].id;
                        $.post(urlAgregaCausa, {_token: token, desig: self.g.causa, factor: factor, tipo: tipo})
                                .then(function (r) {
                                    if (r.result == true) {
                                        self.l.causas.push(Vue.util.extend({}, r.datos));
                                    }
                                    self.agregandoCausa = false;
                                })
                    },
                    elimFactor: function ($index, id) {
                        var self = this;
                        self.indxElimFact = $index;
                        $.post(urlElimFactor, {_token: token, id: id})
                                .then(function (r) {
                                    if (r.tipo == 1) {
                                        if ($index !== -1) {
                                            self.l.factores.splice($index, 1)
                                        }
                                    }
                                    mensaje(r);
                                    self.indxElimFact = -1;
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