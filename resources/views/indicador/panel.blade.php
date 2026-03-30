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
    <div class="viewCont" v-cloak>
        <h1 class="ui dividing header">Indicadores</h1>
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
                            <div class="field">
                                <label></label>
                                <button class="ui button blue" v-on:click="mostrar()">Mostrar</button>
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
                                    Total Accidentes
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
            </div>
        </div>
    </div>
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
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        showInLegend: true,
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %',
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
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        showInLegend: true,
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %',
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
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        showInLegend: true,
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %',
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
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        showInLegend: true,
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %',
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
            var viewCont = new Vue({
                el: '.viewCont',
                data: {
                    cargando: false,
                    d: {
                        desde: moment().subtract(1, 'months').format('DD/MM/YYYY'),
                        hasta: moment().format('DD/MM/YYYY'),
                        empresa: ''
                    },
                    r: {
                        totAccidentes: 0,
                        diasPerdidos: 0,
                        accEmp: [],
                        empNom: '',
                        accRealizar: [],
                        accRealizadas: [],
                        accRealizarTot: 0,
                        accRealizadasTot: 0,
                        indFrecTot: 0,
                        totAccidentesEmp: 0,
                        indFrecTotEmp: 0,
                        indGrav: 0,
                        indGravEmp: 0
                    },
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
                                    chart4.series[0].setData([{name: 'A Realizar', y: r.accRealizarTot}, {name: 'Realizadas', y: r.accRealizadasTot}]);
                                    chart4.setTitle({text: 'Accidentes por Factor de Causa Básica. Empresa ' + r.empNom});
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