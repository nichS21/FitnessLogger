<?php
include_once("scriptsPHP/dbConnect.php");
include_once("scriptsPHP/util.php");

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
    $query = "SELECT c.courseID, name, unsplash_url " . 
             "From Course AS c JOIN Enrollment AS e ON c.courseID = e.courseID " .
             "WHERE e.uid=$uid ";

    $res = $db->query($query);
    
    if ($res == false || $res->rowCount() < 1) {
        print "<P>You do not have course so far</P>";
    }
    else {
        while ($row = $res->fetch()) {
            $courseID = $row['courseID'];
            $name = $row['name'];
            $unsplash_url = $row['unsplash_url'];
            
            echo "
            <div class='course'>
                <div class='image-container'>
                    <img src='$unsplash_url' alt='$name'>
                    <div class='overlay'>
                        <button class='course-btn'><a href='./courseDetail.php?courseID=$courseID'>Detail</a></button>
                        
                    </div>
                </div>
                <p>$name</p>
            </div>\n";
        }
    }
}

function genAllCourse($db, $uid) {
    $query =   "SELECT courseID, name, unsplash_url from Course
                EXCEPT
                SELECT c.courseID, name, unsplash_url from Course AS c JOIN Enrollment AS e
                WHERE e.uid=$uid AND e.courseID = c.courseID";

    $res = $db->query($query);

    if ($res == false || $res->rowCount() < 1) {
        print "<p>You have enrolled in all available courses. No new course has been created</p>";
    } 
    else {
        while ($row = $res->fetch()) {
            $courseID = $row['courseID'];
            $name = $row['name'];
            $unsplash_url = $row['unsplash_url'];

            echo "
            <div class='course'>
                <div class='image-container'>
                    <img src='$unsplash_url' alt='$name'>
                    <div class='overlay'>
                        <button class='course-btn'><a href='./courseDetail.php?courseID=$courseID'>Detail</a></button>
                        <form method='POST' action='enrollCourse.php'>
                            <input type='hidden' name='courseID' value='$courseID'>
                            <input type='hidden' name='uid' value='$uid'>
                            <button type='submit' class='course-btn'>Enroll</button>
                        </form>
                    </div>
                </div>
                <p>$name</p>
            </div>\n";
        }
    }
}

?>