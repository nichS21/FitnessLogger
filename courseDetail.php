<?php
// session_start();

include_once("scriptsPHP/util.php");
include("dashboard_util.php");

$uid = 1;

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
    <style>
        .register-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            margin-top: 50px;
            width: 100%;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .details-list {
            text-align: center;
            list-style-type: none;
            padding: 0;
        }

        .details-list li {
            font-size: 16px;
            padding: 8px 0;
        }
    </style>
</head>

<body>
    <?php
    neededImports();
    genNavBar();

    $courseID = $_GET['courseID'];

    $query = "SELECT username as 'Created by', name, class_length, description
            FROM Course NATURAL JOIN user WHERE Course.courseID = $courseID";
    $res = $db->query($query);

    if ($res != false) {
        while ($row = $res->fetch()) {
            $username = $row['Created by'];
            $name = $row['name'];
            $length = $row['class_length'];
            $description = $row['description'];
        }
    }
    ?>

    <div class="register-container">
        <h2>Course Details:</h2>
        <ul class="details-list">
            <li><strong>Course ID: </strong><?php print $courseID ?></li>
            <li><strong>Created by: </strong><?php print $username ?></li>
            <li><strong>Course title: </strong><?php print $name ?></li>
            <li><strong>Duration: </strong><?php print $length ?></li>
            <li><strong>Description: </strong><?php print $description ?></li>
        </ul>
    </div>

    <section class="actions">
        <div class="buttons">
            <button class="acctBtn mx-auto d-block" type="button" aria-expanded="false">
                <a class="gnrlBtn" href="showCourse.php">Back</a>
            </button>
        </div>
    </section>
</body>

</html>