@extends('layout.default')
@section('title')
    @parent
@stop
@section('js')
    <script src="{{ asset('js/comp/tabla.js')  }}"></script>
@stop
@section('css')

@stop
@section('cont')
    <div class="viewCont" v-cloak>
        <h1 class="ui dividing header">Listado de Accidentes</h1>
        <a href="{{ URL::to('accidente/agregar') }}" class="ui button green">AGREGAR</a>
        <tabla token="{{ csrf_token()  }}"
               url="{{ URL::to('accidente/tabla')  }}"
               url-edit="{{ URL::to('accidente/editar')  }}"
               url-archivo="{{ asset('adjuntos')   }}"
               url-elim="{{ URL::to('accidente/eliminar')  }}"></tabla>
        @include('layout.tabla')
    </div>
@stop
@section('script')
    <script>
        $(document).ready(function () {

            var token = '{{ csrf_token()  }}';
            var viewCont = new Vue({
                el: '.viewCont',
                data: function () {

                },
                methods: {}
            })

        })
    </script>
@stop