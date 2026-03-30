<div class="ui menu">
    <a href="{{ URL::to('/') }}" class="header item">SEGAR</a>
    @if (!Auth::guest())
        @can('v-accidente')
            <div class="ui simple dropdown item">
                Accidentes
                <i class="dropdown icon"></i>

                <div class="menu">
                    <a class="item" href="{{ URL::to('accidente/agregar')  }}">Agregar</a>
                    <a class="item" href="{{ URL::to('accidente/listado')  }}">Listado</a>
                </div>
            </div>
            @endif
            @can('v-riesgo')
                <div class="ui simple dropdown item">
                    Riesgos
                    <i class="dropdown icon"></i>

                    <div class="menu">
                        <a class="item" href="{{ URL::to('riesgo/listado')  }}">Listado</a>
                        <a class="item" href="{{ URL::to('riesgo/agregar')  }}">Agregar</a>
                    </div>
                </div>
                @endif
                @can('v-accidente')
                    <a class="item" href="{{ URL::to('informe/index') }}">
                        Informes
                    </a>
                    @can('menu-administracion')
                        <div class="ui simple dropdown item">
                            Administración
                            <i class="dropdown icon"></i>

                            <div class="menu">

                                <a class="item" href="{{ URL::to('incidente/gestion')  }}">Incidentes</a>
                                <a class="item" href="{{ URL::to('cliente/gestion')  }}">Clientes</a>
                                <a class="item" href="{{ URL::to('tipoaccidente/gestion')  }}">Tipo Accidentes</a>
                                <a class="item" href="{{ URL::to('empresa/gestion')  }}">Empresas</a>
                                {{--<a class="item" href="{{ URL::to('lugar/gestion')  }}">Lugar / Equipo</a>--}}
                                <a class="item" href="{{ URL::to('caubasica/gestion')  }}">Causa Básica</a>
                                <a class="item" href="{{ URL::to('cauinm/gestion')  }}">Causa Inmediata</a>
                                <a class="item" href="{{ URL::to('horas/empresa')  }}">Horas Hombre Empresa</a>
                                <a class="item" href="{{ URL::to('horas/cliente')  }}">Horas Hombre Cliente</a>
                                <a class="item" href="{{ URL::to('horas/equipo')  }}">Horas Hombre Equipo</a>
                                <a class="item" href="{{ URL::to('equipo/gestion')  }}">Equipo</a>
                                <div class="divider"></div>
                                <div class="item">
                                    <i class="dropdown icon"></i>
                                    Usuarios
                                    <div class="menu">
                                        <a class="item" href="{{ URL::to('usuario/listado')  }}">Listado</a>
                                        <a class="item" href="{{ URL::to('usuario/crear')  }}">Agregar</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endif
                        @endif
                        <div class="right menu">
                            @if(!Auth::guest())
                                <a class="item">
                                    <i class="icon user"></i>
                                    Usuario
                                </a>
                                <a class="item" href="{{ url('/logout') }}">
                                    <i class="red icon sign out"></i>
                                    Salir
                                </a>
                            @else
                                <a class="item" href="{{ url('/login') }}">
                                    <i class="green icon sign in"></i>
                                    Entrar
                                </a>
                            @endif
                        </div>

</div>