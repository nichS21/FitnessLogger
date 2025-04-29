<?php
session_start();
if (!isset($_SESSION['uid'])) {
    header("Location: index.php");
    exit();
}

include_once("scriptsPHP/dbConnect.php");
include_once("scriptsPHP/util.php");

// Check if the update form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateDetails'])) {
    // Get updated details from POST
    $newWeight = (int) trim($_POST['weight']);
    $newHeight = (float) trim($_POST['height']);
    $newWeeklyCalGoal = (int) trim($_POST['weeklyCalGoal']);
    
    updateAccount($db, $_SESSION['uid'], $newWeight, $newHeight, $newWeeklyCalGoal);
}

// Retrieve user details (including birthDay to derive age)
$userDetails = showDetails($db, $_SESSION['uid']);
$age = null;
if ($userDetails && !empty($userDetails['birthDay'])) {
    $birthDate = new DateTime($userDetails['birthDay']);
    $currentDate = new DateTime('now');
    $age = date_diff($birthDate, $currentDate)->y;
}
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
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        label {
            display: block;
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
        }
        input[type="number"] {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .btn {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            color: #fff;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #0056b3;
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
                <li><strong>Age:</strong> <?php echo htmlentities($age); ?></li>
                <!-- Use input fields for editable values -->
                <li>
                    <form method="POST" action="account.php">
                        <div class="form-group">
                            <label for="weight">Weight (kg):</label>
                            <input type="number" id="weight" name="weight" value="<?php echo htmlentities($userDetails['weight']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="height">Height (cm):</label>
                            <input type="number" step="any" id="height" name="height" value="<?php echo htmlentities($userDetails['height']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="weeklyCalGoal">Weekly Calorie Goal:</label>
                            <input type="number" id="weeklyCalGoal" name="weeklyCalGoal" value="<?php echo htmlentities($userDetails['weeklyCalGoal']); ?>" required>
                        </div>
                        <button type="submit" name="updateDetails" class="btn">Update Details</button>
                    </form>
                </li>
            </ul>
        <?php else: ?>
            <p>No account details found.</p>
        <?php endif; ?>
        <div class="login-link">
            <a href="dashboard.php">Back to Dashboard</a><br>
            <a href="analytics.php">View Analytics</a>
        </div>
    </div>
</body>
</html>