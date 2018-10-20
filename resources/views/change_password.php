<?php 


?>
<!DOCTYPE html>

<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Change password</title>
        <link href="<?php echo url('/')."/"; ?>assets/css/bootstrap.css" rel="stylesheet">
        <link href="<?php echo url('/')."/"; ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
        <link href="<?php echo url('/')."/"; ?>assets/css/style.css" rel="stylesheet">
        <link href="<?php echo url('/')."/"; ?>assets/css/style-responsive.css" rel="stylesheet">
        <style type="text/css">
            .form-horizontal.style-form .form-group{

                border-bottom: none;
            }
        </style>
        <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
        <style type="text/css">
        body{
            font-family: 'Montserrat', sans-serif;
        }
        </style>
    </head>

    <style type="text/css">
    .form-panel {
        background: none;
        margin: 10px;
        padding: 30px;
         box-shadow: none; 
        text-align: left;
    }
    .control-label {
        text-align: left;
        font-size: 15px;
        color: #ffffff;
        font-weight: 900;
        /*margi*/
    }

    .form-control {
        display: block;
        margin-top: 15px;
        }

    .mb
    {
        background: none; 
        padding: 15px;
        color: #ffffff;
        font-size: 30px;
        font-weight: 900;
    }
    .btn-info:focus{
       outline: none; 
    }
    .wrapper {
        margin-top: 0%;
    }    
    </style>

    <body style="background-color:#378FE5; ">
        <div style="text-align:center;
            color: #ffffff;
            width: 60%;
            margin:auto;
            margin-top: 5%;">
            <h1 style="    font-size: 50px;
                    font-weight: normal;
                    border: 2px solid;
                    width: 35%;
                    padding: 10px;
                    margin: auto;
                    border-radius: 50px;">Eclat App
            </h1>
        </div>

        <?php

        if (isset($msg) && $msg == "success") {

            ?>

            <div id="login-page" style="margin: 0 auto;">
                <div class="container">
                    <div style="text-align:center;
                                margin:auto;
                                margin-top: 20%;
                                color: #ffffff;
                                width: 100%;
                                font-size: 42px;">
                        <h1 style="font-size: 50px;">Your password has been changed successfully! Thank you.</h1>
                    </div>
                </div>
            </div>

        <?php

        } else if (isset($data->email_id) && $data->email_id != "" && isset($data->ref_key) && $data->ref_key != "") {

            if (isset($validated_link) && $validated_link == "success") {

        ?>
                <section class="wrapper">

                    <div class="row mt">

                        <div class="col-lg-5" style="margin: auto;float: none;">

                            <div class="form-panel">

                                <h2 class="mb" style="text-align: center;"> Change Password</h2>

                                <div class="tab-pane active" id="horizontal-form">

                                    <form class="form-horizontal style-form" action="/update_password" method="post" enctype="multipart/form-data">

                                        <input type="hidden" name="email_id" value="<?= $data->email_id ?>">

                                        <input type="hidden" name="user_id" value="<?= $data->user_id ?>">

                                        <input type="hidden" name="ref_key" value="<?= $data->ref_key ?>">

                                        <div class="form-group">
                                            <div class="col-sm-6" style="margin: auto;float: none;">
                                                <label class="col-sm-12 control-label" style="padding: 10px 0px;">New Password </label>
                                            </div>
                                            <div class="col-sm-6" style="margin: auto;float: none;">
                                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your New Password"/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-6" style="margin: auto;float: none;">
                                                 <label class="col-sm-12 control-label" style="padding: 10px 0px;">Confirm Password </label>
                                            </div>
                                            <div class="col-sm-6" style="margin: auto;float: none;">
                                                <input type="password" class="form-control" id="re-password" placeholder="Re-Enter your Password"/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-6" style="text-align: center;margin: auto;float: none;">
                                                <button class="btn btn-info" style="background: #ffffff;color: #378fe5;border: none;margin-top: 10px;font-weight: 900;" onclick="return validateChangePassword()">
                                                    Change My Password 
                                                </button>&nbsp;
                                                <label id="change_pwd_error" style="color: #b90000;margin-top: 20px;font-size: 16px;font-weight: 900; display: none;">The passwords you entered do not match. Please try again.</label>
                                            </div>
                                                <div class="col-sm-6" style="margin-top: 10px;margin: auto;float: none;">
                                                <label id="char_error" style="color: red; display: none;">Password must be at least 8 characters long (1 uppercase [A-Z], 1 lowercase [a-z],1 number [0-9])</label>
                                            </div>

                                        </div>

                                    </form>

                                </div>

                            </div>

                        </div>

                    </div>

                </section>

            <?php

            } else {

            ?>

                <div id="login-page" style="margin: 0 auto;">
                    <div class="container">
                        <div style="text-align:center;
                                    margin:auto;
                                    margin-top: 20%;
                                    color: #ffffff;
                                    width: 100%;
                                    font-size: 60px;">
                        <h1 style="font-size: 50px;">This link is expired.</h1>
                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
        ?>

            <div id="login-page" style="margin: 0 auto;">
                <div class="container">
                     <div style="text-align:center;margin:auto;
                                    margin-top: 20%;
                                    color: #ffffff;
                                    width: 100%;
                                    font-size: 42px;">
                                <h1 style="font-size: 50px;">Something went wrong please try again.</h1>
                                </div>
                       <!--  <h4 class="page-head-line" style="border-bottom: 0; text-align: center; margin-top: 40%; color: #fff;">Something went wrong please try again. </h4> -->

                    </div>
                </div>
            </div>
        <?php

        }

        ?>


        <script src="<?php echo url('/')."/"; ?>assets/js/jquery.js"></script>

        <script src="<?php echo url('/')."/"; ?>assets/js/bootstrap.min.js"></script>

        <script src="<?php echo url('/')."/"; ?>assets/js/admin_dev_js.js"></script>

    </body>

</html>

