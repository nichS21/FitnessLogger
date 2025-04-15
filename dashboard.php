<?php
session_start();

include_once("scriptsPHP/util.php");
include("util.php");

// neededImports();
// $user = "User";

// $query = "SELECT * FROM User";
// $result = $db->query($query);
// print_r($result);
// print "<div>$result</div>";

if (isset($_GET['uid'])) {
    $uid = $_GET['uid'];
    genCourse($db, $uid);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>AMNT Fitness Logger</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <!-- <script src="script.js"></script> -->
</head>

<body>
    <header>
        <div class="logo">
            <img src="images/amntLogo.png" alt="Logo">
            <span>AMNT Fitness Logger</span>
        </div>
        <div class="user-info">
            <img src="images/avatar.png" alt="User Icon">
            <select id="user" name="user">
                <?php genUsers($db); ?>
            </select>

        </div>
    </header>

    <main>
        <section class="courses">
            <h2><u>My courses:</u></h2>
            <div class="course-grid">
                <!-- <div class="course">
                    <img src="images/arm.jpg" alt="Arms">
                    
                </div> -->
                <div class="course">
                    <img src="images/arm.jpg" alt="Arms">
                    <p>W1121: Arms</p>
                </div>
                <div class="course">
                    <img src="images/leg.jpg" alt="Legs">
                    <p>W1322: Legs</p>
                </div>
                <div class="course">
                    <img src="images/core.jpg" alt="Core">
                    <p>W1352: Core</p>
                </div>
            </div>
            <div class="track">
                <div class="progress-bar" data-label="Calories..."></div>
                <!-- <div class="text">Goal</div> -->
            </div>
        </section>

        <section class="actions">
            <h3>What do you want to do today?</h3>
            <div class="buttons">
                <button>Log workouts</button>
                <button>Enroll in Course</button>
                <button>View Progress</button>
                <button>Review Coach’s Feedback</button>
            </div>
        </section>
    </main>
</body>

</html>