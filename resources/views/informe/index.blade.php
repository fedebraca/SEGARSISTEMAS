@extends('layout.default')
@section('title')
    @parent
@stop
@section('js')

@stop
@section('css')
@stop
@section('cont')
    <h1>GENERACION DE INFORME</h1>
    <div class="viewCont">
        <form class="ui form" action="{{  URL::to('informe/descarga') }}" method="post">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="fields">
                <div class="field">
                    <label>Desde</label>
                    <input type="date" name="desde">
                </div>
                <div class="field">
                    <label>Hasta</label>
                    <input type="date" name="hasta">
                </div>
                <div class="field">
                    <label>Empresa</label>
                    <div class="ui ui search selection dropdown" v-select="d.empresa">
                        <input type="hidden" name="empresa">
                        <i class="dropdown icon"></i>

                        <div class="default text">Seleccione Empresa</div>
                        <div class="menu">
                            <div class="item" data-value="">TODOS</div>
                            <div v-for="item in l.empresas" class="item" data-value="<% item.value %>"><% item.text %></div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="ui button green icon" type="submit"><i class="icon download"></i> Descargar</button>
        </form>
    </div>

@stop
@section('script')
    <script>
        var token = '{{ csrf_token()  }}';
        var urlConfig = '{{ URL::to('indicador/config') }}';
        var viewCont = new Vue({
            el: '.viewCont',
            data: {
                cargando: false,
                l: {
                    empresas: []
                }

            },
            created: function () {
                var self = this;
                self.traeConfig();
            },
            methods: {
                traeConfig: function () {
                    var self = this;
                    self.cargando = true;
                    $.post(urlConfig, {'_token': token})
                            .then(function (r) {
                                self.l = r;
                                self.cargando = false;
                            }, function () {
                                self.cargando = false;
                            })
                }

            }
        });
    </script>
@stop