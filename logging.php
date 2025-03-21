<!DOCTYPE html>
<html>
<head>
    <title> Workout Logging </title>
    <?php 
        include_once("scriptsPHP\util.php"); 
        neededImports();
    ?>

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
    </style>

</head>

<body>
<!-- Nav bar --> 
<?php genNavBar() ?>


<!-- Navigation buttons -->
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
                <div class="col-md-6 d-inline site-color">This div will hold the search result and the option to create a a log (from scratch/template) if none exist.</div>
            </div>
        </div>
    </div>
</div>



<!-- Logging table -->
<div class="container-fluid p-5">
    <div class="table-responsive p-4 shadow" style="background-color:azure;">
        <p class="site-font text-center fw-bold fs-3" style="display:block"> Log for <?php print "DATE_VAR" ?> </p>

        <table>
            <tr> 
                <th>Exercise</th> 
                <th>Sets</th> 
                <th>Reps</th> 
                <th>Weight(lbs)</th> 
                <th>Duration(mins)</th> 
                <th>Notes</th>
            </tr>
            <tr>
                <td>Squat</td>
                <td>3</td>
                <td>8</td>
                <td>225</td>
                <td>N/A</td>
                <td class="text-wrap">Had to push through the last rep in each set</td>
                <td class="modifyTD">
                    <i class="bi bi-floppy saveBtn" ></i>
                </td>
                <td class="modifyTD">
                    <i class="bi bi-trash delBtn"></i>
                </td>
            </tr>
            <tr>
                <td>Pullups</td>
                <td>3</td>
                <td>10</td>
                <td>0</td>
                <td>N/A</td>
                <td class="text-wrap">Grip endurance could use more attention</td>
                <td class="modifyTD">
                    <i class="bi bi-floppy saveBtn" ></i>
                </td>
                <td class="modifyTD">
                    <i class="bi bi-trash delBtn"></i>
                </td>
            </tr>
            <tr>
                <td>Running</td>
                <td>1</td>
                <td>N/A</td>
                <td>N/A</td>
                <td>10</td>
                <td class="text-wrap">Need to continue working on my stamina</td>
                <td class="modifyTD">
                    <i class="bi bi-floppy saveBtn" ></i>
                </td>
                <td class="modifyTD">
                    <i class="bi bi-trash delBtn"></i>
                </td>
            </tr>
        </table>

        <i class="bi bi-plus-square" style="font-size:50px"></i>
    </div>
 </div>

<!-- Weekly Goal Progress -->
<!-- things like how many calories this log burned relative to your weekly goal -->


</body>

</html>