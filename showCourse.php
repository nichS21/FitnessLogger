<?php
// session_start();

include_once("scriptsPHP/util.php");
include("dashboard_util.php");

$uid = 2;

// if (isset($_GET['uid'])) {
//     $uid = $_GET['uid'];
//     genCourse($db, $uid);
// }
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
    <?php
        neededImports();
        genNavBar();
    ?>
    <main>
        <section class="courses">
            <h2><u>All courses:</u></h2>
            <div class="course-grid">
                <?php
                    genAllCourse($db);
                ?>
            </div>
            
        </section>
    </main>
</body>

</html>