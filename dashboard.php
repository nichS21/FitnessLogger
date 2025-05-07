<?php
session_start();

include("scriptsPHP/util.php");
include("dashboard_util.php");

$uid = $_SESSION['uid'];             
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
                ?>
            </div>
        </section>

        <section class="actions">
            <h3>What do you want to do today?</h3>
            <div class="buttons">
                <div class="button"><a href="./loggingPage.php">Log workouts</a></div>
                <div class="button"><a href="./showCourse.php?menu=enroll">Enroll in Course</a></div>
                <div class="button"><a href="./analytics.php">View Progress</a></div>
                <?php
                    $query = "SELECT uid FROM Admin";
                    $res = $db->query($query);
                    $isAdmin = false;
                    
                    if ($res) {
                        while ($row = $res->fetch()) {
                            if ($row['uid'] == $uid) {
                                $isAdmin = true;
                                break;
                            }
                        }
                    }
                    if ($isAdmin) {
                        echo "
                        <div class='button'><a href='./showCourse.php?menu=enroll'>See All Courses</a></div>
                        <div class='button'><a href='./createClass.php'>Create New Class</a></div>
                        <div class='button'><a href='./createTemp.php'>Create New Template</a></div>
                        <div class='button'><a href='./createFeedback.php'>Record Feedback</a></div>";
                    }
                    else {
                        echo "
                        <div class='button'><a href='./showCourse.php?menu=enroll'>See All Courses</a></div>
                        <div class='button'><a href='./feedback.php'>View Recent Feedback</a></div>";
                    }
                ?>
            </div>
        </section>
    </main>
    <?php
        if (isset($_SESSION['toastClass'])) {
            $toastClass = $_SESSION['toastClass'];
            showToast($toastClass['message'], $toastClass['type']);
            unset($_SESSION['toastClass']);
        }
    ?>
</body>
</html>