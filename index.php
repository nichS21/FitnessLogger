<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
session_start();
include_once("scriptsPHP/util.php");
include_once("scriptsPHP/dbConnect.php"); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username       = trim($_POST["username"]);
    $password       = trim($_POST["password"]);
    processLogin($db, $username, $password);
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title> AMNT Fitness Logger </title>
    <?php 
        neededImports();
    ?>
</head>

<style>
    .lBtn{
        font-family: "Helevetica Neue", sans-serif;
        border-radius: 5px;
        border-style: none;
        color: black;
        text-decoration: none;
        padding: 5px;
        width: 150px;
        height: auto;
        font-size:20px;
        display:inline-block;
        text-align:center;
    }
    .lBtn:hover{
        background-color:rgb(57, 137, 184);
    }
    .login{
        background-color:#D3D3D3;
        text-align:center;
        display:inline-block;
        width:70%;
        height:110%;
        position: relative;
        top:-80px;
        left:100px;
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.4), 0 6px 20px 0 rgba(0, 0, 0, 0.38);    
    }

</style>

<body>
    <!-- Nav bar -->
    <div class="container-fluid site-color p-3 text-white"> 
        <div class="row">
            <div class="col-md-1">
                <img src="images/amntLogo.png" height="50px" width="auto" class="mx-auto d-block" /> 
            </div>
            <div class="col-md-9">
                <p class="fs-1" style="display:inline">AMNT Fitness Logger</p>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>  

    <!-- Background image -->
    <div class="container-fluid bg-main p-0">
        <img src="images/Blurred-runners-cropped.jpg" height="150px" width="100%">
    </div>

    <!-- Main content -->
    <div class="container-fluid text-dark">
        <div class="row">
            <div class="col-md-6" style="margin-bottom:80px">
                <div class="p-3">
                    <b>LOG YOUR WORKOUTS</b> <br/>
                    <p>Create your own logs from scratch, or use one created by a coach.</p> <br/>
                    
                    <b>SEE YOUR PROGRESS</b> <br/>
                    <p>Set a weekly calorie goal, and see your logs' contributions.</p> <br/>
    
                    <b>JOIN A CLASS</b> <br/>
                    <p>
                        Join a course offered by a coach and participate in their handmade exercise regimes.
                        Coaches can provide feedback on your progress through your logs.
                    </p>
                </div> 
            </div>
            <div class="col-md-6 text-dark">
                <div class="login p-5">
                    <p class="text-center fs-2">Login:</p>
                    <form method="POST">
                        <input class="input-group p-2" type="text" name="username" placeholder="Username"/> <br/>
                        <input class="input-group p-2" type="password" name="password" placeholder="Password"/> <br/>

                        <button class="lBtn site-color" type="submit">Login</button>
                    </form> <br/>

                    <p class="text-center fs-3" style="font-weight:bold">OR</p> <br/>
                    <a href="signup.php" class="lBtn site-color">Sign Up</a>
                </div>
            </div>
        </div>
    </div>

</body>


</html>