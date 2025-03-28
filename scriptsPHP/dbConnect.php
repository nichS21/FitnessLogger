<?php
//connect to database
$server = "cray.cs.gettysburg.edu";
$dbase  = "s25_amnt";
$user   = "stacni01";
$pass   = "stacni01";
$dsn    = "mysql:host=$server;dbname=$dbase";       //data source name

try 
{
    $db = new PDO($dsn, $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    //print "<h3>Sucessfully connected to the database.</h3>\n";

    //try query to double check 
    // $sql = "SELECT * FROM User";
    // $result = $db->query($sql);

    // while($row = $result->fetch())
    // {
    //     print "<pre>" . print_r($row) .  "</pre>\n";
    // }

}
catch(PDOException $e)
{
    error_log($e->getMessage());
    print "<h3>ERROR connecting to the database</h3>\n";
    exit();
}

?>