<?php
/*
*   This file created by Nick
*   The script is to handle the create log from on loggingPage.php, and create a log as specified by the user
*/

session_start();
include_once("dbConnect.php");

if(isset($_POST['create']))
{
    //Which method of create?
    $method = $_POST['create'];

    //TODO: use actual session date not hardcoded uid
    $uid = 2;//$_SESSION['uid'];
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
    $sql = "INSERT INTO log (uid, date) " . 
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
    //must contain the new template ID
    //make the new log 
    // $sql = "INSERT INTO log (uid, date) " . 
    // "VALUES (?, ?)";

    // $stmt = $db->prepare($sql);
    // $stmt->bindParam(1, $uid);
    // $stmt->bindParam(2, $data['date']);
    // $res = $stmt->execute(); 

    // if(!$res) exit();                           //if query fails, quit executing code

    //prefill from the template 

    //TODO
    
}

//Creates a new log, prefilled with data from previously created log
function createPrevious($db, $uid, $data)
{
    //make the new log
    $sql = "INSERT INTO log (uid, date) " . 
    "VALUES (?, ?)";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(1, $uid);
    $stmt->bindParam(2, $data['date']);
    $res = $stmt->execute();

    if(!$res) exit();                           //if query fails, quit executing code
    
    $newID = $db->lastInsertId();              //new log's ID


    //prefill with entered exercises from the last log
    $oldLID = $data['prevDate'];


    //first, get the data from the old log
    $sql = "SELECT * FROM entered_exercise WHERE lid = ?";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(1, $oldLID);
    $res = $stmt->execute();


    //put data from old log into new one
    $sql = "INSERT INTO entered_exercise (lid, eid, caloriesBurned, time, sets, reps, weight, notes) \n" . 
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