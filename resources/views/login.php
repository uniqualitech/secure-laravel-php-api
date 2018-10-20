<!DOCTYPE html>



<html lang="en">



    <head>

        <meta charset="utf-8">

        <title>Login</title>

        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">

        <link href="{{ URL::asset('assets/css/bootstrap.css')}}" rel="stylesheet">

        <link href="{{ URL::asset('assets/font-awesome/css/font-awesome.css')}}" rel="stylesheet" />

        <link href="{{ URL::asset('assets/css/style.css')}}" rel="stylesheet">

        <link href="{{ URL::asset('assets/css/style-responsive.css')}}" rel="stylesheet">

        <style type="text/css">

            .form-login h2.form-login-heading {

                background: #378fe5;

            }

            .btn-theme:hover, .btn-theme:focus, .btn-theme:active, .btn-theme.active, .open .dropdown-toggle.btn-theme {

                color: #fff;

                background-color: #378fe5;

            }

            .login-wrap{

                background: #378fe5;

            }

            .form-login .btn{

                width: 50%;

                margin: auto;

            }

        </style>

    </head>



    <body style="background: rgb(55, 143, 229);">

        <div style="text-align:center;

                    color: #ffffff;

                    width: 60%;

                    margin:auto;

                    margin-top: 5%;">

            <h1 style=" font-size: 50px;

                        font-weight: normal;

                        border: 2px solid;

                        width: 35%;

                        padding: 10px;

                        margin: auto;

                        border-radius: 50px;">Calender App

            </h1>

        </div>

        <div id="login-page">



            <div class="container">



                <form class="form-login" action="/login" method="POST" onsubmit ="return validateLogin();">

                    <h2 class="form-login-heading" style="font-weight: 900;text-transform: capitalize;font-size: 30px;"  >sign in now </h2>

                    <div class="login-wrap">

                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <input type="email" class="form-control" placeholder="Email" id="email" name="email_id" >

                        <br>

                        <input type="password" class="form-control" placeholder="Password" name="password" id="password">

                        <br>

                        <button class="btn btn-theme btn-block" type="submit"><i class="fa fa-lock"></i> SIGN IN</button>

                        <label style="color: rgb(185, 0, 0);

                                      margin-top: 10px;

                                      margin-top: 20px;

                                      text-align: center;

                                      width: 100%;

                                      text-transform: capitalize;

                                      font-weight: 900;

                                      font-size: 16px;"> 

                        <?php if(isset($msg)){ ?>

                            {{ $msg }}

                        <?php } ?>

                        </label>

                        <?php

                        if (isset($_GET['msg']) && $_GET['msg'] != "") {

                            if ($_GET['msg'] == "login_fail") {

                                ?>

                                <label style="color: rgb(185, 0, 0);

                                              margin-top: 10px;

                                              margin-top: 20px;

                                              text-align: center;

                                              width: 100%;

                                              text-transform: capitalize;

                                              font-weight: 900;

                                              font-size: 16px;"> 

                                    Invalid Email-id or Password 

                                </label>

                        <?php

                            }

                        }

                        ?>

                    </div>

                </form>

            </div>

        </div>

        <script src="{{ URL::asset('assets/js/jquery.js')}}"></script>

        <script src="{{ URL::asset('assets/js/bootstrap.min.js')}}"></script>

        <script src="{{ URL::asset('assets/js/admin_dev_js.js')}}"></script>

    </body>

</html>



