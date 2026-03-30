@extends('layout.default')

@section('cont')
    <div class="ui middle aligned center aligned grid stackable">
        <div class="six wide column">
            <h2 class="ui teal header">
                <div class="content">INGRESO AL SISTEMA</div>
            </h2>
            <form class="ui large form" role="form" method="POST" action="{{ url('/login') }}">
                {!! csrf_field() !!}
                <div class="ui stacked segment">
                    <div class="field {{ $errors->has('email') ? ' error' : '' }}">
                        <div class="ui left icon input">
                            <i class="user icon"></i>
                            <input type="text" name="email" placeholder="E-mail address" value="{{ old('email') }}">

                        </div>
                        @if ($errors->has('email'))
                            <div class="ui pointing red basic label">
                                <strong>{{ $errors->first('email') }}</strong>
                            </div>
                        @endif
                    </div>
                    <div class="field {{ $errors->has('password') ? ' error' : '' }}">
                        <div class="ui left icon input">
                            <i class="lock icon"></i>
                            <input type="password" name="password" placeholder="Password">

                        </div>
                        @if ($errors->has('password'))
                            <div class="ui pointing red basic label">
                                <strong>{{ $errors->first('password') }}</strong>
                            </div>
                        @endif
                    </div>
                    <button type="submit" class="ui fluid large teal submit button">Login</button>
                </div>

                <a class="btn btn-link" href="{{ url('/password/reset') }}">¿Olvidó su contraseña?</a>

            </form>
        </div>
    </div>
@endsection
