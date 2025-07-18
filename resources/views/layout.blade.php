<!DOCTYPE html>
<html>

<head>
    <title>POS Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    <style>
        .img-div{
            width:100%;
            height:100px;
            overflow:hidden;
        }
        .header-img {
            width:100%;
        }
    </style>
</head>
<body>
    <div class="img-div">
        <img src="{{URL::asset('/images/img.jpg')}}"class=" header-img" alt="Responsive image">
    </div>
    @if(session('userId'))
    <nav class="py-2 bg-light border-bottom">
        <div class="container d-flex flex-wrap">
            <ul class="nav me-auto">
            </ul>
            <ul class="nav">
                <!--  text-decoration-underline  -->
                <li class="nav-item"><a href="#" class="nav-link link-dark px-2">Home</a></li>
                <li class="nav-item"><a href="#" class="nav-link link-dark px-2">Configuration</a></li>
                <li class="nav-item"><a href="#" class="nav-link link-dark px-2">Backup</a></li>
                <li class="nav-item"><a href="#" class="nav-link link-dark  px-2">Logs</a></li>
                @if(session('userId'))
                <li class="nav-item">
                    <a href="logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link link-dark px-2">Logout</a>
                    <form id="logout-form" action="logout" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </li>
                @endif
            </ul>
        </div>
    </nav>
    @endif
    <header class="py-3 mb-4">
        <div class="container d-flex flex-wrap justify-content-center">
            <a href="/" class="d-flex align-items-center mb-3 mb-lg-0 me-lg-auto text-dark text-decoration-none">
                <svg class="bi me-2" width="40" height="32">
                    <use xlink:href="#bootstrap" /></svg>
                    <div></div>
                <span class="fs-4 d-block p-2 version"> CIRMS POS<p class="fs-6 text-center"> v.0.0.0.0</p> </span>
                
            </a>
            <form class="col-12 col-lg-auto mb-3 mb-lg-0">
                <ul class="nav">
                    <li class="nav-item">
                        <a href="#" class="nav-link link-dark text-decoration-underline px-2">
                            @if(session('userId'))
                            <h6>Hi {{session('fullName')}}! <span> ({{session('clientGroupId')}})</span></h6>
                            
                            @endif
                        </a>
                    </li>
                </ul>
            </form>
        </div>
    </header>
    <main>
        <div id="app">
        @yield('content')
        </div>
    </main>

    <footer>
    <div class="footer-line"></div>
    </footer>
    @vite('resources/js/app.js')
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"> </script>

    @yield('js')
</body>

</html>
