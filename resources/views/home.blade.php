@extends('layout.default')
@section('title')
    @parent
@stop
@section('js')
    <script src="{{ asset('js/highcharts/highcharts.js')  }}"></script>
@stop
@section('css')
@stop
@section('cont')
    @if(!Auth::guest())
        @can('v-accidente')
            <div class="viewCont" v-cloak>
                <div class="ui segment" :class="{loading: cargando}">
                    <div class="ui form">
                        <div class="row">
                            <div class="column">
                                <div class="fields">
                                    <div class="field">
                                        <label>Desde</label>
                                        <input type="date" name="desde" v-model="d.desde">
                                    </div>
                                    <div class="field">
                                        <label>Hasta</label>
                                        <input type="date" name="hasta" v-model="d.hasta">
                                    </div>
                                    <div class="field">
                                        <label>Rango Hacia Atrás</label>
                                        <select name="rango" class="ui search selection dropdown" v-select="d.rango">
                                            <option value="">Seleccione Rango</option>
                                            <option v-for="(k,v) in rango" value="<% k %>"><% v %></option>
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="column">
                                <div class="fields">
                                    <div class="field">
                                        <label>Empresa</label>
                                        <div class="ui search selection dropdown" v-select="d.empresa">
                                            <input type="hidden" name="empresa">
                                            <i class="dropdown icon"></i>

                                            <div class="default text">Seleccione Empresa</div>
                                            <div class="menu">
                                                <div v-for="item in l.empresas" class="item"
                                                     data-value="<% item.value %>"><% item.text %>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <label>Cliente</label>
                                        <div class="ui search selection dropdown" v-select="d.cliente">
                                            <input type="hidden" name="cliente">
                                            <i class="dropdown icon"></i>
                                            <div class="default text">Seleccione Cliente</div>
                                            <div class="menu">
                                                <div v-for="item in l.clientes" class="item"
                                                     data-value="<% item.value %>"><% item.text %>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <label>Equipo</label>
                                        <div class="ui search selection dropdown multiple" v-select="d.equipo">
                                            <input type="hidden" name="equipo">
                                            <i class="dropdown icon"></i>

                                            <div class="default text">Seleccione Equipo</div>
                                            <div class="menu">
                                                <div v-for="item in l.equipos" class="item"
                                                     data-value="<% item.value %>"><% item.text %>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <label>Mostrar</label>
                                        <button class="ui fluid icon button blue" v-on:click="mostrar()"><i
                                                    class="icon unhide"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="ui divider"></div>
                        <div class="row">
                            <div class="column">
                                <div class="ui statistics">
                                    <div class="ui statistic">
                                        <div class="value"><% r.totAccidentes %></div>
                                        <div class="label">
                                            TOTALES GENERALES ENTRE FECHAS SIN FILTROS
                                        </div>
                                    </div>
                                    <div class="ui statistic">
                                        <div class="value"><% r.diasPerdidos %></div>
                                        <div class="label">
                                            Total Dias Perdidos
                                        </div>
                                    </div>
                                    <div class="ui statistic">
                                        <div class="value"><% r.indFrecTot %></div>
                                        <div class="label">
                                            Indice Frec de Accidentes
                                        </div>
                                    </div>
                                    <div class="ui statistic">
                                        <div class="value"><% r.indGrav %></div>
                                        <div class="label">
                                            Indice Gravedad Accidentes
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="ui divider"></div>
                        <div class="row">
                            <div class="column">
                                <h3 class="ui dividing header">Datos por Empresa Activa: <% r.empNom %></h3>
                                <div class="ui statistics">
                                    <div class="ui statistic">
                                        <div class="value"><% r.totAccidentesEmp %></div>
                                        <div class="label">
                                            Total Accidentes
                                        </div>
                                    </div>
                                    <div class="ui statistic">
                                        <div class="value"><% r.indFrecTotEmp %></div>
                                        <div class="label">
                                            Indice Frec de Accidentes
                                        </div>
                                    </div>
                                    <div class="ui statistic">
                                        <div class="value"><% r.indGravEmp %></div>
                                        <div class="label">
                                            Indice Gravedad Accidentes
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ui divider"></div>
                        <div class="row">
                            <div class="column">
                                <h3 class="ui dividing header">Datos por Cliente Activo: <% r.cliNom %></h3>
                                <div class="ui statistics">
                                    <div class="ui statistic">
                                        <div class="value"><% r.totAccidentesCli %></div>
                                        <div class="label">
                                            Total Accidentes
                                        </div>
                                    </div>
                                    <div class="ui statistic">
                                        <div class="value"><% r.indFrecTotCli %></div>
                                        <div class="label">
                                            Indice Frec de Accidentes
                                        </div>
                                    </div>
                                    <div class="ui statistic">
                                        <div class="value"><% r.indGravCli %></div>
                                        <div class="label">
                                            Indice Gravedad Accidentes
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ui divider"></div>
                        <div class="row">
                            <div class="column">
                                <h3 class="ui dividing header">Datos por Equipo Activo: <% r.equipoNom %></h3>
                                <div class="ui statistics">
                                    <div class="ui statistic">
                                        <div class="value"><% r.totAccidentesEqu %></div>
                                        <div class="label">
                                            Total Accidentes
                                        </div>
                                    </div>
                                    <div class="ui statistic">
                                        <div class="value"><% r.indFrecTotEqu %></div>
                                        <div class="label">
                                            Indice Frec de Accidentes
                                        </div>
                                    </div>
                                    <div class="ui statistic">
                                        <div class="value"><% r.indGravEqu %></div>
                                        <div class="label">
                                            Indice Gravedad Accidentes
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ui divider"></div>
                        <div class="row">
                            <div class="column">
                                <h3 class="ui dividing header">Total de Accidente por Empresa</h3>
                                <div class="ui statistics">
                                    <div class="ui statistic" v-for="acc in r.accEmp">
                                        <div class="value"><% acc.total %></div>
                                        <div class="label">
                                            <% acc.rzon_soc %>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="ui divider"></div>
                        <div class="row">
                            <div class="column">
                                <h3 class="ui dividing header">Total de Acciones Correctivas a Realizar</h3>
                                <div class="ui statistics">
                                    <div class="ui statistic" v-for="acc in r.accRealizar">
                                        <div class="value"><% acc.tot %></div>
                                        <div class="label">
                                            Acción Correctiva <% acc.nom %>
                                        </div>
                                    </div>
                                    <div class="ui statistic">
                                        <div class="value"><% r.accRealizarTot %></div>
                                        <div class="label">
                                            Total Acción Correctiva a Realizar
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="ui divider"></div>
                        <div class="row">
                            <div class="column">
                                <h3 class="ui dividing header">Total de Acciones Correctivas Realizadas</h3>
                                <div class="ui statistics">
                                    <div class="ui statistic" v-for="acc in r.accRealizadas">
                                        <div class="value"><% acc.tot %></div>
                                        <div class="label">
                                            Acción Correctiva <% acc.nom %>
                                        </div>
                                    </div>
                                    <div class="ui statistic">
                                        <div class="value"><% r.accRealizadasTot %></div>
                                        <div class="label">
                                            Total Acción Correctiva Realizadas
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="ui divider"></div>
                        <div class="row">
                            <div class="ui grid celled stackable two columns">
                                <div class="column">
                                    <div id="chart1"></div>
                                </div>
                                <div class="column">
                                    <div id="chart2"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="ui grid celled stackable two columns">
                                <div class="column">
                                    <div id="chart3"></div>
                                </div>
                                <div class="column">
                                    <div id="chart4"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="ui grid celled stackable two columns">
                                <div class="column">
                                    <div id="chart5"></div>
                                </div>
                                <div class="column">
                                    <div id="chart6"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="ui grid celled stackable two columns">
                                <div class="column">
                                    <div id="chart7"></div>
                                </div>
                                <div class="column">
                                    <div id="chart8"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="ui grid celled stackable two columns">
                                <div class="column">
                                    <div id="chart9"></div>
                                </div>
                                <div class="column">
                                    <div id="chart10"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="ui grid celled stackable two columns">
                                <div class="column">
                                    <div id="chart11"></div>
                                </div>
                                <div class="column">
                                    <div id="chart12"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="ui grid celled stackable two columns">
                                <div class="column">
                                    <div id="chart13"></div>
                                </div>
                                <div class="column">
                                    <div id="chart14"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="ui grid celled stackable two columns">
                                <div class="column">
                                    <div id="chart15"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
@stop
@section('script')
    <script>
        $(document).ready(function () {
            var urlPanel = '{{ URL::to('indicador/panel') }}';
            var urlConfig = '{{ URL::to('indicador/config') }}';
            var token = '{{ csrf_token()  }}';
            //////////////////////////////////////////////
            var chart1 = $('#chart1').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: 'Accidentes por tipo'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        showInLegend: true,
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.y} ({point.percentage:.1f} %)',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        }
                    }
                },
                series: [{
                    name: 'Brands',
                    colorByPoint: true,
                    data: []
                }]
            });
            //////////////////////////////////////////////
            var chart2 = $('#chart2').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: 'Accidentes por tipo'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        showInLegend: true,
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.y} ({point.percentage:.1f} %)',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        }
                    }
                },
                series: [{
                    name: 'Brands',
                    colorByPoint: true,
                    data: []
                }]
            });
            //////////////////////////////////////////////
            var chart3 = $('#chart3').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: 'Accidentes por tipo'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        showInLegend: true,
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.y} ({point.percentage:.1f}) %',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        }
                    }
                },
                series: [{
                    name: 'Brands',
                    colorByPoint: true,
                    data: []
                }]
            });
            //////////////////////////////////////////////
            var chart4 = $('#chart4').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: 'Acc Corr a Realizar vs Realizadas'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        showInLegend: true,
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.y} ({point.percentage:.1f} %)',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        }
                    }
                },
                series: [{
                    name: 'Brands',
                    colorByPoint: true,
                    data: []
                }]
            });
            //////////////////////////////////////////////
            var chart5 = $('#chart5').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: 'Accidentes por Cliente'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        showInLegend: true,
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.y} ({point.percentage:.1f} %)',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        }
                    }
                },
                series: [{
                    name: 'Brands',
                    colorByPoint: true,
                    data: []
                }]
            });
            //////////////////////////////////////////////
            var chart6 = $('#chart6').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: 'Accidentes por Equipo'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        showInLegend: true,
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.y} ({point.percentage:.1f} %)',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        }
                    }
                },
                series: [{
                    name: 'Brands',
                    colorByPoint: true,
                    data: []
                }]
            });
            //////////////////////////////////////////////
            var chart7 = $('#chart7').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'column'
                },
                title: {
                    text: 'Indice de Frecuecia de Accidente por Empresa'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        showInLegend: true,
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.y} ({point.percentage:.1f} %)',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        }
                    }
                },
                series: [{
                    name: 'Enmpresas',
                    colorByPoint: true,
                    data: []
                }]
            });
            //////////////////////////////////////////////
            var chart8 = $('#chart8').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'column'
                },
                title: {
                    text: 'Indice de Gravedad de Accidente por Empresa'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'
                },
                xAxis: {
                    type: 'category',
                },
                series: [{
                    name: 'Empresas',
                    colorByPoint: true,
                    data: []
                }]
            });
            //////////////////////////////////////////////
            var chart9 = $('#chart9').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'column'
                },
                title: {
                    text: 'Indice de Frecuecia de Accidente por cliente'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'
                },
                xAxis: {
                    type: 'category',
                },
                series: [{
                    name: 'Clientes',
                    colorByPoint: true,
                    data: []
                }]
            });
            //////////////////////////////////////////////
            var chart10 = $('#chart10').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'column'
                },
                title: {
                    text: 'Indice de Gravedad de Accidente por cliente'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'
                },
                xAxis: {
                    type: 'category',
                },
                series: [{
                    name: 'Clientes',
                    colorByPoint: true,
                    data: []
                }]
            });
            //////////////////////////////////////////////
            var chart11 = $('#chart11').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'column'
                },
                title: {
                    text: 'Indice de Frecuecia de Accidente por equipo'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'
                },
                xAxis: {
                    type: 'category',
                },
                series: [{
                    name: 'Equipos',
                    colorByPoint: true,
                    data: []
                }]
            });
            //////////////////////////////////////////////
            var chart12 = $('#chart12').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'column'
                },
                title: {
                    text: 'Indice de Gravedad de Accidente por equipo'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'
                },
                xAxis: {
                    type: 'category',
                },
                series: [{
                    name: 'Equipos',
                    colorByPoint: true,
                    data: []
                }]
            });
            //////////////////////////////////////////////
            var chart13 = $('#chart13').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'column'
                },
                title: {
                    text: 'Hs mensuales x empresa activa'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'
                },
                xAxis: {
                    type: 'category',
                },
                series: [{
                    name: 'Empresas',
                    colorByPoint: true,
                    data: []
                }]
            });
            //////////////////////////////////////////////
            var chart14 = $('#chart14').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'column'
                },
                title: {
                    text: 'Hs mensuales x cliente activo'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'
                },
                xAxis: {
                    type: 'category',
                },
                series: [{
                    name: 'Clientes',
                    colorByPoint: true,
                    data: []
                }]
            });
            //////////////////////////////////////////////
            var chart15 = $('#chart15').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'column'
                },
                title: {
                    text: 'Hs mensuales x equipo activo'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'
                },
                xAxis: {
                    type: 'category',
                },
                series: [{
                    name: 'Equipos',
                    colorByPoint: true,
                    data: []
                }]
            });
            //////////////////////////////////////////////
            var viewCont = new Vue({
                el: '.viewCont',
                data: {
                    cargando: false,
                    rango: {
                        1: '1 MES',
                        3: '3 MESES',
                        6: '6 MESES',
                        12: '12 MESES'
                    },
                    d: {
                        desde: moment().subtract(1, 'months').format('YYYY-MM-DD'),
                        hasta: moment().format('YYYY-MM-DD'),
                        rango: '',
                        empresa: '',
                        cliente: '',
                        equipo: ''
                    },
                    r: {
                        totAccidentes: 0,
                        diasPerdidos: 0,
                        accEmp: [],
                        accEquipo: [],
                        empNom: '',
                        cliNom: '',
                        cliEqui: '',
                        accRealizar: [],
                        accRealizadas: [],
                        accRealizarTot: 0,
                        accRealizadasTot: 0,
                        indFrecTot: 0,
                        indGrav: 0,
                        totAccidentesEmp: 0,
                        indFrecTotEmp: 0,
                        indGravEmp: 0,
                        totAccidentesCli: 0,
                        indFrecTotCli: 0,
                        indGravCli: 0,
                        totAccidentesEqu: 0,
                        indFrecTotEqu: 0,
                        indGravEqu: 0
                    },
                    l: {
                        empresas: [],
                        clientes: [],
                        equipos: []
                    }

                },
                watch: {
                    'd.rango': function () {
                        var self = this;
                        self.d.desde = moment().subtract(self.d.rango, 'months').format('YYYY-MM-DD')
                    },
                    'd.empresa': function () {
                        this.traeClientes();
                        this.traeEquipos();
                    },
                    'd.cliente': function () {
                        this.traeEquipos();
                    }
                },
                created: function () {
                    var self = this;
                    self.traeEmpresas();
                },
                methods: {
                    traeClientes: function () {
                        var self = this;
                        self.cargando = true;
                        $.post('{{ URL::to('indicador/config-clientes') }}', {
                            '_token': token,
                            'empresa': self.d.empresa
                        })
                            .then(function (r) {
                                self.l.clientes = r;
                                self.mostrar();
                                self.cargando = false;
                            }, function () {
                                self.cargando = false;
                            })
                    },
                    traeEquipos: function () {
                        var self = this;
                        // if (!self.d.cliente) {
                        //     return false;
                        // }
                        self.cargando = true;
                        $.post('{{ URL::to('indicador/config-equipos') }}', {
                            '_token': token,
                            'empresa': self.d.empresa,
                            'cliente': self.d.cliente
                        })
                            .then(function (r) {
                                self.l.equipos = r;
                                self.mostrar();
                                self.cargando = false;
                            }, function () {
                                self.cargando = false;
                            })
                    },
                    traeEmpresas: function () {
                        var self = this;
                        self.cargando = true;
                        $.post('{{ URL::to('indicador/config-empresas') }}', {'_token': token})
                            .then(function (r) {
                                self.l.empresas = r;
                                self.mostrar();
                                self.cargando = false;
                            }, function () {
                                self.cargando = false;
                            })
                    },
                    mostrar: function () {
                        var self = this;
                        self.cargando = true;
                        $.post(urlPanel, {'_token': token, 'd': self.d})
                            .then(function (r) {
                                var chart1 = $('#chart1').highcharts();
                                chart1.series[0].setData(r.accTipo);
                                chart1.setTitle({text: 'Accidentes por tipo. Empresa ' + r.empNom});
                                /////////////
                                var chart2 = $('#chart2').highcharts();
                                chart2.series[0].setData(r.accTipoTipo);
                                chart2.setTitle({text: 'Accidentes por tipo. Empresa ' + r.empNom});
                                /////////////
                                var chart3 = $('#chart3').highcharts();
                                chart3.series[0].setData(r.accCauBasicaFactor);
                                chart3.setTitle({text: 'Accidentes por Factor de Causa Básica. Empresa ' + r.empNom});
                                /////////////
                                var chart4 = $('#chart4').highcharts();
                                chart4.series[0].setData([{
                                    name: 'A Realizar',
                                    y: r.accRealizarTot
                                }, {name: 'Realizadas', y: r.accRealizadasTot}]);
                                chart4.setTitle({text: 'Accidentes por Factor de Causa Básica. Empresa ' + r.empNom});
                                /////////////
                                var chart5 = $('#chart5').highcharts();
                                chart5.series[0].setData(r.accCliente);
                                chart5.setTitle({text: 'Accidentes por Cliente. Cliente ' + r.cliNom});
                                /////////////
                                var chart6 = $('#chart6').highcharts();
                                chart6.series[0].setData(r.accEquipo);
                                chart6.setTitle({text: 'Accidentes por Equipo. Equipo ' + r.equiNom});
                                /////////////
                                var chart7 = $('#chart7').highcharts();
                                chart7.series[0].setData(r.grafIindFrecEmp);
                                chart7.setTitle({text: 'Indice de frecuencia de accidente por empresa'});
                                /////////////
                                var chart8 = $('#chart8').highcharts();
                                chart8.series[0].setData(r.grafIindGravEmp);
                                chart8.setTitle({text: 'Indice de gravedad de accidente por empresa'});
                                /////////////
                                var chart9 = $('#chart9').highcharts();
                                chart9.series[0].setData(r.grafIindFrecCli);
                                chart9.setTitle({text: 'Indice de frecuencia de accidente por cliente'});
                                /////////////
                                var chart10 = $('#chart10').highcharts();
                                chart10.series[0].setData(r.grafIindGravCli);
                                chart10.setTitle({text: 'Indice de gravedad de accidente por cliente'});
                                /////////////
                                var chart11 = $('#chart11').highcharts();
                                chart11.series[0].setData(r.grafIindFrecEqu);
                                chart11.setTitle({text: 'Indice de frecuencia de accidente por equipo'});
                                /////////////
                                var chart12 = $('#chart12').highcharts();
                                chart12.series[0].setData(r.grafIindGravEqu);
                                chart12.setTitle({text: 'Indice de gravedad de accidente por equipo'});
                                /////////////
                                var chart13 = $('#chart13').highcharts();
                                chart13.series[0].setData(r.grafHsEmp);
                                /////////////
                                var chart14 = $('#chart14').highcharts();
                                chart14.series[0].setData(r.grafHsCli);
                                /////////////
                                var chart15 = $('#chart15').highcharts();
                                chart15.series[0].setData(r.grafHsEqu);
                                /////////////
                                self.r = r;
                                self.cargando = false;
                            }, function () {
                                self.cargando = false;
                            })
                    }

                }
            });

        });
    </script>
@stop