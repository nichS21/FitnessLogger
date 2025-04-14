<?php 
/*
*   This file serves as an endpoint that JS on a client's browser can call
*   and send data to, to either save or delete a row in a log.
*
*   This file written by Nick
*/

include_once("dbConnect.php");


//Utility function that grabs all exercises that can be selected with a log, for use in <select> element
function execSelect($db, $formData)
{
    try{
        //get all exercises
        $sql = "SELECT eid, name FROM exercise";
        $res = $db->query($sql);

        if($res == false) throw new Exception('Failed to get exercises from database.');

        $rowID = $formData['rowID'];

        $selectString = "<select class=\"form-select inputSelect\" name=\"exercise\" oninput=\"showUnsaved('$rowID')\">\n";

        while($row = $res->fetch())
        {
        $selectString .= " <option value=\"". $row['eid'] . "\">" . $row['name'] . "</option>\n";
        }

        $selectString .= "</select>\n";

        //encode data to send back
        echo json_encode(array(
            'msg' => 'Success',
            'select' => $selectString
            )
        );
    }
    catch (Exception $e)
    {
        //pass an error message back to JS on client's side
        echo json_encode(array(
            'msg' => $e->getMessage()
            )
        );
    }
}

//Utility function that calculates colories burned for a given entered exercise
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


//Function to save a row of a log to the DB
function saveRow($db, $formData)
{
    try
    {
        //calculate calories burned
        $burnedCals = calcCals($db, $formData);

        //insert the data
        $sql = "INSERT INTO entered_exercise (lid, eid, caloriesBurned, time, sets, reps, weight, notes) " .
        "VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $bindParams = array($formData['lid'], $formData['eid'], $burnedCals, $formData['time'], $formData['sets'], $formData['reps'], $formData['weight'], $formData['notes']);
        $stmt = $db->prepare($sql);
        $res = $stmt->execute($bindParams);

        if($res == false) throw new Exception("Failed to save row to database.");
        $lastId = $db->lastInsertId();

        //pass a success message back to JS on client's side
        echo json_encode(array(
            'msg' => 'Success', 
            'eeid' => $lastId  
            )
        );

    }
    catch (Exception $e) 
    {
        //pass an error message back to JS on client's side
        echo json_encode(array(
            'msg' => $e->getMessage()
            )
        );
    } 
}

//Function to update a row of a log, in the DB
function updateRow($db, $formData)
{
    try
    {
        //calculate calories burned
        $burnedCals = calcCals($db, $formData);

        //update the data
        $sql = "UPDATE entered_exercise " . 
               "SET eid = ?, caloriesBurned = ?, time = ?, sets = ?, reps = ?, weight = ?, notes = ? " . 
               "WHERE eeid = ? ";

        $bindParams = array($formData['eid'], $burnedCals, $formData['time'], $formData['sets'], $formData['reps'], $formData['weight'], $formData['notes'], $formData['eeid']);
        
        $stmt = $db->prepare($sql);
        $res = $stmt->execute($bindParams);

        if($res == false) throw new Exception("Failed to save row to database");

        //pass a success message back to JS on client's side
        echo json_encode(array(
                'msg' => 'Success'
            )
        );
    }
    catch (Exception $e) 
    {
        //pass an error message back to JS on client's side
        echo json_encode(array(
                'msg' => $e->getMessage()
            )
        );
    }
}

//Function to delete a row of a log from the DB
function delRow($db, $formData)
{
    try
    {
        //delete the data
        $sql = "DELETE FROM entered_exercise " . 
               "WHERE eeid = ? ";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(1, $formData['eeid']);
        $res = $stmt->execute();

        if($res == false) throw new Exception("Failed to delete row in database");

        //pass a success message back to JS on client's side
        echo json_encode(array(
                'msg' => 'Success'
            )
        );
    }
    catch (Exception $e) 
    {
        //pass an error message back to JS on client's side
        echo json_encode(array(
                'msg' => $e->getMessage()
            )
        );
    }
}

//If this file requested, see which function to call
$formData = json_decode(file_get_contents('php://input'), true);    //Must use this instead of $_POST to recieve all data

if($formData['action'] == 'saveRow'){
    saveRow($db, $formData);
}
else if($formData['action'] == 'updateRow')
{
    updateRow($db, $formData);
}
else if($formData['action'] == 'delRow'){
    delRow($db, $formData);
}
else if($formData['action'] == 'getExercises')
{
    execSelect($db, $formData);
}
else return;


?>