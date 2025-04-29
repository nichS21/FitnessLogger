<?php
session_start();

include_once("scriptsPHP/util.php");
include("dashboard_util.php");

// neededImports();
// $user = "User";

// $query = "SELECT * FROM User";
// $result = $db->query($query);
// print_r($result);
// print "<div>$result</div>";

$uid = 1;

// if (isset($_GET['uid'])) {
//     $uid = $_GET['uid'];
// }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>AMNT Fitness Logger</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>

<body>
    <?php
        neededImports();
        genNavBar();
    ?>
    <main>
        <section class="courses">
            <h2><u>My courses:</u></h2>
            <div class="course-grid">
                <?php
                    genCourse($db, $uid);
                    // if (isset($_GET['uid'])) {
                    //     $uid = $_GET['uid'];
                    //     genCourse($db, $uid);
                    // }
                    // else {
                    //     echo "<p>You do not have any course so far</p>";
                    // }
                ?>
            </div>
            <div class="track">
                <div class="progress-bar" data-label="Calories..."></div>
                <!-- <div class="text">Goal</div> -->
            </div>
        </section>

        <section class="actions">
            <h3>What do you want to do today?</h3>
            <div class="buttons">
                <div class="button"><a href="/loggingPage.php">Log workouts</a></div>
                <div class="button"><a href="/showCourse.php?menu=enroll">Enroll in Course</a></div>
                <div class="button"><a href="?menu=progress">View Progress</a></div>
                <div class="button"><a href="?menu=feedback">Review Coach’s Feedback</a></div>
            </div>
        </section>
    </main>
</body>

</html>