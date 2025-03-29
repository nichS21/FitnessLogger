<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Workout Logging </title>
    <?php 
        include_once("scriptsPHP\util.php"); 
        include_once("scriptsPHP\logging.php");
        neededImports();

        $func = "no log";                       //default to no log, must select a date
    ?>

    <script src="js/logging.js"></script>

    <style>
        th, td{
            border: 1px solid black;
            border-collapse: collapse;
            padding: 15px;
        } 
        table {
            margin-left:auto;
            margin-right:auto;
            overflow-x:auto;
        }
        th {
            background-color:deepskyblue;
        } 
        tr:nth-child(even) {
            background-color: #D6EEEE; 
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
    </style>

</head>

<body class="site-font">
<!-- Nav bar --> 
<?php genNavBar() ?>


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
            <a class="gnrlBtn" href="landingPage.php">To Dashboard</a>
            <button class="gnrlBtn" type="button" data-bs-toggle="collapse" data-bs-target="#searchDate" aria-expanded="false" aria-controls="searchDate">View/Create Log</button>

            <div class="collapse p-2" id="searchDate">
                <div class="card card-body">
                    <div class="row">
                        <div class="col-md-6 d-inline">
                            <form method="POST" action="?func=log">
                                <label for="date"> Choose a date: </label>
                                <input type="date" name="date" min="2025-01-01" required/>
                                <button class="gnrlBtn" type="submit">Search</button>

                            
                            </form>
                        </div>
                        <!-- JS to display result of search -->
                        <div class="col-md-6 d-inline site-color">This div will hold the search result and the option to create a log (from scratch/template) if none exist.</div>
                    </div>
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
                //TODO:
                //HARDCODED UID, remove once login complete
                displayPage($db, 2, $_POST);
                //displayPage($db, $_SESSION['uid'], $_POST);
                break;
        }
     
    ?>

    
 </div>

<!-- Weekly Goal Progress -->
<!-- things like how many calories this log burned relative to your weekly goal -->



</body>

</html>