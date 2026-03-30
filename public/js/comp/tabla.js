Vue.component('tabla', {
    template: '#tabla-template',
    props: {
        token: String,
        url: String,
        urlEdit: String,
        urlVer: String,
        urlElim: String,
        urlDescarga: String,
        urlArchivo: String
    },
    data: function () {
        return {
            cargando:false,
            irA: '',
            current_page: 1,
            last_page: 1,
            datos: [],
            cols: [],
            paginas: 0,
            orden: {},
            filtro: {},
            muestraFiltro: false,
            formulario: {},
            idActivo: ''
        }
    },
    watch: {
        'filtro': {
            handler: function (val, oldVal) {
                var self = this;
                self.traeDatos();
            },
            deep: true
        }
    },
    ready: function () {
        this.traeDatos();
    },
    methods: {
        traeDatos: function () {
            var self = this;
            self.cargando = true;
            $.post(this.url, {_token: self.token, page: self.current_page, orden: self.orden, filtro: self.filtro})
                .then(function (r) {
                    self.cols = r.cols;
                    self.paginas = r.data;
                    self.last_page = r.datos.last_page;
                    self.current_page = r.datos.current_page;
                    self.datos = r.datos.data;
                    self.formulario = $('.ui.form.formFiltro').form();
                    self.cargando = false;
                });
        },
        inicio: function () {
            this.current_page = 1;
            this.traeDatos();
        },
        anterior: function () {
            this.current_page--;
            this.traeDatos();
        },
        siguiente: function () {
            this.current_page++;
            this.traeDatos();
        },
        fin: function () {
            this.current_page = this.last_page;
            this.traeDatos();
        },
        irPagina: function () {
            this.current_page = this.irA;
            this.traeDatos();
        },
        ordenarPor: function (campo) {
            var self = this;
            $.each(self.orden, function (k, v) {
                if (k !== campo) {
                    delete self.orden[k];
                }
            });
            self.$set('orden.' + campo, (self.orden[campo] == 'asc') ? 'desc' : 'asc');
            self.traeDatos();
        },
        quitarFiltro: function () {
            var self = this;
            self.formulario.form('clear');
            self.filtro = {};
            self.traeDatos();
        },
        confirmaElim: function ($index, id) {
            var self = this;
            self.idActivo = id;
            self.idxActivo = $index;
            $('.ui.modal.modalEliminar').modal('show');
        },
        eliminar: function () {
            var self = this;
            $.post(self.urlElim, {_token: self.token, 'id': self.idActivo})
                .then(function (r) {
                    if (r.tipo == 1) {
                        self.datos.splice(self.idxActivo, 1);
                        $('.ui.modal.modalEliminar').modal('hide');
                    }
                    mensaje(r);
                }, function () {
                    $('.ui.modal.modalEliminar').modal('hide');
                })
        },
    }
});
