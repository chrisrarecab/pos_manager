<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'POS Manager')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    @vite('resources/css/auth.css')
    <style>
        .img-div {
            width:100%;
            height:100px;
            overflow:hidden;
        }
        .header-img {
            width:100%;
        }
        .navi {
            border-top: 1px solid #808080;
            border-bottom: 1px solid #808080;
            height: 40px;
        }
        .page-register.text-error {
            font-size: 14px;
        }
        #app {
            padding-top: 5%;
        }
        .section-body td {
            padding: 12px;
        }
        .section-footer { padding: 35px; }
    </style>
</head>
<body>
    <div class="img-div">
        <img src="{{URL::asset('/images/img.jpg')}}"class=" header-img" alt="Responsive image">
    </div>
    <main>
        <div id="app">
        @yield('content')
        </div>
    </main>

    @vite('resources/js/app.js')
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"> </script>

    @yield('js')
</body>

</html>
