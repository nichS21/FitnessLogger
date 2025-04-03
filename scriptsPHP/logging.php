<?php
/*
 * This file contains the server-side functionality needed for 'loggingPage.php'
 * Written by Nick
 */

 //Function to get and display the the entered exercises for a given log
 function logRows($db, $logID)
 {
    $sql = $db->prepare(
        "SELECT * " .
        "FROM entered_exercise NATURAL JOIN exercise " .
        "WHERE lid = ?"
    );
    $sql->bindParam(1, $logID);
    $result = $sql->execute();

    if($result == true)
    {
        //display each row
        $count = 1;
        while($row = $sql->fetch())
        {
            print "<tr id=row$count>\n";

            print "<td> <input type=\"text\" name=\"exercise\" placeholder=\"exercise\" value=\"" . $row['name'] . "\" required/> </td>";
            print "<td> <input type=\"number\" name=\"sets\" placeholder=\"sets\" value=\"" . $row['sets'] . "\" required/> </td>";
            print "<td> <input type=\"number\" name=\"reps\" placeholder=\"reps\" value=\"" . $row['reps'] . "\" required/> </td>";
            print "<td> <input type=\"number\" name=\"weight\" placeholder=\"weight\" step=\"0.5\" value=\"" . $row['weight'] . "\" required/> </td>";
            print "<td> <input type=\"number\" name=\"duration\" placeholder=\"time\" step=\"0.5\" value=\"" . $row['time'] . "\" required/> </td>";
            print "<td> <textarea name=\"notes\" placeholder=\"notes\">" . $row['notes'] . "</textarea></td>";
?>
            <td class="modifyTD">
                <i class="bi bi-floppy saveBtn clickable" ></i>
            </td>
            <td class="modifyTD">
                <i class="bi bi-trash delBtn clickable" onclick="openModal('1')"></i>
            </td>
<?php

            print "</tr>\n";
            $count++;
        }

    }
    else return;                    //no rows found, could be a new log -> don't display an error message
 }

 //Function that displays the log portion of the page. Shows up only after a valid date with a log is provided by user
 function displayPage($db, $uid, $formData)
 {
    $date = $formData['date']; 

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
    else if($rowCount != 1)                  //No log found for this date, prompt user if they wish to create one
    {
        print "<h3>No log found for '" . $formData['date'] . "'</h3>\n";
        createLog($db, $uid, $date);
        return;                    
    }

    //Otherwise, display log portion of the page
    $logID = $stmt->fetch()['lid'];
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

        <i class="bi bi-plus-square clickable" onclick="addExercise()" style="font-size:50px; margin-left:8%"></i>
    </div>
<?php

 }

 //Function to create a log if the user chooses. Will call displayPage upon success
 function createLog($db, $uid, $date)
 {
    print "<h3>Create a log</h3>";
 }

//Function to save a row of a log 
 function saveRow($db, $lid)
 {

 }

?>