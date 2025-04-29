<?php
include_once("scriptsPHP/dbConnect.php");

// function debug($str) {
//     print "<div class='debug'>$str</div>";
// }

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
    $query = "SELECT name from Course AS c JOIN Enrollment AS e
              WHERE e.uid=$uid AND e.courseID = c.courseID";

    $res = $db->query($query);
    
    if ($res == false || $res->rowCount() < 1) {
        print "<P>You do not have course so far</P>";
    }
    else {
        while ($row = $res->fetch()) {
            $name = $row['name'];
            
            echo "
            <div class='course'>
                <img src='images/core.jpg' alt='$name'>
                <p>$name</p>
            </div>\n";
        }
    }
}

function genAllCourse($db, $uid) {
    $query =   "SELECT name from Course
                EXCEPT
                SELECT name from Course AS c JOIN Enrollment AS e
                WHERE e.uid=$uid AND e.courseID = c.courseID";

    $res = $db->query($query);

    if ($res == false || $res->rowCount() < 1) {
        print "<p>No course has been created</p>";
    } 
    else {
        while ($row = $res->fetch()) {
            $name = $row['name'];

            echo "
            <div class='course'>
                <img src='images/core.jpg' alt='$name'>
                <p>$name</p>
            </div>\n";
        }
    }
}

?>