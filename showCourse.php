<?php
session_start();

include_once("scriptsPHP/util.php");
include("dashboard_util.php");

//set uid
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
            <h2><u>Your courses:</u></h2>
            <div class="course-grid">
                <?php
                    genCourse($db, $uid);
                ?>
            </div>

        </section>
        <section class="courses">
            <h2><u>Other courses:</u></h2>
            <div class="course-grid">
                <?php
                    genAllCourse($db, $uid);
                ?>
            </div>

        </section>

        <section class="actions">
            <div class="buttons">
                <button class="acctBtn mx-auto d-block" type="button" aria-expanded="false">
                    <a class="gnrlBtn" href="dashboard.php">Back</a>
                </button>
            </div>
        </section>
    </main>
</body>

</html>