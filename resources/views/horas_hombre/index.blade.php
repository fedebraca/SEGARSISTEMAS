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
        <h1 class="ui dividing header">Gestión de Horas Hombre por {{ $tit }}</h1>
        <button class="ui icon button green labeled" v-on:click="mostrarAgregar()"><i class="icon plus"></i> Agregar</button>
        <div class="ui icon input">
            <input type="text" placeholder="Búsqueda..." v-model="busqueda" v-on:change="traeDatos(1)">
            <i class="circular search link icon"></i>
        </div>
        <table class="ui table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th v-if="tipo == 'e'">Empresa</th>
                    <th v-if="tipo == 'c'">Cliente</th>
                    <th v-if="tipo == 'q'">Equipo</th>
                    <th>Mes</th>
                    <th>Año</th>
                    <th>Cantidad</th>
                    <th><i class="icon setting"></i></th>
                </tr>
            </thead>
            <tbody v-show="!cargandoTabla">
                <tr v-for="d in datos">
                    <td><% d.id %></td>
                    <td v-if="tipo == 'e'"><% d.empresa_txt %></td>
                    <td v-if="tipo == 'c'"><% d.cliente_txt %></td>
                    <td v-if="tipo == 'q'"><% d.equipo_txt %></td>
                    <td><% d.mes %></td>
                    <td><% d.ano %></td>
                    <td><% d.cant %></td>
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
                    <div class="field" v-if="tipo == 'e'">
                        <label>Empresa</label>
                        <div class="ui ui search selection dropdown" v-select="e.empresa_id">
                            <input type="hidden" name="empresa_id">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Empresa</div>
                            <div class="menu">
                                <div v-for="itm in l.empresas" class="item" data-value="<% itm.value %>"><% itm.text %></div>
                            </div>
                        </div>
                    </div>
                    <div class="field" v-if="tipo == 'c'">
                        <label>Cliente</label>
                        <div class="ui ui search selection dropdown" v-select="e.cliente_id">
                            <input type="hidden" name="cliente_id">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Cliente</div>
                            <div class="menu">
                                <div v-for="itm in l.clientes" class="item" data-value="<% itm.value %>"><% itm.text %></div>
                            </div>
                        </div>
                    </div>
                    <div class="field" v-if="tipo == 'q'">
                        <label>Equipo</label>
                        <div class="ui ui search selection dropdown" v-select="e.equipo_id">
                            <input type="hidden" name="equipo_id">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Equipo</div>
                            <div class="menu">
                                <div v-for="itm in l.equipos" class="item" data-value="<% itm.value %>"><% itm.text %></div>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label>Mes</label>
                        <input type="text" name="mes" v-model="e.mes">
                    </div>
                    <div class="field">
                        <label>Año</label>
                        <input type="text" name="ano" v-model="e.ano">
                    </div>
                    <div class="field">
                        <label>Cantidad</label>
                        <input type="text" name="cant" v-model="e.cant">
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
                    <div class="field" v-if="tipo == 'e'">
                        <label>Empresa</label>
                        <div class="ui ui search selection dropdown" v-select="d.empresa_id">
                            <input type="hidden" name="empresa_id">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Empresa</div>
                            <div class="menu">
                                <div v-for="itm in l.empresas" class="item" data-value="<% itm.value %>"><% itm.text %></div>
                            </div>
                        </div>
                    </div>
                    <div class="field" v-if="tipo == 'c'">
                        <label>Cliente</label>
                        <div class="ui ui search selection dropdown" v-select="d.cliente_id">
                            <input type="hidden" name="cliente_id">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Cliente</div>
                            <div class="menu">
                                <div v-for="itm in l.clientes" class="item" data-value="<% itm.value %>"><% itm.text %></div>
                            </div>
                        </div>
                    </div>
                    <div class="field" v-if="tipo == 'q'">
                        <label>Equipo</label>
                        <div class="ui ui search selection dropdown" v-select="d.equipo_id">
                            <input type="hidden" name="equipo_id">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Equipo</div>
                            <div class="menu">
                                <div v-for="itm in l.equipos" class="item" data-value="<% itm.value %>"><% itm.text %></div>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label>Mes</label>
                        <div class="ui ui search selection dropdown" v-select="d.mes">
                            <input type="hidden" name="mes">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Mes</div>
                            <div class="menu">
                                <div v-for="(k,v) in meses" class="item" data-value="<% k %>"><% v %></div>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label>Año</label>
                        <input type="number" name="ano" v-model="d.ano">
                    </div>
                    <div class="field">
                        <label>Cantidad</label>
                        <input type="number" name="cant" v-model="d.cant">
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
            var urlAgregar = '{{ URL::to('horas/agregar') }}';
            var urlEdit = '{{ URL::to('horas/editar') }}';
            var urlElim = '{{ URL::to('horas/eliminar') }}';
            var urlActivar = '{{ URL::to('horas/activar') }}';
            var urlTabla = '{{ URL::to('horas/tabla') }}';
            var token = '{{ csrf_token()  }}';
            //////////////////////////////////////////////
            var viewCont = new Vue({
                el: '.viewCont',
                data: {
                    tipo: '{{  $tipo }}',
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
                    meses: {
                        1: 'Enero',
                        2: 'Febrero',
                        3: 'Marzo',
                        4: 'Abril',
                        5: 'Mayo',
                        6: 'Junio',
                        7: 'Julio',
                        8: 'Agosto',
                        9: 'Septiembre',
                        10: 'Octubre',
                        11: 'Noviembre',
                        12: 'Diciembre'
                    },
                    e: {
                        empresa_id: '',
                        cliente_id: '',
                        equipo_id: '',
                        mes: '',
                        ano: '',
                        cant: '',
                        tipo: '{{  $tipo }}'
                    },
                    d: {
                        empresa_id: '',
                        cliente_id: '',
                        equipo_id: '',
                        mes: '',
                        ano: '',
                        cant: '',
                        tipo: '{{  $tipo }}'
                    },
                    l: {
                        empresas: [],
                        clientes: [],
                        equipos:[]
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
                        $.post(urlTabla, {'_token': token, 'page': pag, 'mostrando': self.mostrando, 'busqueda': self.busqueda, 'tipo': self.tipo})
                                .then(function (r) {
                                    for (var i = 1; i <= r.tabla.last_page; i++) {
                                        self.paginas.push(i);
                                    }
                                    $.each(r.list, function (k, v) {
                                        self.l[k] = v;
                                    });
                                    setTimeout(function () {
                                        self.datos = r.tabla.data;
                                        self.currentPage = r.tabla.current_page;
                                        self.hasta = r.tabla.to;
                                        self.desde = r.tabla.from;
                                        self.totalPages = r.tabla.last_page;
                                    }, 0);
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
                observeChanges: true,
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