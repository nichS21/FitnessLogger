<?php session_start(); ?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Workout Logging </title>
    <?php 
        include_once("scriptsPHP/util.php"); 
        include_once("scriptsPHP/logging.php");
        neededImports();

        $func = "no log";                       //default to no log, must select a date

        $uid = $_SESSION['uid'];                 
    ?>

    <script src="js/logging.js"></script>
  

    <style>
        th{
            border: 1px solid black;
            border-collapse: collapse;
            padding: 15px;
        } 
        td{
            border: 0.5px solid gray;
            border-collapse: collapse;
            padding:10px;
        }
        table {
            margin-left:auto;
            margin-right:auto;
            overflow-x:auto;
        }
        th {
            background-color:deepskyblue;
        } 
        .modifyTD{
            border: none;
            background-color:azure;
            text-align:center
        }
        .modalDel{
            display: none;      /* Default: do not display on page */
            position: fixed;    /* Fixed position outside of normal DOM element flow */
            z-index: 1;         /* Position on top of page */
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%;
            overflow: auto; 
            background-color: rgba(0,0,0,0.4);
        }
        .modalContent{
            position: relative;     /* positioned relative to itself */
            margin: auto;
            background-color: #FFFFFF;
            border: 1px solid rgba(150, 149, 149, 0.4);
            border-radius: 2px;
            width:50%;
            margin-top: 10rem;
            animation-name: slideDown;
            animation-duration: 0.4s;
        }
        @keyframes slideDown {         /*Animate modal sliding down from top*/
             from {top:-300px; opacity:0}
             to {top:0; opacity:1}
        }
        .mHeader{
            padding:10px;
            color: #FFFFFF;
        }
        .mBody{
            padding: 10px;
        }
        .mFooter{
            padding: 10px;
        }
        .inputSelect{
            width: 12vw;
        }
        .inputNum{
            width: 8vw;
        }
        .inputText{
            width: 22vw;
        }
    </style>

</head>

<body class="site-font">
<!-- Nav bar --> 
<?php genNavBar(); ?>


<!-- Delete Confirmation Modal -->
<div class="modalDel" id="delModal">
    <div class="modalContent">
        <div class="mHeader site-color-2nd">
            <p class="fs-4 fw-bold">Confirm delete</p>
        </div>
        <div class="mBody">
            <p class="fs-6"> Are you sure you want to delete this exercise?</p>
        </div>
        <div class="mFooter text-end site-color-2nd">
            <button class="gnrlBtnSmall" id="modalCan">Cancel</button>
            <button class="gnrlBtnSmall" id="modalCon">Delete</button>
        </div>
    </div>
</div>



<!-- Navigation buttons -->
<div class="container">
    <div class="row">
        <div class="col-md-12 p-4">
            <a class="gnrlBtn" href="dashboard.php">To Dashboard</a>
            <button class="gnrlBtn" type="button" data-bs-toggle="collapse" data-bs-target="#searchDate" aria-expanded="false" aria-controls="searchDate">View/Create Log</button>

            <div class="collapse p-2" id="searchDate">
                <div class="mx-auto" style="height:auto; width:80vw;">
                    <form method="POST" action="?func=log">
                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header fw-bold">Choose a date:</div>
                                        <div class="card-body">
                                            <input class="form-control form-control-lg" style="width:20vw" type="date" name="date" id="date" min="2025-01-01" required/>
                                            
                                            <div class="row">
                                                <div class="col-sm-10"></div>
                                                <div class="col-sm-2">
                                                    <button class="gnrlBtn" type="submit">Search</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                              
                                </div>

                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header fw-bold">Your most recent log(s)</div>
                                        <div class="card-body"> <?php fivePrevLogs($db, $uid); ?> </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </form>
                </div>

              

            </div>
        </div>
    </div>
</div>



<!-- Logging table / Create Log -->
<div class="container-fluid p-5">

    <?php 
    
        if(isset($_GET['func']))  $func = $_GET['func'];
        switch($func)
        {
            case 'no log':
                print "<h3>No date has been selected. Please choose one from above to view or create a log.</h3>\n";
                break;
            
            case 'log': 
                //Get date submitted, could be from $_SESSION or $_POST (brand new log or date search on page respectively)
                $date;
                if(isset($_SESSION['date']))
                {
                    $date = $_SESSION['date'];
                    unset($_SESSION['date']);       //unset date after displaying this log, do not want this value stuck in session data
                } 
                else if(isset($_POST['date'])) $date = $_POST['date'];
                else $date = "";

                
                displayPage($db, $uid, $date);
                break;
        }
     
    ?>
  
 </div>

</body>

</html>