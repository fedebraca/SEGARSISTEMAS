<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>SEGAR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--///////////////////////////////	JS	///////////////////////////////-->
    <script src="{{ asset('js/jquery/dist/jquery.min.js')  }}"></script>
    <script src="{{ asset('semantic/dist/semantic.min.js')  }}"></script>
    <script src="{{ asset('js/vue/dist/vue.min.js')  }}"></script>
    <script src="{{ asset('js/noty/js/noty/packaged/jquery.noty.packaged.min.js')  }}"></script>
    <script src="{{ asset('js/jquery.inputmask/dist/jquery.inputmask.bundle.js')  }}"></script>
    <script src="{{ asset('js/teamdf/jquery-number/jquery.number.min.js')  }}"></script>
    <script src="{{ asset('js/moment/min/moment-with-locales.min.js')  }}"></script>
@yield('js')
<!--///////////////////////////////	CSS	///////////////////////////////-->
    <link rel="stylesheet" href="{{ asset('semantic/dist/semantic.min.css')  }}">
    <link rel="stylesheet" href="{{ asset('css/animate.css')  }}">
    <link rel="stylesheet" href="{{ asset('css/gral.css')  }}">
    @yield('css')
</head>
<body>
    @include('layout.menu')
    <div class="ui container fluid">
        @yield('cont')
    </div>

    <script>
        $.fn.form.settings.prompt = {
            empty: '{name} debe contener un valor',
            match: '{name} debe ser igual al campo {ruleValue}'
        };
        Vue.config.delimiters = ['<%', '%>'];
        ////////////////////////////////////////////////////////////////////////////    COMPONENTES

        ////////////////////////////////////////////////////////////////////////////    DIRECTIVAS
        Vue.directive('color', {
            twoWay: true,
            bind: function () {
                var self = this;
                $(self.el).spectrum({
                    allowEmpty: true,
                    togglePaletteOnly: true,
                    showPaletteOnly: true,
                    showPalette: true,
                    change: function (color) {
                        self.set(color.toHexString());
                    }
                });
            },
            unbind: function () {
                // do clean up work
                // e.g. remove event listeners added in bind()
            }
        });
        Vue.directive('checkValor', {
            twoWay: true,
            bind: function () {
                var self = this;
                $(self.el).checkbox({
                    onChecked: function () {
                        self.set(this.value);
                    },
                    onUnchecked: function () {
                        self.set(this.value);
                    }
                });

            },
            unbind: function () {
                // do clean up work
                // e.g. remove event listeners added in bind()
            }
        });

        Vue.directive('check', {
            twoWay: true,
            bind: function () {
                var self = this;
                $(self.el).checkbox({
                    onChecked: function () {
                        self.set(true);
                    },
                    onUnchecked: function () {
                        self.set(false);
                    }
                });

            },
            update: function (newValue, oldValue) {
                var self = this;
                if (newValue == 'true') {
                    $(self.el).checkbox('set checked');
                } else if (newValue == 'false') {
                    $(self.el).checkbox('set unchecked');
                }
            },
            unbind: function () {
                // do clean up work
                // e.g. remove event listeners added in bind()
            }
        });
        Vue.directive('select', {
            twoWay: true,
            bind: function () {
                var self = this;
                $(self.el).dropdown({
                    placeholder: false,
                    onChange: function (value, text, $choice) {
                        self.set(value)
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
        Vue.directive('selector', {
            twoWay: true,
            bind: function () {
                var self = this;
                $(self.el).dropdown();
            },
            unbind: function () {
                // do clean up work
                // e.g. remove event listeners added in bind()
            }
        });
        Vue.directive('fecha', {
            twoWay: true,
            bind: function () {
                var self = this;
                $(self.el).inputmask('mask', {
                    mask: "99/99/9999",
                    'oncomplete': function () {
                        self.set(self.el.value)
                    }
                });
            },
            update: function (newValue, oldValue) {
                var self = this;
                $(self.el).inputmask("setvalue", newValue);
            },
            unbind: function () {
                var self = this;
                $(self.el).inputmask('remove');
            }
        });
        Vue.directive('mascara', {
            twoWay: true,
            deep: true,
            params: ['tipo'],
            paramWatchers: {
                tipo: function (val, oldVal) {
                    var self = this;
                    var tipos = {cuit: "99-99999999-9", dni: "99999999"};
                    $(self.el).inputmask('remove');
                    $(self.el).inputmask('mask', {
                        mask: tipos[val],
                        'oncomplete': function () {
                            self.set(self.el.value)
                        }
                    });
                }
            },
            bind: function () {
                var self = this;
                $(self.el).inputmask('mask', {
                    mask: "99-99999999-9",
                    'oncomplete': function () {
                        self.set(self.el.value)
                    }
                });
            },
            update: function (newValue, oldValue) {
                var self = this;
                $(self.el).inputmask("setvalue", newValue);
            },
            unbind: function () {
                var self = this;
                $(self.el).inputmask('remove');
            }
        });
        ////////////////////////////////////////////////////////////////////////////    FILTROS
        Vue.filter('fecha', function (value) {

            if (!value) {
                return '--'
            }
            var fecha = moment(value, "YYYY-MM-DD").format('DD/MM/YYYY');
            if (fecha == 'Invalid date') {
                fecha = 'Fecha no Asignada'
            }
            return fecha
        });
        Vue.filter('fechaHora', function (value) {
            if (!value) {
                return '--'
            }
            return moment(value, "YYYY-MM-DD HH:mm:ss").format('DD/MM/YYYY HH:mm:ss');
        });
        Vue.filter('opciones', function (value) {
            if (value == 1) {
                return 'SI';
            } else if (value == 2) {
                return 'NO';
            } else if (value == 3) {
                return 'N/A';
            } else if (value == 0 || !value) {
                return 'SIN ASIGNAR';
            }
        });
        Vue.filter('moneda', function (value, signo) {
            if (!value) {
                return 0;
            }
            var valor = $.number(value, 2, ',', '.');
            var sign;
            if (signo == '1') {
                sign = '$';
            } else if (signo == '2') {
                sign = 'U$S';
            }
            return sign + ' ' + valor;
        });
        Vue.filter('suma', function (list, key1) {
            return list.reduce(function (total, item) {
                return total + item[key1]
            }, 0);
        });
        var mensaje = function (msje) {
            var tipo = 1;
            if (msje.tipo == 1) {
                tipo = 'success';
            } else if (msje.tipo == 2) {
                tipo = 'error';
            }
            var n = noty({
                layout: 'topLeft',
                text: msje.txt,
                theme: 'relax',
                type: tipo,
                timeout: 3000,
                animation: {
                    open: 'animated bounceInLeft',
                    close: 'animated bounceOutLeft'
                }
            });

        }
        ////////////////////////////////////////////////////////////////////////////    TRANSICIONES
        Vue.transition('fade', {
            enterClass: 'fadeInDown',
            leaveClass: 'fadeOutUp'
        })
    </script>
    @yield('script')
</body>
</html>