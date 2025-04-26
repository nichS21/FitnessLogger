<?php
/*
 * This file contains the server-side functionality needed for 'loggingPage.php'
 * Written by Nick
 */

//Function to output all possible exercises for an HTML select; sets given eid to the selected value
function exerciseOpts($db, $eid, $rowID)
{
    //get all exercises
    $sql = "SELECT eid, name FROM exercise";
    $res = $db->query($sql);

    print "<select class=\"form-select inputSelect\" name=\"exercise\" oninput=\"showUnsaved('$rowID')\">\n";

    while($row = $res->fetch())
    {
        $currEid = $row['eid'];

        if($currEid == $eid)
        {
            print " <option value=\"$currEid\" selected>" . $row['name'] . "</option>\n";
        }
        else
        {
            print " <option value=\"$currEid\">" . $row['name'] . "</option>\n";
        }
    }

    print "</select>\n";

}

//Function to get select list of templates for this user (from their enrolled classes)
function templateSelect($db, $uid)
{
    $sql = "SELECT tid, name " . 
           "FROM course AS c " .
           "LEFT JOIN enrollment AS e ON c.courseID = e.courseID " .
           "LEFT JOIN workout_template AS wt ON c.courseID = wt.courseID " .
           "WHERE e.uid = $uid";

    $res = $db->query($sql);

    if(!$res || $res->rowCount() < 1)
    {
        //Display select with no options
        ?>
            <select class="form-select" style="width:50%" name="selectedTemp">
                <option value="-1">No templates to select</option>
            </select>
        <?php
    }
    else
    {
        //Display all templates from classes this user is enrolled in
        print "<select class=\"form-select\" style=\"width:50%\" name=\"selectedTemp\">\n";

        while($row = $res->fetch())
        {
            print " <option value=\"" . $row['tid'] . "\"selected>" . $row['name'] . "</option>\n";
        }

        print "</select>\n";
    }

}

//Function to get select list of previous logs for this user
function previousSelect($db, $uid)
{
    $sql = "SELECT lid, date FROM Log WHERE uid = ?";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(1, $uid);
    $res = $stmt->execute();

    if(!$res || $stmt->rowCount() < 1)
    {
        //Display select with no options
        ?>
            <select class="form-select" style="width:50%" name="prevDate">
                <option value="-1">No dates to select</option>
            </select>
        <?php
    }
    else
    {
        //Display all available previous log dates
        print "<select class=\"form-select\" style=\"width:50%\" name=\"prevDate\">\n";

        while($row = $stmt->fetch())
        {
            $temp =  new DateTime($row['date']);
            $date = date_format($temp, "Y-m-d");                 //format date without hours, minutes, seconds
            print " <option value=\"" . $row['lid'] . "\"selected>" . $date . "</option>\n";
        }

        print "</select>\n";
    }
}

 //Function to get and display the the entered exercises for a given log
 function logRows($db, $logID)
 {
    $sql = $db->prepare(
        "SELECT * " .
        "FROM entered_exercise NATURAL JOIN exercise " .
        "WHERE lid = ? " . 
        "ORDER BY eeid ASC"
    );
    $sql->bindParam(1, $logID);
    $result = $sql->execute();

    if($result == true)
    {
        //display each row
        $count = 1;
        while($row = $sql->fetch())
        {
            $rowID = 'row' . $count;
            print "<tr id=$rowID>\n";

            print "<td>";
            exerciseOpts($db, $row['eid'], $rowID);  
            print "</td>";

            print "<td> <input class=\"form-control inputNum\" type=\"number\" name=\"sets\" value=\"" . $row['sets'] . "\" required min=0 oninput=\"showUnsaved('$rowID')\"/> " . 
                        "<input type=\"hidden\" name=\"eeid\" value=\"" . $row['eeid'] . "\"/> </td>";
            print "<td> <input class=\"form-control inputNum\" type=\"number\" name=\"reps\" value=\"" . $row['reps'] . "\" required min=0 oninput=\"showUnsaved('$rowID')\"/> </td>";
            print "<td> <input class=\"form-control inputNum\" type=\"number\" name=\"weight\" value=\"" . $row['weight'] . "\" required min = 0 oninput=\"showUnsaved('$rowID')\"/> </td>";
            print "<td> <input class=\"form-control inputNum\" type=\"number\" name=\"duration\" value=\"" . $row['time'] . "\" required min=0 oninput=\"showUnsaved('$rowID')\"/> </td>";
            print "<td> <textarea class=\"form-control inputText\" name=\"notes\" placeholder=\"notes\" oninput=\"showUnsaved('$rowID')\">" . $row['notes'] . "</textarea></td>";
?>
            <td class="modifyTD">
                <i class="bi bi-pencil editBtn" ></i>
            </td>
            <td class="modifyTD">
                <i class="bi bi-trash delBtn clickable" onclick="openModal('<?php echo $count; ?>')"></i>
            </td>
<?php

            print "</tr>\n";
            $count++;
        }

        //print lid only once for use with save/edit/delete
        print "<input type='hidden' id='lid' value=\"$logID\"/>";

    }
    else return;                    //no rows found, could be a new log -> don't display an error message
 }

 //Function that displays the log portion of the page. Shows up only after a valid date with a log is provided by user
 function displayPage($db, $uid, $date)
 {
    //check if entered date points to a log
    $sql = "SELECT * " . 
           "FROM Log " .
           "WHERE uid = ? AND (date >= ? AND date < ? + INTERVAL 1 DAY)";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(1, $uid);
    $stmt->bindParam(2, $date);
    $stmt->bindParam(3, $date);
    $resLog = $stmt->execute();
    $rowCount = $stmt->rowCount();

    if($resLog == false)                            //Database error
    {
        print "<h3>An error has occured. Please try again.</h3>\n";
        return;
    }
    else if($rowCount != 1)                         //No log found for this date, prompt user if they wish to create one
    {
        print "<h3>No log found for '" . $date . "'</h3> </br>\n";
        createLog($db, $uid, $date);
        return;                    
    }

    //Otherwise, display log portion of the page
    $row = $stmt->fetch();
    $logID = $row['lid'];
?>
    <div class="table-responsive p-4 shadow" style="background-color:azure;">
        <p class="site-font text-center fw-bold fs-3" style="display:block"> Log for <?php echo $date ?> </p>

        <table id="logTable">
            <tr> 
                <th>Exercise</th> 
                <th>Sets</th> 
                <th>Reps</th> 
                <th>Weight(lbs)</th> 
                <th>Duration(mins)</th> 
                <th>Notes</th>
            </tr>
            <?php logRows($db, $logID); ?>
        </table>

        <i class="bi bi-plus-square clickable" onclick="addExercise()" style="font-size:50px; margin-left:4%"></i>
    </div>

<?php
    //display coachfeedback, if exists, for this log
    if(isset($row['tid']) && isset($row['feedback']))
    {
        //get course name
        $sql = "SELECT name " . 
        "FROM workout_template AS wt " . 
        "LEFT JOIN course AS c ON wt.courseID = c.courseID " . 
        "WHERE tid = " . $row['tid'] . "";

        $res = $db->query($sql);
        $name = $res->fetch()['name'];

        ?>
        <div class="card m-5">
            <div class="card-header">
                Coach Feedback <?php echo " - <b>$name</b>" ?>
            </div>
            <div class="card-body">
                <blockquote class="blockquote mb-0">
                    <p> <?php echo $row['feedback']; ?> </p>
                </blockquote>
            </div>
        </div>

        <?php
    }


 }


 //Function to create a log if the user chooses. Will call displayPage upon success
 function createLog($db, $uid, $date)
 { ?>

    <h3 class="text-center">Create a log below <b>OR</b> Select another date</h3>

    <div class="p-4 shadow mx-auto" style="background-color:azure; width:50vw; height:auto;">
        <form method="POST" action="scriptsPHP\createLog.php">
        <p class="fs-4 fw-bold">Create a log from?</p>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="create" id="scratch" onclick="updateField()" value="scratch">
                <label class="form-check-label" for="scratch">
                    Scratch
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="create" id="template" onclick="updateField()" value="template">
                <label class="form-check-label" for="template">
                    Class Template
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="create" id="previous" onclick="updateField()" value="previous">
                <label class="form-check-label" for="previous">
                    Another Log
                </label>
            </div>

            <!-- Dynamic form inputs for creating from template or previous log -->
            <div class="m-2" style="display:none" id="templateSelect">
                <?php templateSelect($db, $uid); ?>
            </div>

            <div class="m-2" style="display:none" id="previousSelect">
                <?php previousSelect($db, $uid); ?>
            </div>

            <div class="row m-2">
                <div class="col-md-10"></div>
                <div class="col-md-2 justify-content-md-end">
                    <button type="submit" class="gnrlBtn">Create</button>
                </div>
            </div>
            <input type="hidden" name='date' value='<?php echo $date; ?>'>
        </form>
    <div>

<?php
 }


 //Utility function that calculates calories burned for a given entered exercise
function calcCals($db, $formData)
{
    //get values for calculations
    $eid = $formData['eid'];
    $time = $formData['time'];
    $reps = $formData['reps'];
    //$weight = $formData['weight'];
    $sets = $formData['sets'];

    //get calories burned per rep OR calories burned per minute for this exercise
    $sql = "SELECT * FROM exercise WHERE eid = ?";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(1, $eid);
    $res = $stmt->execute();

    //return caloriesBurned
    if($res == false) throw new Exception('Failed to calculate calories.');


    $row = $stmt->fetch();

    if($row['caloriesPerRep'] <= 0)     //calculate based on time
    {
        return ($time * $row['caloriesPerMinute']) * $sets;
    }
    else                                //calculate based on reps
    {           
        return ($reps * $row['caloriesPerRep']) * $sets;
    }
}

//Function that gets five most recent logs a user has created to display on the logging page date collapse box
function fivePrevLogs($db, $uid)
{
    $sql = "SELECT date FROM log " .
           "WHERE uid = ?  " .
           "ORDER BY date DESC " .
           "LIMIT 5";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(1, $uid);
    $res = $stmt->execute();

    if(!$res || $stmt->rowCount() < 1) 
    {
        print "<div class=\"card-header fw-bold\">Your most recent log(s)</div> \n";
        print "<div class=\"card-body\">You haven't created any logs yet.</div> \n";
    }
    else
    {
        print "<div class=\"card-header fw-bold\">Your " . $stmt->rowCount() . " most recent log(s)</div> \n";
        print "<div class=\"card-body\"> \n";
        print "<ul> \n";

        //loop over dates to make a bulleted list
        while($row = $stmt->fetch())
        {
            $temp =  new DateTime($row['date']);
            $date = date_format($temp, "Y-m-d");                 //format date without hours, minutes, seconds
            print "<li>" . $date . "</li>";
        }

        print "</ul> \n";
        print "</div> \n";
    }
}


?>