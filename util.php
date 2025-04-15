<?php
include_once("scriptsPHP/dbConnect.php");

function debug($str) {
    print "<div class='debug'>$str</div>";
}

function genUsers($db) {
    $query = "SELECT uid, username FROM User";
    $res = $db->query($query);

    if ($res == false) {
        print "<P>Fail to generate users</P>";
    }
    else {
        print "<OPTION value=name>User</OPTION>\n";

        while ($row = $res->fetch()) {
            $uid = $row['uid'];
            $username = $row['username'];
            print "<OPTION value='$uid'>$username</OPTION>\n";
        }
    }
}

function genCourse($db, $uid) {
    $query = "SELECT name from Course NATURAL JOIN User WHERE uid=$uid";
    $res = $db->query($query);
    
    if ($res == false) {
        print "<P>Fail to generate courses</P>";
    }
    else {
        while ($row = $res->fetch()) {
            $name = $row['name'];
            // print "<P>$table</P>\n";
            echo "<div class='course'><P>$name</P></div>\n";
        }
    }
}

?>