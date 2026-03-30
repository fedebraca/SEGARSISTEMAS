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
        <h1 class="ui dividing header">Agregar Riesgo</h1>

        <div class="ui form">
            <div class="fields">
                <div class="field">
                    <label>Descripción</label>
                    <input type="text" name="desc" v-model="d.desc">
                </div>
                <div class="field">
                    <label>Cliente</label>

                    <div class="ui search selection dropdown" v-select="d.cliente" :class="{loading:cargaConfig}">
                        <input type="hidden" name="cliente">
                        <i class="dropdown icon"></i>

                        <div class="default text">Seleccione Cliente</div>
                        <div class="menu">
                            <div v-for="cli in l.clientes" class="item" data-value="<% cli.value %>"><% cli.text %></div>
                        </div>
                    </div>
                </div>
                <div class="field">
                    <label>Fecha Riesgo</label>
                    <input name="friesgo" v-fecha="d.friesgo">
                </div>
                <div class="field">
                    <label>Lugar</label>
                    <input name="lugar" v-model="d.lugar">
                </div>
            </div>
            <div class="field">
                <label>Comentario</label>
                <textarea name="coment" v-model="d.coment"></textarea>
            </div>

        </div>
        <div class="ui divider"></div>
        <form class="dropzone ui form" id="dropzone" role="form" method="POST">
            {!! csrf_field() !!}
        </form>
        <br>
        <button class="ui button icon green" v-on:click="guardar()"><i class="icon save"></i> Guardar</button>
    </div>

@stop
@section('script')
    <script>
        $(document).ready(function () {
            Dropzone.autoDiscover = false;
            var urlAgregar = '{{ URL::to('riesgo/agregar') }}';
            var urlConfig = '{{ URL::to('riesgo/config') }}';
            var token = '{{ csrf_token()  }}';
            //////////////////////////////////////////////
            var viewCont = new Vue({
                el: '.viewCont',
                data: {
                    drop: '',
                    cargaConfig: false,
                    cargaGuardar: false,
                    l: {
                        clientes: []
                    },
                    d: {
                        desc: '',
                        cliente: '',
                        friesgo: '',
                        coment: '',
                        lugar: ''
                    }
                },
                created: function () {

                },
                ready: function () {
                    this.drop = new Dropzone("form#dropzone", {
                        url: urlAgregar,
                        uploadMultiple: true,
                        autoProcessQueue: false
                    });
                },
                methods: {
                    traeConfig: function () {
                        var self = this;
                        self.cargaConfig = true;
                        $.post(urlConfig)
                                .then(function (r) {
                                    $.each(r.listado, function (k, v) {
                                        self.l[k] = v;
                                    });
                                    self.cargaConfig = false;
                                }, function () {
                                    mensaje({tipo: 2, txt: 'Error de Conexión'})
                                    self.cargaConfig = false;
                                })
                    },
                    guardar: function () {
                        var self = this;
                        self.cargaGuardar = true;
                        var dd = self.drop;
                        self.drop.processQueue();
                    }
                }
            });
        })
    </script>
@stop