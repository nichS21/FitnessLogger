<?php
/*
*   This file created by Nick
*   The script is to handle the create log from on loggingPage.php, and create a log as specified by the user
*/

session_start();
include_once("dbConnect.php");
include_once("logging.php");        //for calcCals function

if(isset($_POST['create']))
{
    //Which method of create?
    $method = $_POST['create'];

    $uid = $_SESSION['uid'];
    if($method == 'scratch')                    //create new blank log
    {
        createBlank($db, $uid, $_POST);
        $_SESSION['date'] = $_POST['date'];     //pass date along in session date so logging page can go straight to new log

        header("Location: ../loggingPage.php?func=log");
        exit();
    }
    else if($method == 'template')              //create log from a class workout template
    {
        createTemplate($db, $uid, $_POST);
        $_SESSION['date'] = $_POST['date'];

        header("Location: ../loggingPage.php?func=log");
        exit();
    }
    else if($method == 'previous')              //create log from a previous one by this user
    {
        createPrevious($db, $uid, $_POST);
        $_SESSION['date'] = $_POST['date'];

        header("Location: ../loggingPage.php?func=log");
        exit();
    }
    else                                //Something went wrong, redirect back to logging
    {
        header("Location: ../loggingPage.php");
        exit();                         //stop execution
    }
}

//Creates a brand new log, with nothing in it
function createBlank($db, $uid, $data)
{
    //make the new log
    $sql = "INSERT INTO Log (uid, date) " . 
           "VALUES (?, ?)";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(1, $uid);
    $stmt->bindParam(2, $data['date']);
    $res = $stmt->execute(); 

    if(!$res) exit();                           //if query fails, quit executing code
}

//Creates a new log, prefilled with data from a template
function createTemplate($db, $uid, $data)
{
    //make the new log 
    $sql = "INSERT INTO Log (uid, date, tid) " . 
    "VALUES (?, ?, ?)";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(1, $uid);
    $stmt->bindParam(2, $data['date']);
    $stmt->bindParam(3, $data['selectedTemp']);
    $res = $stmt->execute(); 

    if(!$res) exit();                           //if query fails, quit executing code
    
    $newID = $db->lastInsertId();               //new log's ID


    //prefill from the template 
    $sql = "SELECT eid, time, sets, reps, weight " .
            "FROM Templated_exercise " . 
            "WHERE tid = ?";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(1, $data['selectedTemp']);
    $res = $stmt->execute();
    
    if(!$res) exit();                           //if query fails, quit executing code

    //put data from template into new one
    $sql = "INSERT INTO Entered_exercise (lid, eid, caloriesBurned, time, sets, reps, weight, notes) \n" .
           "VALUES \n";

    $numRows = $stmt->rowCount();
    $row = -1;
    $burnedCals = -1;
    for($i = 0; $i < $numRows - 1; $i++)
    {
        $row = $stmt->fetch();

        try
        {
            $burnedCals = calcCals($db, $row);
        }
        catch (Exception $e)
        {
            $burnedCals = 0;
        }

        $sql .= "(" . $newID . ", " . $row['eid'] . ", " . $burnedCals . ", " . $row['time'] 
                    . ", " . $row['sets'] . ", " . $row['reps'] . ", " . $row['weight'] . ", \"\"), \n";
    }

    //last row doesn't need comma: (x, x, x, x ...)
    $row = $stmt->fetch();

    try
    {
        $burnedCals = calcCals($db, $row);
    }
    catch (Exception $e)
    {
        $burnedCals = 0;
    }

    $sql .= "(" . $newID . ", " . $row['eid'] . ", " . $burnedCals . ", " . $row['time'] 
            . ", " . $row['sets'] . ", " . $row['reps'] . ", " . $row['weight'] . ", \"\")";

    //insert new data
    $stmt = $db->prepare($sql);
    $res = $stmt->execute();
}

//Creates a new log, prefilled with data from previously created log
function createPrevious($db, $uid, $data)
{
    //make the new log
    $sql = "INSERT INTO Log (uid, date) " . 
    "VALUES (?, ?)";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(1, $uid);
    $stmt->bindParam(2, $data['date']);
    $res = $stmt->execute();

    if(!$res) exit();                           //if query fails, quit executing code
    
    $newID = $db->lastInsertId();               //new log's ID


    //prefill with entered exercises from the last log
    $oldLID = $data['prevDate'];


    //first, get the data from the old log
    $sql = "SELECT * FROM Entered_exercise WHERE lid = ?";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(1, $oldLID);
    $res = $stmt->execute();


    //put data from old log into new one
    $sql = "INSERT INTO Entered_exercise (lid, eid, caloriesBurned, time, sets, reps, weight, notes) \n" . 
           "VALUES \n";

    $numRows = $stmt->rowCount();
    $row = -1;
    for($i = 0; $i < $numRows - 1; $i++)
    {
        $row = $stmt->fetch();
        $sql .= "(" . $newID . ", " . $row['eid'] . ", " . $row['caloriesBurned'] . ", " . $row['time'] 
                    . ", " . $row['sets'] . ", " . $row['reps'] . ", " . $row['weight'] . ", \"" . $row['notes'] . "\"), \n";
    }

    //last row doesn't need comma: (x, x, x, x ...)
    $row = $stmt->fetch();
    $sql .= "(" . $newID . ", " . $row['eid'] . ", " . $row['caloriesBurned'] . ", " . $row['time'] 
                . ", " . $row['sets'] . ", " . $row['reps'] . ", " . $row['weight'] . ", \"" . $row['notes'] . "\")";


    //insert new data
    $stmt = $db->prepare($sql);
    $res = $stmt->execute();
}

?>