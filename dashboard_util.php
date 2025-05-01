<?php
include_once("scriptsPHP/dbConnect.php");
include_once("scriptsPHP/util.php");

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
                <div class='image-container'>
                    <img src='images/core.jpg' alt='$name'>
                    <div class='overlay'>
                        <button class='course-btn'>Detail</button>
                    </div>
                </div>
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
                <div class='image-container'>
                    <img src='images/core.jpg' alt='$name'>
                    <div class='overlay'>
                        <button class='course-btn'><a href='courseDetail.php'>Detail</a></button>
                        <button class='course-btn'><a href='/loggingPage.php'>Enroll</a></button>
                    </div>
                </div>
                <p>$name</p>
            </div>\n";
        }
    }
}

?>