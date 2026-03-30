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
        <h1 class="ui dividing header">Listado de Usuarios</h1>
        <a href="{{ URL::to('usuario/crear') }}" class="ui button green">AGREGAR</a>
        <div class="ui icon input">
            <input type="text" placeholder="Búsqueda..." v-model="busqueda" v-on:change="traeDatos(1)">
            <i class="circular search link icon"></i>
        </div>
        <table class="ui table">
            <thead>
                <tr class="center aligned">
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Tipo</th>
                    <th>Vista</th>
                    <th><i class="icon setting"></i></th>
                </tr>
            </thead>
            <tbody v-show="!cargandoTabla">
                <tr v-for="d in datos">
                    <td><% d.name %></td>
                    <td><% d.email %></td>
                    <td>
                        <div class="ui ui search selection dropdown" v-select="d.tipo">
                            <input type="hidden" name="tipo">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Tipo</div>
                            <div class="menu">
                                <div v-for="itm in tipos" class="item" data-value="<% itm.value %>"><% itm.text %></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="ui ui search selection dropdown" v-select="d.vista">
                            <input type="hidden" name="vista">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Vista</div>
                            <div class="menu">
                                <div v-for="itm in vistas" class="item" data-value="<% itm.value %>"><% itm.text %></div>
                            </div>
                        </div>
                    </td>
                    <td class="center aligned">
                        <div class="ui mini icon buttons">
                            <button class="ui blue button" v-on:click="guardar($index,d.id)" :class="{loading: guardando == $index}">
                                <i class="save icon"></i>
                            </button>
                            <a class="ui green button" href="{{ URL::to('usuario/editar') }}/<% d.id %>">
                                <i class="edit icon"></i>
                            </a>
                            <button class="ui red button" v-on:click="confirmaElim($index,d.id)" :class="{loading: eliminando == $index}">
                                <i class="trash icon"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tbody v-show="cargandoTabla">
                <tr>
                    <td colspan="6" class="center aligned"><i class="icon spinner loading"></i> Cargando...</td>
                </tr>
            </tbody>

        </table>
        {{--///////////////////////////////////////////////////////////////////////////////////////////////////--}}
        <div class="ui small pagination menu">
            <a class="item" v-show="currentPage != 1" v-on:click="traeDatos(1)">
                Inicio
            </a>
            <a class="icon item" v-show="currentPage != 1" v-on:click="traeDatos(currentPage-1)">
                <i class="left arrow icon"></i>
            </a>

            <div class="item">
                Página <% currentPage %> de <% totalPages %>
            </div>
            <div class="ui simple dropdown item" v-select="">
                Mostrar...
                <i class="dropdown icon"></i>

                <div class="menu">
                    <div class="item" v-on:click="mostrar(5)">5 items</div>
                    <div class="item" v-on:click="mostrar(10)">10 items</div>
                    <div class="item" v-on:click="mostrar(15)">15 items</div>
                </div>
            </div>
            <div class="item">
                <div class="ui category search item">
                    <div class="ui transparent icon input">
                        <input class="prompt" type="text" placeholder="Ir a pagina..." v-on:change="traeDatos(irA)" v-model="irA">
                        <i class="search link icon"></i>
                    </div>
                </div>
            </div>
            <a class="icon item" v-show="currentPage != totalPages" v-on:click="traeDatos(currentPage+1)">
                <i class="right arrow icon"></i>
            </a>
            <a class="item" v-show="currentPage != totalPages" v-on:click="traeDatos(totalPages)">
                Fin
            </a>
        </div>
        <div class="ui small modal eliminar">
            <i class="close icon"></i>

            <div class="header">CONFIRMAR ELIMINACIÓN</div>
            <div class="content">
                <div class="description">¿Confirma la eliminación del registro?</div>
            </div>
            <div class="actions">
                <div class="ui button negative">NO</div>
                <div class="ui button positive">SI</div>
            </div>
        </div>
    </div>
@stop
@section('script')
    <script>
        $(document).ready(function () {
            var urlTabla = '{{ URL::to('usuario/tabla') }}';
            var urlGuardar = '{{ URL::to('usuario/guardar-tabla') }}';
            var urlElim = '{{ URL::to('usuario/eliminar') }}';
            var token = '{{ csrf_token()  }}';
            //////////////////////////////////////////////
            var viewCont = new Vue({
                el: '.viewCont',
                data: {
                    eliminando: -1,
                    elimID: '',
                    elimIndx: '',
                    busqueda: '',
                    cargandoTabla: false,
                    guardando: -1,
                    datos: [],
                    currentPage: 1,
                    desde: 0,
                    hasta: 0,
                    totalPages: 0,
                    paginas: [],
                    irA: '',
                    mostrando: 5,
                    idActivo: '',
                    idxActivo: '',
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
                created: function () {
                    this.traeDatos(1);
                },
                methods: {
                    mostrarAgregar: function () {
                        $('.ui.modal.agregar').modal('show');
                    },
                    agregar: function () {
                        var self = this;
                        self.cargandoAgregar = true;
                        $.post(urlAgregar, {'_token': token, 'datos': self.d})
                                .then(function (r) {
                                    if (r.result == 'success') {
                                        self.traeDatos(self.currentPage);
                                        self.d = {};
                                        $('.ui.modal.agregar').modal('hide');
                                    }
                                    self.cargandoAgregar = false;
                                }, function () {
                                    $('.ui.modal.agregar').modal('hide');
                                    self.cargandoAgregar = false;
                                })
                    },
                    mostrar: function (cant) {
                        var self = this;
                        self.mostrando = cant;
                        self.traeDatos(self.currentPage);
                    },
                    traeDatos: function (pag) {
                        var self = this;
                        self.cargandoTabla = true;
                        $.post(urlTabla, {'_token': token, 'page': pag, 'mostrando': self.mostrando, 'busqueda': self.busqueda})
                                .then(function (r) {
                                    for (var i = 1; i <= r.last_page; i++) {
                                        self.paginas.push(i);
                                    }
                                    self.datos = r.data;
                                    self.currentPage = r.current_page;
                                    self.hasta = r.to;
                                    self.desde = r.from;
                                    self.totalPages = r.last_page;
                                    self.cargandoTabla = false;
                                }, function () {
                                    self.cargandoTabla = false;
                                })
                    },
                    guardar: function ($index, id) {
                        var self = this;
                        self.guardando = $index;
                        $.post(urlGuardar, {'_token': token, d: self.datos[$index]})
                                .then(function (r) {
                                    mensaje(r);
                                    self.guardando = -1;
                                }, function () {
                                    self.guardando = -1;
                                })
                    },
                    confirmaElim: function ($index, id) {
                        var self = this;
                        self.elimID = id;
                        self.elimIndx = $index;
                        $('.ui.modal.eliminar').modal('show');
                    },
                    eliminar: function () {
                        var self = this;
                        self.eliminando = self.elimIndx;
                        $.post(urlElim, {'_token': token, id: self.elimID})
                                .then(function (r) {
                                    mensaje(r);
                                    $('.ui.modal.eliminar').modal('hide');
                                    self.eliminando = -1;
                                }, function () {
                                    self.eliminando = -1;
                                })
                    }
                }
            });
            $('.ui.modal.eliminar').modal({
                onApprove: function () {
                    viewCont.eliminar();
                    return false;
                }
            });
        })
    </script>
@stop