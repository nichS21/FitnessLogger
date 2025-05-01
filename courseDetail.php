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
</head>

<body>
    <?php
    neededImports();
    genNavBar();
    ?>
    <main>
        <section class="courses">
            <h2><u>Course details:</u></h2>

            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">First</th>
                        <th scope="col">Last</th>
                        <th scope="col">Handle</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">1</th>
                        <td>Mark</td>
                        <td>Otto</td>
                        <td>@mdo</td>
                    </tr>
                    <tr>
                        <th scope="row">2</th>
                        <td>Jacob</td>
                        <td>Thornton</td>
                        <td>@fat</td>
                    </tr>
                    <tr>
                        <th scope="row">3</th>
                        <td>Larry</td>
                        <td>the Bird</td>
                        <td>@twitter</td>
                    </tr>
                </tbody>
            </table>
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