<?php 

if($validated_link == "success"){

?>
<html>
<title>Active user</title>
<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
<style type="text/css">
    body{
        font-family: 'Montserrat', sans-serif;
        background-color:#378FE5; 
    }
</style>
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
<div style="text-align:center;
            color: #ffffff;
            width: 60%;
            margin:auto;
            margin-top: 10%;">
    <h1 style="font-size: 50px;font-weight: normal;">Your Eclat App account <br/>is now active.</h1>
</div>

</html>

<?php }else{?>

<html>
<title>Active user</title>
<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
<style type="text/css">
    body{
        font-family: 'Montserrat', sans-serif;
        background-color:#378FE5; 
    }
</style>
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
<div style="text-align:center;
            color: #ffffff;
            width: 60%;
            margin:auto;
            margin-top: 10%;">
    <h1 style="font-size: 50px;font-weight: normal;">This link has been expired.</h1>
</div>

</html>
<?php } ?>
