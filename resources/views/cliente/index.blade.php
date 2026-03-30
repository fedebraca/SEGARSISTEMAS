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
        <h1 class="ui dividing header">Gestión de Clientes</h1>
        <button class="ui icon button green labeled" v-on:click="mostrarAgregar()"><i class="icon plus"></i> Agregar</button>
        <div class="ui icon input">
            <input type="text" placeholder="Búsqueda..." v-model="busqueda" v-on:change="traeDatos(1)">
            <i class="circular search link icon"></i>
        </div>
        <table class="ui table">
            <thead>
                <tr>
                    <th>Razon Social</th>
                    <th>Activo</th>
                    <th>Operación</th>
                </tr>
            </thead>
            <tbody v-show="!cargandoTabla">
                <tr v-for="d in datos">
                    <td><% d.rzon_soc %></td>
                    <td v-if="cargandoActivo != $index">
                        <i v-if="d.activo == 1" class="icon green checkmark" v-on:click="activar($index,d.id,0)"></i>
                        <i v-if="d.activo == 0" class="icon red remove" v-on:click="activar($index,d.id,1)"></i>
                    </td>
                    <td v-if="cargandoActivo == $index"><i class="spinner loading icon"></i></td>
                    <td>
                        <div class="ui mini icon buttons">
                            <button class="ui blue button" v-on:click="editar($index,d.id)">
                                <i class="edit icon"></i>
                            </button>
                            <button class="ui red button" v-on:click="confirmaElim($index,d.id)">
                                <i class="icon" :class="{spinner: cargandoElim == $index,loading: cargandoElim == $index, trash: cargandoElim != $index }"></i>
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
        {{--///////////////////////////////////////////////////////////////////////////////////////////////////--}}
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
        {{--///////////////////////////////////////////////////////////////////////////////////////////////////--}}
        <div class="ui small modal editar">
            <i class="close icon"></i>

            <div class="header">EDICIÓN DE REGISTRO</div>
            <div class="content">
                <div class="ui form editar">
                    <div class="field">
                        <label>Descripción</label>
                        <input type="text" name="desig" v-model="e.rzon_soc">
                    </div>
                </div>
            </div>
            <div class="actions">
                <div class="ui icon button labeled negative"><i class="icon remove"></i> Cancelar</div>
                <div class="ui icon button labeled positive"><i class="icon checkmark"></i> Guardar</div>
            </div>
        </div>
        {{--///////////////////////////////////////////////////////////////////////////////////////////////////--}}
        <div class="ui small modal agregar">
            <i class="close icon"></i>

            <div class="header">AGREGAR REGISTRO</div>
            <div class="content">
                <div class="ui form agregar">
                    <div class="field">
                        <label>Descripción</label>
                        <input type="text" name="desig" v-model="d.rzon_soc">
                    </div>
                </div>
            </div>
            <div class="actions">
                <div class="ui icon button labeled negative"><i class="icon remove"></i> Cancelar</div>
                <div class="ui icon button labeled positive"><i class="icon checkmark"></i> Guardar</div>
            </div>
        </div>
        {{--///////////////////////////////////////////////////////////////////////////////////////////////////--}}
    </div>
@stop
@section('script')
    <script>
        $(document).ready(function () {
            var urlAgregar = '{{ URL::to('cliente/agregar') }}';
            var urlEdit = '{{ URL::to('cliente/editar') }}';
            var urlElim = '{{ URL::to('cliente/eliminar') }}';
            var urlActivar = '{{ URL::to('cliente/activar') }}';
            var urlTabla = '{{ URL::to('cliente/tabla') }}';
            var token = '{{ csrf_token()  }}';
            //////////////////////////////////////////////
            var viewCont = new Vue({
                el: '.viewCont',
                data: {
                    busqueda: '',
                    cargandoActivo: -1,
                    cargandoElim: -1,
                    cargandoEdit: false,
                    cargandoTabla: false,
                    cargandoAgregar: false,
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
                    e: {
                        rzon_soc: ''
                    },
                    d: {
                        rzon_soc: ''
                    }
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
                    confirmaEdit: function () {
                        var self = this;
                        self.cargandoEdit = true;
                        $.post(urlEdit, {'_token': token, 'datos': self.e})
                                .then(function (r) {
                                    if (r.result == 'success') {
                                        self.traeDatos(self.currentPage);
                                        self.e = {};
                                        $('.ui.modal.editar').modal('hide');
                                    }
                                    self.cargandoEdit = false;
                                }, function () {
                                    $('.ui.modal.editar').modal('hide');
                                    self.cargandoEdit = false;
                                })
                    },
                    editar: function ($indx, id) {
                        var self = this;
                        self.idActivo = id;
                        self.idxActivo = $indx;
                        self.e = self.datos[$indx];
                        $('.ui.modal.editar').modal('show');
                    },
                    confirmaElim: function ($indx, id) {
                        var self = this;
                        self.idActivo = id;
                        self.idxActivo = $indx;
                        $('.ui.modal.eliminar').modal('show');
                    },
                    eliminar: function () {
                        var self = this;
                        self.cargandoElim = self.idxActivo;
                        $.post(urlElim, {'_token': token, 'id': self.idActivo})
                                .then(function (r) {
                                    if (r.result == 'success') {
                                        self.datos.splice(self.idxActivo, 1);
                                        $('.ui.modal.eliminar').modal('hide');
                                    }
                                    self.cargandoElim = -1;
                                }, function () {
                                    $('.ui.modal.eliminar').modal('hide');
                                    self.cargandoElim = -1;
                                })
                    },
                    activar: function ($indx, id, estado) {
                        var self = this;
                        self.cargandoActivo = $indx;
                        $.post(urlActivar, {'_token': token, 'estado': estado, 'id': id})
                                .then(function (r) {
                                    if (r.result == 'success') {
                                        self.datos[$indx].activo = estado;
                                    }
                                    self.cargandoActivo = -1;
                                }, function () {
                                    self.cargandoActivo = -1;
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
                    guardar: function () {
                        var self = this;
                    }
                }
            });
            $('.ui.modal.eliminar').modal({
                onApprove: function () {
                    viewCont.eliminar();
                    return false;
                }
            });
            $('.ui.modal.editar').modal({
                onApprove: function () {
                    viewCont.confirmaEdit();
                    return false;
                }
            });
            $('.ui.modal.agregar').modal({
                onApprove: function () {
                    viewCont.agregar();
                    return false;
                }
            });
        })
    </script>
@stop