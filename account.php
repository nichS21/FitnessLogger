<?php
session_start();
// if (!isset($_SESSION['uid'])) {
//     header("Location: index.php");
//     exit();
// }

ini_set('display_errors', $_SESSION['uid']);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include_once("scriptsPHP/dbConnect.php");
include_once("scriptsPHP/util.php");

// $userDetails = showDetails($db, $_SESSION['uid']);
$userDetails = showDetails($db, $_SESSION['uid']);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Account Details - AMNT Fitness Logger</title>
    <?php neededImports(); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            background-image: url('images/Blurred-runners-cropped.jpg');
            background-size: cover;
            background-position: center;
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            overflow-y: auto;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .register-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            text-align: center;
            overflow: visible;
        }
        .logo {
            display: block;
            margin: 0 auto 20px;
            max-width: 120px;
        }
        h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }
        .details-list {
            text-align: left;
            list-style-type: none;
            padding: 0;
        }
        .details-list li {
            font-size: 16px;
            padding: 8px 0;
        }
        .login-link {
            margin-top: 15px;
            font-size: 14px;
        }
        .login-link a {
            color: #007BFF;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <img src="images/amntLogo.png" alt="FitnessLogger Logo" class="logo">
        <h2>Account Details for <?php echo htmlentities($userDetails['username']); ?></h2>
        <?php if ($userDetails): ?>
            <ul class="details-list">
                <li><strong>Username:</strong> <?php echo htmlentities($userDetails['username']); ?></li>
                <li><strong>Email:</strong> <?php echo htmlentities($userDetails['email']); ?></li>
                <li><strong>Age:</strong> <?php echo htmlentities($userDetails['age']); ?></li>
                <li><strong>Weight (kg):</strong> <?php echo htmlentities($userDetails['weight']); ?></li>
                <li><strong>Height (cm):</strong> <?php echo htmlentities($userDetails['height']); ?></li>
                <li><strong>Weekly Calorie Goal:</strong> <?php echo htmlentities($userDetails['weeklyCalGoal']); ?></li>
            </ul>
        <?php else: ?>
            <p>No account details found.</p>
        <?php endif; ?>
        <div class="login-link">
            <a href="dashboard.php">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
