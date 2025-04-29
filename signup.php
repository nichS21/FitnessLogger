<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once("scriptsPHP/util.php");
include_once("scriptsPHP/dbConnect.php");

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname       = trim($_POST["fullname"]);
    $username       = trim($_POST["username"]);
    $email          = trim($_POST["email"]);
    $birthday       = trim($_POST["birthday"]);  // New birthday input
    $weight         = (int) trim($_POST["weight"]);
    $height         = (int) trim($_POST["height"]);
    $weeklyCalGoal  = (int) trim($_POST["weeklyCalGoal"]);
    $password       = trim($_POST["password"]);
    
    // Calculate age from birthday
    
    addUser($db, $birthday, $weight, $email, $height, $username, $password, $weeklyCalGoal);
    
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sign Up - AMNT Fitness Logger</title>
    <?php 
        neededImports();
    ?>
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
        .grid-form {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        @media (max-width: 600px) {
            .grid-form {
                grid-template-columns: 1fr;
            }
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
            grid-column: span 2;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-group.full-width {
            grid-column: span 2;
        }
        label {
            display: block;
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="date"] {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus {
            border-color: #007BFF;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
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
            grid-column: span 2;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .login-link {
            margin-top: 15px;
            font-size: 14px;
            grid-column: span 2;
        }
        .login-link a {
            color: #007BFF;
            text-decoration: none;
            margin-left: 5px;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <img src="images/amntLogo.png" alt="FitnessLogger Logo" class="logo">
        <h2>Sign Up for FitnessLogger</h2>
        <form class="grid-form" method="POST" action="signup.php">
            
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="form-group">
                <label for="fullname">Full Name:</label>
                <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <!-- Replace age field with birthday field -->
            <div class="form-group">
                <label for="birthday">Birthday:</label>
                <input type="date" id="birthday" name="birthday" required>
            </div>
            <div class="form-group">
                <label for="weight">Weight (kg):</label>
                <input type="number" id="weight" name="weight" placeholder="Enter your weight in kg" required>
            </div>
            <div class="form-group">
                <label for="height">Height (cm):</label>
                <input type="number" id="height" name="height" placeholder="Enter your height in cm" required>
            </div>
            <div class="form-group">
                <label for="weeklyCalGoal">Weekly Calorie Goal:</label>
                <input type="number" id="weeklyCalGoal" name="weeklyCalGoal" placeholder="Enter your weekly calorie goal" required>
            </div>

            <button type="submit" class="btn">Sign Up</button>
            <div class="login-link">
                Already have an account? <a href="index.php">Login here</a>
            </div>
        </form>
    </div>
</body>
</html>