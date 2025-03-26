<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Workout Logging </title>
    <?php 
        include_once("scriptsPHP/util.php"); 
        neededImports();
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
                            <form method="POST" action="">
                                <label for="logDate"> Choose a date: </label>
                                <input type="date" name="logDate" min="2025-03-01" />
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



<!-- Logging table -->
<div class="container-fluid p-5">
    <div class="table-responsive p-4 shadow" style="background-color:azure;">
        <p class="site-font text-center fw-bold fs-3" style="display:block"> Log for <?php print "DATE_VAR" ?> </p>

        <?php // will need to do a PHP for loop and add row ID automatically once have data ?>

        <table id="logTable">
            <tr> 
                <th>Exercise</th> 
                <th>Sets</th> 
                <th>Reps</th> 
                <th>Weight(lbs)</th> 
                <th>Duration(mins)</th> 
                <th>Notes</th>
            </tr>
            <tr id="row1">
                <td>Squat</td>
                <td>3</td>
                <td>8</td>
                <td>225</td>
                <td>N/A</td>
                <td class="text-wrap">Had to push through the last rep in each set</td>
                <td class="modifyTD">
                    <i class="bi bi-floppy saveBtn clickable" ></i>
                </td>
                <td class="modifyTD">
                    <i class="bi bi-trash delBtn clickable" onclick="openModal('1')"></i>
                </td>
            </tr>
            <tr id="row2">
                <td>Pullups</td>
                <td>3</td>
                <td>10</td>
                <td>0</td>
                <td>N/A</td>
                <td class="text-wrap">Grip endurance could use more attention</td>
                <td class="modifyTD">
                    <i class="bi bi-floppy saveBtn clickable" ></i>
                </td>
                <td class="modifyTD">
                    <i class="bi bi-trash delBtn clickable" onclick="openModal('2')"></i>
                </td>
            </tr>
            <tr id="row3">
                <td>Running</td>
                <td>1</td>
                <td>N/A</td>
                <td>N/A</td>
                <td>10</td>
                <td class="text-wrap">Need to continue working on my stamina</td>
                <td class="modifyTD">
                    <i class="bi bi-floppy saveBtn clickable" ></i>
                </td>
                <td class="modifyTD">
                    <i class="bi bi-trash delBtn clickable" onclick="openModal('3')"></i>
                </td>
            </tr>
        </table>

        <i class="bi bi-plus-square clickable" onclick="addExercise()" style="font-size:50px; margin-left:8%"></i>
    </div>
 </div>

<!-- Weekly Goal Progress -->
<!-- things like how many calories this log burned relative to your weekly goal -->



</body>

</html>