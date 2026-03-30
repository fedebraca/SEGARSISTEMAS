@extends('layout.default')
@section('title')
    @parent
@stop
@section('js')

@stop
@section('css')

@stop
@section('cont')
    <div v-cloak>
        <div v-if="!showForm">
            <h1 class="ui header">Listado de Observaciones</h1>
            <button class="ui button green labeled icon" v-on:click="abreAgregar()"><i class="icon plus"></i>Agregar
            </button>

            <div class="ui divider"></div>
            <tabla url="obs/tabla" ref="tabla" :edit-local="true"
                   v-on:click-edit="abreEditar($event)"
                   v-on:click-eliminar="abreEliminar($event)"
                   v-on:click-ver="abreVer($event)"
                   class="animated fadeIn"
                   url-descarga="{{ asset('/informe.xlsx') }}"
            ></tabla>
        </div>

        <div class="ui raised segment" v-if="showForm">
            <div class="one column stackable ui grid animated fadeIn">
                <div class="column">
                    <h1 class="ui header">@{{ operacion }} Observación</h1>
                    <div class="ui form" :class="{loading:agregando}">
                        <div class="fields">
                            <div class="field">
                                <label>Fecha Observación</label>
                                <selector-fecha v-model="d.fecha_obs"></selector-fecha>
                            </div>
                            <div class="field readonly">
                                <label>Observador</label>
                                <input type="text" name="observador" v-model="d.observador">
                            </div>
                            <div class="field">
                                <label>Equipo</label>
                                <semantic-select type="text" v-model="d.equipo_id"
                                                 :opciones="l.equipos"></semantic-select>
                            </div>
                            <div class="field">
                                <label>Cliente</label>
                                <semantic-select type="text" v-model="d.cliente_id"
                                                 :opciones="l.clientes"></semantic-select>
                            </div>
                            <div class="field">
                                <label>Tipo Equipo</label>
                                <semantic-select type="text" v-model="d.tipo_equipo_id"
                                                 :opciones="l.tipos"></semantic-select>
                            </div>

                        </div>
                        <div class="fields">
                            <div class="field">
                                <label>Tipo</label>
                                <semantic-select type="text" v-model="d.tipo"
                                                 :opciones="tipoActos"></semantic-select>
                            </div>
                        </div>
                        <div class="fields">
                            <div class="field">
                                <label>Personal</label>
                                <semantic-select type="text" v-model="d.personal_id"
                                                 :opciones="l.personal"></semantic-select>
                            </div>
                            <div class="field">
                                <label>Equipamiento</label>
                                <semantic-select type="text" v-model="d.equipamiento_id"
                                                 :opciones="l.equipamiento"></semantic-select>
                            </div>
                            <div class="field">
                                <label>Acción Inmediata</label>
                                <input type="text" v-model="d.acc_inmediata">
                            </div>
                            <div class="field">
                                <label>Tipo acto inseguro</label>
                                <semantic-select v-model="d.tipo_acto_id" :opciones="l.actos"></semantic-select>
                            </div>
                            <div class="field">
                                <label>Tipo condición insegura</label>
                                <semantic-select v-model="d.tipo_cond_id" :opciones="l.condiciones"></semantic-select>
                            </div>
                        </div>
                        <div class="field">
                            <label>Observación</label>
                            <textarea
                                    @if(!Gate::allows('ver-mas'))
                                    readonly
                                    @endif
                                    type="text"
                                    v-model="d.obs"></textarea>
                        </div>
                        <div class="field">
                            <label>Evidencia de la condición o acto inseguro</label>
                            <textarea type="text" v-model="d.evidencia_cond"></textarea>
                        </div>
                        <div class="field">
                            <label>Medida correctiva sugerida</label>
                            <textarea type="text" v-model="d.medida"></textarea>
                        </div>

                        <div class="fields">
                            <div class="field">
                                <label>Prioridad</label>
                                <semantic-select v-model="d.prioridad_id" :opciones="l.prioridades"></semantic-select>
                            </div>
                            @if(Gate::allows('ver-mas'))
                                <div class="field">
                                    <label>Responsable</label>
                                    <semantic-select v-model="d.responsable_id"
                                                     :opciones="l.responsables"></semantic-select>
                                </div>
                                <div class="field">
                                    <label>Enviar email a...</label>
                                    <semantic-select v-model="d.envio_id"
                                                     :opciones="l.envios"></semantic-select>
                                </div>
                                <div class="field">
                                    <label>Fecha Verificación</label>
                                    <selector-fecha v-model="d.fecha_verif"></selector-fecha>
                                </div>
                                <div class="field">
                                    <label>Verificado por...</label>
                                    <semantic-select v-model="d.verificador_id"
                                                     :opciones="l.usuarios"></semantic-select>
                                </div>
                            @endif
                        </div>
                        <div class="field">
                            <label>Evidencia de la solucion</label>
                            <textarea type="text" v-model="d.evidencia"></textarea>
                        </div>
                        <div class="field">
                            <label>Comentarios</label>
                            <textarea type="text" v-model="d.coment"></textarea>
                        </div>
                        <div class="fields">
                            @if(Gate::allows('ver-mas'))
                                <div class="field">
                                    <label>Porcentaje</label>
                                    <semantic-select v-model="d.porcent" :opciones="l.porcentajes"></semantic-select>
                                </div>
                            @endif
                        </div>
                        <a v-if="oper == 'v' && d.archivo" :href="url +'/obs/descarga/'+ d.id">Descargar Archivo</a>
                        <div class="field" v-if="oper != 'v'">
                            <label>Adjuntar Archivo</label>
                            <input type="file" v-on:change="subirArchivo($event)">
                        </div>
                    </div>
                    <div class="ui divider"></div>
                    <button class="ui button labeled icon red" v-on:click="cancelar()"><i class="icon remove"></i>Cancelar
                    </button>
                    <button v-if="oper == 'a'" class="ui button labeled icon green" :disabled="agregando"
                            v-on:click="agregar()">
                        <i class="icon plus"></i>Agregar
                    </button>
                    <button v-if="oper == 'e'" class="ui button labeled icon green" :disabled="agregando"
                            v-on:click="guardar()">
                        <i class="icon save"></i>Guardar
                    </button>
                </div>
            </div>
        </div>
        @if(Gate::allows('crud-eliminar'))
            <div class="ui modal borrar">
                <div class="header">Eliminar Registro</div>
                <div class="content">
                    <p>¿Confirma la eliminación del registro?</p>
                </div>
                <div class="actions">
                    <button class="ui button cancel labeled icon red"><i class="icon remove"></i>Cancelar</button>
                    <button class="ui button labeled icon green" :class="{loading: eliminando}" v-on:click="eliminar()">
                        <i class="icon check"></i>Eliminar
                    </button>
                </div>
            </div>
        @endif
    </div>
@stop
@section('script')
    <script>
        var archivo = '';
        $(document).ready(function () {
            let app = new Vue({
                el: '#app',
                data: {
                    url: urlBase,
                    oper: 'a',
                    operacion: 'Agregar',
                    agregando: false,
                    eliminando: false,
                    eliminarIndx: -1,
                    showForm: false,
                    l: {
                        equipos: [],
                        tipos: [],
                        actos: [],
                        condiciones: [],
                        personal: [],
                        equipamiento: [],
                        prioridades: [],
                        responsables: [],
                        clientes: [],
                        usuarios: [],
                        porcentajes: [],
                        envios: []
                    },
                    tipoActos: [
                        {value: 'a', text: 'Acto Inseguro'},
                        {value: 'c', text: 'Condición Insegura'},
                    ],
                    d: {
                        id: '',
                        fecha_obs: '',
                        observador: '',
                        equipo_id: '',
                        cliente_id: '',
                        tipo_equipo_id: '',
                        tipo: '',
                        inseguro_acto: 0,
                        inseguro_cond: 0,
                        personal_id: '',
                        equipamiento_id: '',
                        acc_inmediata: '',
                        tipo_acto_id: '',
                        tipo_cond_id: '',
                        obs: '',
                        evidencia: '',
                        medida: '',
                        coment: '',
                        archivo: '',
                        prioridad_id: '',
                        fecha_verif: '',
                        verificador_id: '',
                        evidencia_cond: '',
                        porcent: ''
                    }

                },
                mounted: function () {
                    this.traeListados();
                    $('.ui.modal').modal({
                        autofocus: false
                    });
                },
                methods: {
                    cancelar: function () {
                        this.showForm = false;
                    },
                    subirArchivo: function (ele) {
                        var self = this;
                        let files = ele.target.files || ele.dataTransfer.files;
                        if (!files.length) {
                            return;
                        }
                        archivo = files[0];
                    },
                    eliminar: function () {
                        var self = this;
                        self.eliminando = true;
                        req.post('obs/eliminar', {id: self.eliminarIndx})
                            .then(function (result) {
                                let r = result.data;
                                noty(r);
                                self.eliminando = false;
                                app.$refs.tabla.actualizar();
                                $('.ui.modal.borrar').modal('hide');
                            }, function (e) {
                                noty({tipo: 2, txt: 'Error de guardado'});
                                self.eliminando = false;
                            })
                    },
                    abreEliminar: function (itm) {
                        this.eliminarIndx = itm.id;
                        $('.ui.modal.borrar').modal('show');
                    },
                    abreVer: function (itm) {
                        var self = this;
                        this.oper = 'v';
                        this.operacion = 'Ver';
                        req.post('obs/editar', {id: itm.id}).then(function (result) {
                            let r = result.data;
                            for (let k in self.d) {
                                self.d[k] = r[k];
                            }
                            archivo = self.d.archivo;
                            self.agregando = false;
                            self.showForm = true;
                        })
                    },
                    abreEditar: function (itm) {
                        var self = this;
                        this.oper = 'e';
                        this.operacion = 'Editar';
                        req.post('obs/editar', {id: itm.id}).then(function (result) {
                            let r = result.data;
                            for (let k in self.d) {
                                self.d[k] = r[k];
                            }
                            archivo = self.d.archivo;
                            self.agregando = false;
                            self.showForm = true;
                        })
                    },
                    guardar: function () {
                        let self = this;
                        self.agregando = true;
                        self.d.inseguro_acto = (self.d.tipo === 'a') ? 1 : 0;
                        self.d.inseguro_cond = (self.d.tipo === 'c') ? 1 : 0;
                        let formData = new FormData();
                        for (let k in self.d) {
                            if (k === 'tipo') {
                                continue;
                            }
                            self.d[k] = (!self.d[k]) ? '' : self.d[k];
                            if (k == 'archivo') {
                                formData.append(k, archivo);
                            } else {
                                formData.append(k, self.d[k]);
                            }
                        }

                        req.post('obs/guardar', formData).then(function (result) {
                            let r = result.data;
                            noty(r);
                            for (let k in self.d) {
                                self.d[k] = '';
                            }
                            archivo = '';
                            self.agregando = false;
                            self.showForm = false;
                            app.$refs.tabla.actualizar();

                        }, function (e) {
                            noty({'tipo': 2, 'txt': 'Error de Guardado'});
                            self.agregando = false;
                        })
                    },
                    abreAgregar: function () {
                        var self = this;
                        this.oper = 'a';
                        this.operacion = 'Agregar';
                        for (let k in self.d) {
                            self.d[k] = '';
                        }
                        self.showForm = true;
                    },
                    agregar: function () {
                        let self = this;
                        if (!self.d.tipo) {
                            noty({'tipo': 2, 'txt': 'Debe ingresar un tipo de observación'});
                            return false;
                        }

                        self.agregando = true;
                        let formData = new FormData();
                        self.d.inseguro_acto = (self.d.tipo === 'a') ? 1 : 0;
                        self.d.inseguro_cond = (self.d.tipo === 'c') ? 1 : 0;

                        for (let k in self.d) {
                            if (k === 'tipo') {
                                continue;
                            }
                            self.d[k] = (!self.d[k]) ? '' : self.d[k];
                            if (k == 'archivo') {
                                formData.append(k, archivo);
                            } else {
                                formData.append(k, self.d[k]);
                            }
                        }
                        req.post('obs/agregar', formData).then(function (result) {
                            let r = result.data;
                            noty(r);
                            for (let k in self.d) {
                                self.d[k] = '';
                            }
                            archivo = '';
                            self.agregando = false;
                            self.showForm = false;
                            app.$refs.tabla.actualizar();
                        }, function (e) {
                            noty({'tipo': 2, 'txt': 'Error de Guardado'});
                            self.agregando = false;
                        })
                    },
                    traeListados: function () {
                        let self = this;
                        req.post('obs/datos').then(function (result) {
                            let r = result.data;
                            for (let k in self.l) {
                                self.l[k] = r[k];
                            }
                        })
                    },

                }
            })
        })
    </script>
@stop
