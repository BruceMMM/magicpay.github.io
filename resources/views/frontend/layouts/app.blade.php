<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <!-- Bootstrap Css -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">

    <!-- Fontawesome  -->
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>


    <!-- Google font(Open Sans) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital@0;1&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{asset('frontend/css/style.css')}}">

    <!-- Data Range Picker Js    -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    @yield('extra_css')

</head>
<body>
    <div id="app">

    <div class="header-menu">
            <div class="d-flex justify-content-center">
                <div class="col-md-8" >
                    <div class="d-flex ">
                        <div class="col-1 text-center">
                            @if(!request()->is('/'))
                            <a href="" class="back-btn">
                                <i class="fas fa-angle-left"></i>
                            </a>
                            @endif
                        </div>
                        <div class="col-10 text-center">
                            <a href="">
                                <h3>@yield('title')</h3>
                            </a>
                        </div>
                        <div class="col-1 text-center">
                            <a href="{{url('notification')}}">
                                <i class="fas fa-bell"></i>
                                <span class="badge badge-pill badge-danger unread-noti-count">{{$unread_noti_count}}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="d-flex justify-content-center">
                <div class="col-md-8">
                    @yield('content')
                </div>
            </div>
        </div>

        <div class="botton-menu">
            <a href="{{url('scan-and-pay')}}" class="scan-tab">
                <div class="inside">
                    <i class="fas fa-qrcode"></i>
                </div>
            </a>


            <div class="d-flex justify-content-center">
                <div class="col-md-8" >
                    <div class="d-flex ">
                        <div class="col-md-3 text-center">
                            <a href="{{route('home')}}" class="@yield('home-active')">
                                <i class="fas fa-home"></i>
                                <p>Home</p>
                            </a>
                        </div>
                        <div class="col-md-3 text-center">
                            <a href="{{route('wallet')}}" class="@yield('wallet-active')">
                                <i class="fas fa-wallet"></i>
                                <p>Wallet</p>
                            </a>
                        </div>
                        <div class="col-md-3 text-center">
                            <a href="{{route('transaction')}}" class="@yield('transaction-active')">
                                <i class="fas fa-exchange-alt"></i>
                                <p>Transation</p>
                            </a>
                        </div>
                        <div class="col-md-3 text-center">
                            <a href="{{route('profile')}}"class="@yield('profile-active')">
                                <i class="fas fa-user"></i>
                                <p>Account</p>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>



      <!-- Sweet Alert 2 -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js" integrity="sha384-+YQ4JLhjyBLPDQt//I+STsc9iw4uQqACwlvpslubQzn4u2UU2UFM80nGisd026JF" crossorigin="anonymous"></script>



    <script>
        $(document).ready(function(){
            let token = document.head.querySelector('meta[name="csrf-token"]');
            if(token){
                $.ajaxSetup({
                    headers : {
                        'X-CSRF_TOKEN' : token.content,
                        'Content-Type' : 'application/json',
                        'Accept' : 'application/json'
                    }
                });
            }

            const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
            })

            @if(session('create'))
            Toast.fire({
                icon: 'success',
                title: "{{session('create')}}"
            });
            @endif

            @if(session('update'))
            Toast.fire({
                icon: 'success',
                title: "{{session('update')}}"
            });
            @endif

            $('.back-btn').on('click', function(e){
                e.preventDefault();
                window.history.go(-1);
                return false;
            })

        });
    </script>

    <script src="{{asset('frontend/js/jscroll.min.js')}}"></script>


    <!-- Data Range Picker Js    -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    @yield('script')
</body>
</html>
