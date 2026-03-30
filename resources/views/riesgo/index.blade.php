@extends('layout.default')
@section('title')
    @parent
@stop
@section('js')
    <script src="{{ asset('js/dropzone/dist/min/dropzone.min.js')  }}"></script>
    <script src="{{ asset('js/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js')  }}"></script>
@stop
@section('css')
    {{--<link rel="stylesheet" href="{{ asset('jslib/dropzone/dist/min/basic.min.css')  }}">--}}
    <link rel="stylesheet" href="{{ asset('js/dropzone/dist/min/dropzone.min.css')  }}">
@stop
@section('cont')
    <div class="viewCont" v-cloak>
        <h1 class="ui dividing header" v-if="oper == 'add'">INGRESO DE RIESGO</h1>
        <h1 class="ui dividing header" v-if="oper == 'edit'">EDICION DE RIESGO</h1>

        <div class="ui form">
            <div class="fields">
                <div class="field">
                    <label>Descripción</label>
                    <input type="text" name="desc" v-model="d.desc">
                </div>
                <div class="field">
                    <label>Cliente</label>

                    <div class="ui search selection dropdown" v-select="d.cliente_id" :class="{loading:cargaConfig}">
                        <input type="hidden" name="cliente_id">
                        <i class="dropdown icon"></i>

                        <div class="default text">Seleccione Cliente</div>
                        <div class="menu">
                            <div v-for="cli in l.clientes" class="item" data-value="<% cli.value %>"><% cli.text %></div>
                        </div>
                    </div>
                </div>
                <div class="field">
                    <label>Fecha Riesgo</label>
                    <input type="date" name="friesgo" v-model="d.friesgo">
                </div>
                <div class="field">
                    <label>Lugar</label>
                    <input type="text" name="lugar" v-model="d.lugar">
                </div>
            </div>
            <div class="field">
                <label>Comentario</label>
                <textarea name="coment" v-model="d.coment"></textarea>
            </div>

        </div>
        <div class="ui divider"></div>
        <div>
            <form class="dropzone ui form" id="dropzone" role="form" method="POST">
                {!! csrf_field() !!}
            </form>
        </div>
        <div v-show="oper == 'edit'">
            Archivo: <a href="{{ asset('adjuntos') }}/<% d.archivo %>"><% d.archivo %></a>
        </div>
        <br>
        <button v-if="oper == 'add'" type="button" class="ui icon button green labeled" v-on:click="agregar()"><i class="icon plus"></i> Agregar</button>
        <button v-if="oper == 'edit'" type="button" class="ui icon button green labeled" v-on:click="guardar()"><i class="icon save"></i> Guardar</button>
    </div>

@stop
@section('script')
    <script>
        $(document).ready(function () {
            Dropzone.autoDiscover = false;
            var urlAgregar = '{{ URL::to('riesgo/agregar') }}';
            var urlGuardar = '{{ URL::to('riesgo/guardar') }}';
            var urlConfig = '{{ URL::to('riesgo/config') }}';
            var urlCauInm = '{{ URL::to('cauinm/listado') }}';
            var urlTipoRaiz = '{{ URL::to('caubasica/listado-tipo') }}';
            var urlCauBasica = '{{ URL::to('caubasica/listado') }}';
            var token = '{{ csrf_token()  }}';

            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
            Vue.directive('selTipoCausa', {
                twoWay: true,
                bind: function () {
                    var self = this;
                    $(self.el).dropdown({
                        onChange: function (value, text, $choice) {
                            self.set(value);
                            $('#cau_inm_id').dropdown('clear');
                            viewCont.actualizaCausaInm();
                        }
                    });
                    $(self.el).dropdown('refresh');
                },
                update: function (newValue, oldValue) {
                    var self = this;
                    // do something based on the updated value
                    // this will also be called for the initial value
                    $(self.el).dropdown('set selected', newValue);
                    $(self.el).dropdown('refresh');
                },
                unbind: function () {
                    // do clean up work
                    // e.g. remove event listeners added in bind()
                }
            });
            Vue.directive('selFactor', {
                twoWay: true,
                bind: function () {
                    var self = this;
                    $(self.el).dropdown({
                        onChange: function (value, text, $choice) {
                            self.set(value);
                            $('#tipoRaiz').dropdown('clear');
                            viewCont.actualizaTipoRaiz();
                        }
                    });
                    $(self.el).dropdown('refresh');
                },
                update: function (newValue, oldValue) {
                    var self = this;
                    // do something based on the updated value
                    // this will also be called for the initial value
                    $(self.el).dropdown('set selected', newValue);
                    $(self.el).dropdown('refresh');
                },
                unbind: function () {
                    // do clean up work
                    // e.g. remove event listeners added in bind()
                }
            });
            Vue.directive('selTipo', {
                twoWay: true,
                bind: function () {
                    var self = this;
                    $(self.el).dropdown({
                        onChange: function (value, text, $choice) {
                            self.set(value);
                            $('#raiz').dropdown('clear');
                            viewCont.actualizaCauBasica();
                        }
                    });
                    $(self.el).dropdown('refresh');
                },
                update: function (newValue, oldValue) {
                    var self = this;
                    // do something based on the updated value
                    // this will also be called for the initial value
                    $(self.el).dropdown('set selected', newValue);
                    $(self.el).dropdown('refresh');
                },
                unbind: function () {
                    // do clean up work
                    // e.g. remove event listeners added in bind()
                }
            });
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
            var viewCont = new Vue({
                el: '.viewCont',
                data: {
                    oper: '{{  (isset($oper))? $oper : '' }}',
                    cargaGuardar: false,
                    modalCausas: {},
                    _token: token,
                    d: {
                        _token: token,
                        id: '{{  (isset($id))? $id : '' }}',
                        cliente_id: '',
                        desc: '',
                        coment: '',
                        friesgo: '',
                        lugar: ''
                    },
                    l: {
                        clientes: []
                    },
                    cargaListas: false
                },
                ready: function () {
                    var self = this;
                    var url = '';
                    if (self.oper == 'add') {
                        url = urlAgregar;
                    } else if (self.oper == 'edit') {
                        url = urlGuardar;
                    }
                    this.drop = new Dropzone("form#dropzone", {
                        url: url,
                        uploadMultiple: true,
                        autoProcessQueue: false,
                        addRemoveLinks: true,
                        createImageThumbnails: true,
                        dictDefaultMessage: 'Haga click aqui o arrastre un archivo para subir'
                    });
                    this.getConfig();
                },
                methods: {
                    getConfig: function () {
                        var self = this;
                        self.cargaListas = true;
                        $.post(urlConfig, {_token: token, id: self.d.id})
                                .then(function (r) {
                                    $.each(r, function (k, v) {
                                        self.l[k] = v;
                                    });
                                    if (self.oper == 'edit') {
                                        setTimeout(function () {
                                            self.d = r.riesgo;
                                        }, 0);

                                    }
                                    self.cargaListas = false;
                                }, function () {
                                    self.cargaListas = false;
                                });
                    },
                    agregar: function () {
                        var self = this;
                        var archivos = self.drop.getQueuedFiles();
                        self.cargaGuardar = true;
                        if (archivos.length > 0) {
                            self.drop.options.params = self.d;
                            self.drop.on("success", function (result) {
                                var msje = jQuery.parseJSON(result.xhr.response);
                                mensaje(msje);
                                self.cargaGuardar = false;
                                $('.ui.form').form('clear');
                                self.drop.removeAllFiles();
                            });
                            self.drop.processQueue();
                        } else {
                            $.post(urlAgregar, self.d)
                                    .then(function (r) {
                                        mensaje(r);
                                        $('.ui.form').form('clear');
                                        self.cargaGuardar = false;
                                    }, function () {
                                        mensaje({tipo: 2, txt: 'Error de Conexión'});
                                        self.cargaGuardar = false;
                                    });
                        }

                    },
                    guardar: function () {
                        var self = this;
                        var archivos = self.drop.getQueuedFiles();
                        self.cargaGuardar = true;
                        if (archivos.length > 0) {
                            self.drop.options.params = self.d;
                            self.drop.on("success", function (result) {
                                var msje = jQuery.parseJSON(result.xhr.response);
                                mensaje(msje);
                                self.cargaGuardar = false;
                                self.drop.removeAllFiles();
                            });
                            self.drop.processQueue();
                        } else {
                            $.post(urlGuardar, {'_token': token, datos: self.d})
                                    .then(function (r) {
                                        mensaje(r);
                                        self.cargaGuardar = false;
                                    }, function () {
                                        mensaje({tipo: 2, txt: 'Error de Conexión'});
                                        self.cargaGuardar = false;
                                    });
                        }

                    }
                }

            })
        })
    </script>
@stop