<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>SEGAR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--///////////////////////////////	JS	///////////////////////////////-->
    <script src="{{ asset('js/jquery/dist/jquery.min.js')  }}"></script>
    <script src="{{ asset('semantic/dist/semantic.min.js')  }}"></script>
<!--///////////////////////////////	CSS	///////////////////////////////-->
    <link rel="stylesheet" href="{{ asset('semantic/dist/semantic.min.css')  }}">
    <link rel="stylesheet" href="{{ asset('css/animate.css')  }}">
    <link rel="stylesheet" href="{{ asset('css/gral.css')  }}">
</head>
<body class="fondo">
    <div class="ui vertical masthead center aligned segment">

        <div class="ui container">
            <div class="ui large secondary menu">
                <div class="right item">
                    <a href="{{ URL::to('/login')  }}" class="ui inverted button">Log in</a>
                </div>
            </div>
        </div>

        <div class="ui text container">
            <div class="ui one column grid">
                <div class="column">
                    <div class="ui medium image middle aligned">
                        <img src="{{ asset('img/logos.png')  }}">
                        <br>
                        <img src="{{ asset('img/logos2.png')  }}">
                    </div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
