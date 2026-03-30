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
        <h1 class="ui dividing header">Listado de Riesgos</h1>
        <a href="{{ URL::to('riesgo/agregar') }}" class="ui button green">AGREGAR</a>
        <div class="ui icon input">
            <input type="text" placeholder="Búsqueda..." v-model="busqueda" v-on:change="traeDatos(1)">
            <i class="circular search link icon"></i>
        </div>
        <div class="table-contenedor">
            <table class="ui table small compact celled unstackable">
                <thead class="center aligned">
                    <tr>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Descripción</th>
                        <th>Comentario</th>
                        <th>Lugar</th>
                        <th>Operación</th>
                    </tr>
                </thead>
                <tbody v-show="!cargandoTabla">
                    <tr v-for="d in datos">
                        <td class="center aligned"><% d.friesgo | fecha %></td>
                        <td><% d.cliente.rzon_soc %></td>
                        <td><% d.desc %></td>
                        <td><% d.coment %></td>
                        <td><% d.lugar %></td>
                        <td class="center aligned">
                            <div class="ui mini icon buttons">
                                <a class="ui blue button" href="{{ URL::to('riesgo/editar')  }}/<% d.ident %>">
                                    <i class="edit icon"></i>
                                </a>
                                <button class="ui red button" v-on:click="confirmaElim($index,d.ident)">
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
        </div>
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
    </div>
@stop
@section('script')
    <script>
        $(document).ready(function () {
            var urlTabla = '{{ URL::to('riesgo/tabla') }}';
            var urlElim = '{{ URL::to('riesgo/eliminar') }}';
            var token = '{{ csrf_token()  }}';
            ///////////////////////////////////////////////////////////////////////////////////////
            Vue.filter('porcent', function (value) {
                if (value < 100) {
                    return '<' + value;
                } else if (value == 100) {
                    return value;
                }
            });
            ///////////////////////////////////////////////////////////////////////////////////////
            var viewCont = new Vue({
                el: '.viewCont',
                data: {
                    busqueda: '',
                    cargandoTabla: false,
                    datos: [],
                    currentPage: 1,
                    desde: 0,
                    hasta: 0,
                    totalPages: 0,
                    paginas: [],
                    irA: '',
                    mostrando: 20,
                    idActivo: '',
                    idxActivo: '',
                    cargandoElim:-1
                },
                created: function () {
                    this.traeDatos(1);
                },
                methods: {
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
                                    if (r.tipo == 1) {
                                        self.datos.splice(self.idxActivo, 1);
                                        $('.ui.modal.eliminar').modal('hide');
                                    }
                                    mensaje(r);
                                    self.cargandoElim = -1;
                                }, function () {
                                    $('.ui.modal.eliminar').modal('hide');
                                    self.cargandoElim = -1;
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