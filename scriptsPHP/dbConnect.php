<?php
// database connection
$server = "cray.cs.gettysburg.edu";
$dbase = "s25_amnt";
$user = "nepaak01";
$pass = "nepaak01";
$dsn = "mysql:host=$server;dbname=$dbase"; //data source name
$options = [ PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
             PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC ];
try {
        $db = new PDO($dsn, $user, $pass, $options);
        
        //print "<h3>Successfully connected to database</h3>\n";
}
catch(PDOException $e) {
        error_log($e->getMessage());
        print "<h3>ERROR connecting to the database</h3>\n";
        echo "<pre>" . $e->getMessage() . "</pre>"; 
        exit();
}

?>