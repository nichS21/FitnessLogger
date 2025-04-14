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
    if($method == 'scratch')            //create new blank log
    {
        createBlank($db, $uid, $_POST);
        $_SESSION['date'] = $_POST['date'];  //pass date along in session date so logging page can go straight to new log

        header("Location: ../loggingPage.php?func=log");
        exit();
    }
    else if($method == 'template')      //create log from a class workout template
    {
        createTemplate($db, $uid, $_POST);
        $_SESSION['date'] = $_POST['date'];

        header("Location: ../loggingPage.php?func=log");
        exit();
    }
    else if($method == 'previous')      //create log from a previous one by this user
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

    
}

//Creates a new log, prefilled with data from previously created log
function createPrevious($uid, $data)
{

}

?>