@extends('layout.default')
@section('title')
    @parent
@stop
@section('js')
    <script src="{{ asset('js/dropzone/dist/min/dropzone.min.js')  }}"></script>
    <script src="{{ asset('js/jstree/dist/jstree.min.js')  }}"></script>
@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('js/dropzone/dist/min/dropzone.min.css')  }}">
    <link rel="stylesheet" href="{{ asset('js/jstree/dist/themes/default/style.min.css')  }}">
    <style>.requerido { color: #db2828; margin-left: 2px; }</style>
@stop
@section('cont')
    <div class="viewCont" v-cloak>
        <h1 class="ui dividing header" v-if="oper == 'add'">INGRESO DE ACCIDENTE</h1>
        <h1 class="ui dividing header" v-if="oper == 'edit'">EDICION DE ACCIDENTE</h1>
        <h1 class="ui dividing header" v-if="oper == 'elim'">ELIMINAR ACCIDENTE</h1>
        <div class="segment ui" v-bind:class="{'loading': cargaGuardar}">
            <div class="ui form">
                <div class="fields">
                    <div class="field">
                        <label>Clasif Interna de Incidente</label>
                        <div class="ui ui search selection dropdown" v-select="d.incidente_id" :class="loading:cargaListas">
                            <input type="hidden" name="incidente_id">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Incidente</div>
                            <div class="menu">
                                <div v-for="inc in l.incidentes" class="item" data-value="<% inc.value %>"><% inc.text %></div>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label>Núm de Siniestro</label>
                        <input type="text" name="num_siniestro" v-model="d.num_siniestro">
                    </div>
                    <div class="field">
                        <label>Cliente <span class="requerido">*</span></label>
                        <div class="ui ui search selection dropdown" v-select="d.cliente_id" :class="loading:cargaListas">
                            <input type="hidden" name="cliente_id">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Cliente</div>
                            <div class="menu">
                                <div v-for="inc in l.clientes" class="item" data-value="<% inc.value %>"><% inc.text %></div>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label>Hubo Accidentado?</label>
                        <div class="ui toggle checkbox" v-check="d.accidentado">
                            <input type="checkbox" name="accidentado"> <% (d.accidentado == true)? 'SI' : 'NO' %>
                        </div>
                    </div>
                    <div class="field" v-if="d.accidentado == true || d.accidentado == 'true'">
                        <label>Nombre y Apellido</label>
                        <input type="text" name="" v-model="d.nom_ape">
                    </div>
                    <div class="field" v-if="d.accidentado == true || d.accidentado == 'true'">
                        <label>Documento</label>
                        <input type="text" name="" v-model="d.doc">
                    </div>
                </div>
                <div class="fields">
                    <div class="field">
                        <label>Empresa</label>
                        <div class="ui ui search selection dropdown" v-select="d.empresa_id" :class="loading:cargaListas">
                            <input type="hidden" name="empresa_id">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Empresa</div>
                            <div class="menu">
                                <div v-for="emp in l.empresas" class="item" data-value="<% emp.value %>"><% emp.text %></div>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label>Lesion / Evento</label>
                        <input type="text" name="" v-model="d.lesion">
                    </div>
                    <div class="field">
                        <label>Descripción</label>
                        <input type="text" name="" v-model="d.descripcion">
                    </div>
                    <div class="field">
                        <label>Fecha Accidente <span class="requerido">*</span></label>
                        <input type="date" name="" v-model="d.f_accidente" placeholder="dd/mm/aaaa">
                    </div>
                    <div class="field">
                        <label>Fecha Alta</label>
                        <input type="date" name="" v-model="d.f_alta" placeholder="dd/mm/aaaa">
                    </div>
                </div>
                <div class="fields">
                    <div class="field">
                        <label>Lugar <span class="requerido">*</span></label>
                        <input type="text" name="lugar" v-model="d.lugar">
                    </div>
                    <div class="field">
                        <label>Días Perdidos</label>
                        <input type="text" name="" v-model="d.dias_perdidos">
                    </div>
                    <div class="field">
                        <label>Denuncia ART</label>
                        <div class="ui ui search selection dropdown" v-select="d.denuncia_art" :class="loading:cargaListas">
                            <input type="hidden" name="denuncia_art">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Opcion</div>
                            <div class="menu">
                                <div v-for="item in opciones" class="item" data-value="<% item.value %>"><% item.text %></div>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label>Itinere</label>
                        <div class="ui ui search selection dropdown" v-select="d.itinere" :class="loading:cargaListas">
                            <input type="hidden" name="itinere">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Opcion</div>
                            <div class="menu">
                                <div v-for="item in opciones" class="item" data-value="<% item.value %>"><% item.text %></div>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label>Tipo Accidente</label>
                        <div class="ui ui search selection dropdown" v-select="d.tipo_accidente_id" :class="loading:cargaListas">
                            <input type="hidden" name="tipo_accidente_id">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Incidente</div>
                            <div class="menu">
                                <div v-for="acc in l.tipoAccidentes" class="item" data-value="<% acc.value %>"><% acc.text %></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="fields">
                    <div class="field">
                        <label>Equipo</label>
                        <div class="ui ui search selection dropdown" v-select="d.equipo_id" :class="loading:cargaListas">
                            <input type="hidden" name="equipo_id">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Equipo</div>
                            <div class="menu">
                                <div v-for="inc in l.equipos" class="item" data-value="<% inc.value %>"><% inc.text %></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="fields">
                    <div class="field">
                        <label>Tipo Causa Inmediata</label>
                        <div class="ui ui search selection dropdown" v-sel-tipo-causa="d.cau_inm_tipo_id" :class="loading:cargaListas">
                            <input type="hidden" name="cau_inm_tipo_id">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Tipo</div>
                            <div class="menu">
                                <div v-for="itm in l.cauInmTipos" class="item" data-value="<% itm.value %>"><% itm.text %></div>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label>Causa Inmediata</label>
                        <div id="cau_inm_id" class="ui ui search selection dropdown" v-select="d.cau_inm_id" :class="loading:cargaListas">
                            <input type="hidden" name="cau_inm_id">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Causa</div>
                            <div class="menu">
                                <div v-for="cau in fija.cauInm" class="item" data-value="<% cau.value %>"><% cau.text %></div>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="fields">
                    <div class="field">
                        <label>Factor Causa Raíz</label>
                        <div class="ui search selection dropdown" v-sel-factor="d.cau_basica_factor_id" :class="loading:cargaListas">
                            <input type="hidden" name="cau_basica_factor_id">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Factor</div>
                            <div class="menu">
                                <div v-for="cau in l.cauBasicaFactr" class="item" data-value="<% cau.value %>"><% cau.text %></div>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label>Tipo Causa Raíz</label>
                        <div id="tipoRaiz" class="ui search selection dropdown" v-sel-tipo="d.cau_basica_tipo_id" :class="loading:cargaListas">
                            <input type="hidden" name="cau_basica_tipo_id">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Tipo</div>
                            <div class="menu">
                                <div v-for="cau in fija.cauBasicaTipo" class="item" data-value="<% cau.value %>"><% cau.text %></div>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label>Causa Básica / Raíz</label>
                        <div id="raiz" class="ui search selection dropdown " v-select="d.cau_basica_id" :class="loading:cargaListas">
                            <input type="hidden" name="cau_basica_id">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Causa</div>
                            <div class="menu">
                                <div v-for="cau in fija.cauBasica" class="item" data-value="<% cau.value %>"><% cau.text %></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="fields">
                    <div class="field">
                        <label>Acción Correctiva 1</label>
                        <input type="text" name="" v-model="d.acc_correct_1">
                    </div>
                    <div class="field">
                        <label>Cumplimiento 1</label>
                        <div class="ui ui search selection dropdown" v-select="d.cumpl_1" :class="loading:cargaListas">
                            <input type="hidden" name="cumpl_1">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Opcion</div>
                            <div class="menu">
                                <div v-for="item in porcent" class="item" data-value="<% item.value %>"><% item.text %></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="fields">
                    <div class="field">
                        <label>Acción Correctiva 2</label>
                        <input type="text" name="" v-model="d.acc_correct_2">
                    </div>
                    <div class="field">
                        <label>Cumplimiento 2</label>
                        <div class="ui ui search selection dropdown" v-select="d.cumpl_2" :class="loading:cargaListas">
                            <input type="hidden" name="cumpl_2">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Opcion</div>
                            <div class="menu">
                                <div v-for="item in porcent" class="item" data-value="<% item.value %>"><% item.text %></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="fields">
                    <div class="field">
                        <label>Acción Correctiva 3</label>
                        <input type="text" name="" v-model="d.acc_correct_3">
                    </div>
                    <div class="field">
                        <label>Cumplimiento 3</label>
                        <div class="ui ui search selection dropdown" v-select="d.cumpl_3" :class="loading:cargaListas">
                            <input type="hidden" name="cumpl_3">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Opcion</div>
                            <div class="menu">
                                <div v-for="item in porcent" class="item" data-value="<% item.value %>"><% item.text %></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="fields">
                    <div class="field">
                        <label>% Cumplido</label>
                        <input type="text" name="" v-model="d.cumplido">
                    </div>
                    <div class="field">
                        <label>Alerta de Seguridad</label>
                        <div class="ui ui search selection dropdown" v-select="d.alerta_seg" :class="loading:cargaListas">
                            <input type="hidden" name="alerta_seg">
                            <i class="dropdown icon"></i>

                            <div class="default text">Seleccione Opcion</div>
                            <div class="menu">
                                <div v-for="item in opciones" class="item" data-value="<% item.value %>"><% item.text %></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div>
                <form class="dropzone ui form" id="dropzone" role="form" method="POST">{!! csrf_field() !!}</form>
            </div>
            <div v-show="oper == 'edit'">
                <div v-if="d.archivo">Archivo 1: <a href="{{ asset('adjuntos') }}/<% d.archivo %>" target="_blank"><% d.archivo %></a></div>
                <div v-if="d.archivo2">Archivo 2: <a href="{{ asset('adjuntos') }}/<% d.archivo2 %>" target="_blank"><% d.archivo2 %></a></div>
                <div v-if="d.archivo3">Archivo 3: <a href="{{ asset('adjuntos') }}/<% d.archivo3 %>" target="_blank"><% d.archivo3 %></a></div>
            </div>
            <br>
            <button v-if="oper == 'add'" type="button" class="ui icon button green labeled" v-on:click="agregar()"><i class="icon plus"></i> Agregar</button>
            <button v-if="oper == 'edit'" type="button" class="ui icon button green labeled" v-on:click="guardar()"><i class="icon save"></i> Guardar</button>
            <button v-if="oper == 'elim'" type="button" class="ui icon button red labeled" v-on:click="eliminar()"><i class="icon trash"></i> Confirme Eliminación</button>
        </div>

    </div>

@stop
@section('script')
    <script>
        $(document).ready(function () {
            Dropzone.autoDiscover = false;
            var urlAgregar = '{{ URL::to('accidente/agregar') }}';
            var urlGuardar = '{{ URL::to('accidente/guardar') }}';
            var urlElim = '{{ URL::to('accidente/eliminar') }}';
            var urlConfig = '{{ URL::to('accidente/config') }}';
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
//                            $('#cau_inm_id').dropdown('clear');
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
                    opciones: [
                        {text: 'SI', value: 1},
                        {text: 'NO', value: 2},
                        {text: 'N/A', value: 2}
                    ],
                    porcent: [
                        {text: '<25', value: 25},
                        {text: '<50', value: 50},
                        {text: '<75', value: 75},
                        {text: '100', value: 100}
                    ],
                    _token: token,
                    d: {
                        '_token': token,
                        id: '{{  (isset($id))? $id : '' }}',
                        incidente_id: '',
                        num_siniestro: '',
                        accidentado: false,
                        cliente_id: '',
                        equipo_id: '',
                        nom_ape: '',
                        doc: '',
                        empresa_id: '',
                        lesion: '',
                        descripcion: '',
                        f_accidente: '',
                        f_alta: '',
                        lugar: '',
                        dias_perdidos: '',
                        denuncia_art: false,
                        itinere: '',
                        tipo_accidente_id: '',
                        cau_inm_id: '',
                        cau_basica_id: '',
                        acc_correct_1: '',
                        acc_correct_2: '',
                        acc_correct_3: '',
                        cumpl_1: '',
                        cumpl_2: '',
                        cumpl_3: '',
                        cumplido: '',
                        alerta_seg: '',
                        archivo: '',
                        archivo2: '',
                        archivo3: '',
                        cau_inm_tipo_id: '',
                        cau_basica_factor_id: '',
                        cau_basica_tipo_id: ''
                    },
                    cau_inm_txt: '',
                    fija: {
                        cauInm: [],
                        cauBasica: [],
                        cauBasicaTipo: []
                    },
                    l: {
                        tipoAccidentes: [],
                        incidentes: [],
                        empresas: [],
                        cauInmTipos: [],
                        cauBasicaFactr: [],
                        clientes: [],
                        equipos: []
                    },
                    cargaListas: false
                },
                ready: function () {
                    var self = this;
                    var url = '';
                    if (self.oper == 'add') {
                        url = urlAgregar
                    } else if (self.oper == 'edit') {
                        url = urlGuardar;
                    }
                    this.modalCausas = $('.ui.modal.causas').modal();
                    this.drop = new Dropzone("form#dropzone", {
                        url: url,
                        uploadMultiple: true,
                        parallelUploads: 3,
                        autoProcessQueue: false,
                        addRemoveLinks: true,
                        createImageThumbnails: true,
                        maxFiles: 3,
                        dictDefaultMessage: 'Haga click aquí o arrastre hasta 3 archivos para subir',
                        dictRemoveFile: 'Borrar archivo'
                    });
                    this.getConfig();
                },
                methods: {
                    mostrarModalCausa: function () {
                        var self = this;
                        self.modalCausas.modal('show');
                    },
                    actualizaCausaInm: function () {
                        var self = this;
                        self.cargaListas = true;
                        $.post(urlCauInm, {_token: token, tipo: self.d.cau_inm_tipo_id})
                                .then(function (r) {
                                    self.fija.cauInm = r;
                                    self.cargaListas = false;
                                }, function () {
                                    self.cargaListas = false;
                                });
                    },
                    actualizaTipoRaiz: function () {
                        var self = this;
                        self.cargaListas = true;
                        $.post(urlTipoRaiz, {_token: token, factor: self.d.cau_basica_factor_id})
                                .then(function (r) {
                                    self.fija.cauBasicaTipo = r;
                                    self.cargaListas = false;
                                }, function () {
                                    self.cargaListas = false;
                                });
                    },
                    actualizaCauBasica: function () {
                        var self = this;
                        self.cargaListas = true;
                        $.post(urlCauBasica, {_token: token, factor: self.d.cau_basica_factor_id, tipo: self.d.cau_basica_tipo_id})
                                .then(function (r) {
                                    self.fija.cauBasica = r;
                                    self.cargaListas = false;
                                }, function () {
                                    self.cargaListas = false;
                                });
                    },
                    getConfig: function () {
                        var self = this;
                        self.cargaListas = true;
                        $.post(urlConfig, {_token: token, id: self.d.id})
                                .then(function (r) {
                                    $.each(r, function (k, v) {
                                        self.l[k] = v;
                                    });
                                    if (self.oper == 'edit' || self.oper == 'elim') {
                                        if (r.accidente.causa_inm) {
                                            self.fija.cauInm = [{text: r.accidente.causa_inm.desig, value: r.accidente.causa_inm.id}];
                                        }
                                        if (r.accidente.causa_basica) {
                                            self.fija.cauBasica = [{text: r.accidente.causa_basica.desig, value: r.accidente.causa_basica.id}];
                                        }
                                        if (r.accidente.causa_basica_tipo) {
                                            self.fija.cauBasicaTipo = [{text: r.accidente.causa_basica_tipo.desig, value: r.accidente.causa_basica_tipo.id}];
                                        }

                                        setTimeout(function () {
                                            self.d = r.accidente;
                                        }, 500);

                                    }
                                    self.cargaListas = false;
                                }, function () {
                                    self.cargaListas = false;
                                });
                    },
                    agregar: function () {
                        var self = this;
                        if (!self.d.cliente_id) {
                            mensaje({tipo: 2, txt: 'El campo Cliente es requerido'});
                            return;
                        }
                        if (!self.d.f_accidente) {
                            mensaje({tipo: 2, txt: 'El campo Fecha Accidente es requerido'});
                            return;
                        }
                        if (!self.d.lugar) {
                            mensaje({tipo: 2, txt: 'El campo Lugar es requerido'});
                            return;
                        }
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
                        if (!self.d.cliente_id) {
                            mensaje({tipo: 2, txt: 'El campo Cliente es requerido'});
                            return;
                        }
                        if (!self.d.f_accidente) {
                            mensaje({tipo: 2, txt: 'El campo Fecha Accidente es requerido'});
                            return;
                        }
                        if (!self.d.lugar) {
                            mensaje({tipo: 2, txt: 'El campo Lugar es requerido'});
                            return;
                        }
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
                            self.d['_token'] = token;
                            $.post(urlGuardar, self.d)
                                    .then(function (r) {
                                        mensaje(r);
                                        self.cargaGuardar = false;
                                    }, function () {
                                        mensaje({tipo: 2, txt: 'Error de Conexión'});
                                        self.cargaGuardar = false;
                                    });
                        }

                    },
                    eliminar: function () {
                        var self = this;
                        $.post(urlElim, {'_token': token, id: self.d.id})
                                .then(function (r) {
                                    mensaje(r);
                                    self.cargaGuardar = false;
                                }, function () {
                                    mensaje({tipo: 2, txt: 'Error de Conexión'});
                                    self.cargaGuardar = false;
                                });
                    }
                }

            })
        })
    </script>
@stop