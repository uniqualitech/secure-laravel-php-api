<html>
    <head>
        <title> Eclat App </title>
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
        <link href="{{ URL::asset('assets/css/bootstrap.css')}}" rel="stylesheet">
        <link href="{{ URL::asset('assets/font-awesome/css/font-awesome.css')}}" rel="stylesheet" />
        <link href="{{ URL::asset('assets/css/style.css')}}" rel="stylesheet">
        <link href="{{ URL::asset('assets/css/style-responsive.css')}}" rel="stylesheet">
        <link href="{{ URL::asset('assets/css/jquery-ui.css')}}" rel='stylesheet' type='text/css'>
        <script src="{{ URL::asset('assets/js/jquery-1.11.1.js')}}"></script>
        <script src="{{ URL::asset('assets/js/jquery.min.js')}} "></script>
        <script src="{{ URL::asset('assets/js/jquery-ui.min.js')}} "></script>
        <script src="{{ URL::asset('assets/js/sweetalert.min.js')}}"></script>
        <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/css/sweetalert.css')}}">
        <script src="https://momentjs.com/downloads/moment-with-locales.min.js"></script>
        <style>
            ul.top-menu > li > .user_name {
                color: #fff;
                font-size: 12px;
                border-radius: 15px !important;
                -webkit-border-radius: 4px;
                padding: 5px 15px;
                margin-right: 15px;
                margin-top: 15px;
                text-transform: capitalize;
                font-weight: 900;
                border: 2px solid #ffffff !important;
                background-color: #378fe5 !important;
                cursor: default;
            }
        </style>
    </head>
    <body style="background: #f5f5f5;">
        <section id="container" >
            <header class="header black-bg" style="background: #378fe5;border-bottom:none; ">
                <!-- <div class="sidebar-toggle-box">
                    <div class="fa fa-calendar" style="color: #ffffff;font-size: 20px;"></div>
                </div>
                <a href="" class="logo"><b>Calendar App</b></a> -->
                <ul class="nav pull-right top-menu col-md-12">
                    <li style="float: right;"><a class="logout" href="/logout/">Logout</a></li>
                    <li style="float: right;"><a class="user_name" href="javascript:void(0);"><?php echo Session::get('name'); ?></a></li>
                    
                </ul>
                <div class="top-menu">
                    <div style="text-align:center;
                                color: #ffffff;
                                width: 60%;
                                margin:auto;
                                margin-top: 2%;
                                margin-bottom: 2%;">
                        <h1 style=" font-size: 50px;
                                    font-weight: normal;
                                    border: 2px solid;
                                    width: 35%;
                                    padding: 10px;
                                    margin: auto;
                                    border-radius: 50px;">
                            Eclat App
                        </h1>
                    </div>
                </div>
            </header>
            <section id="main-content" style="color:#000000;">
                @yield('content')    
            </section>
        </section>
        <script src="{{ URL::asset('assets/js/bootstrap.min.js')}} "></script>
    </body>
</html>
