<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['uid'])) {
    header("Location: index.php");
    exit();
}
include_once("scriptsPHP/dbConnect.php");
include_once("scriptsPHP/util.php"); // Ensure util.php does not call session_start()

$uid = $_SESSION['uid'];

// Assume $selectedDate is obtained from user input (e.g., via GET or POST)
$selectedDate = $_GET['date']; // or $_POST['date']

// Query user's weeklyCalGoal using backticks around column/table names
$stmt = $db->prepare("SELECT weeklyCalGoal FROM `User` WHERE `uid` = ?");
$stmt->execute([$uid]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);
$weeklyCalGoal = $userData['weeklyCalGoal'];

// Query total calories burned from Entered_exercise for the current user
$stmt2 = $db->prepare("
    SELECT SUM(ee.caloriesBurned) AS totalCal 
    FROM Entered_exercise ee 
    JOIN Log l ON ee.lid = l.lid 
    WHERE l.uid = ?
      AND l.date >= ?
      AND l.date < DATE_ADD(?, INTERVAL 7 DAY)
");
$stmt2->execute([$uid, $selectedDate, $selectedDate]);
$rowCal = $stmt2->fetch(PDO::FETCH_ASSOC);
$totalCaloriesEntered = $rowCal['totalCal'] ? $rowCal['totalCal'] : 0;

// Query joined data for reps and sets from Entered_exercise and Templated_exercise
$stmt3 = $db->prepare("
    SELECT ex.name AS exerciseName, 
           e.reps AS repsEntered, 
           e.sets AS setsEntered, 
           te.reps AS repsTemplated, 
           te.sets AS setsTemplated 
    FROM Entered_exercise e 
    JOIN Log l ON e.lid = l.lid 
    JOIN Exercise ex ON e.eid = ex.eid
    JOIN Templated_exercise te ON e.eid = te.eid 
    WHERE l.uid = ?
      AND l.date >= ?
      AND l.date < DATE_ADD(?, INTERVAL 7 DAY)
");
$stmt3->execute([$uid, $selectedDate, $selectedDate]);
$exerciseData = $stmt3->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Analytics - AMNT Fitness Logger</title>
    <?php neededImports(); ?>
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/site.css">
</head>
<body>
    <?php genNavBar(); ?>
    <h1>Analytics</h1>
    <p>User: <?php echo htmlentities($_SESSION['username']); ?></p>
    
    <!-- Date Selection Form -->
    <form method="GET" action="analytics.php">
        <label for="weekDate">Choose Week (Sunday): </label>
        <select name="date" id="weekDate">
            <?php 
                // Get today's date.
                $today = new DateTime();
                // Compute the most recent Sunday: if today is Sunday, that's it; otherwise, roll back.
                $dayOfWeek = $today->format('w'); // Sunday = 0, Monday = 1, ...
                $mostRecentSunday = clone $today;
                if ($dayOfWeek != 0) {
                    $mostRecentSunday->modify("-{$dayOfWeek} days");
                }
                // List the 12 most recent Sundays
                for ($i = 0; $i < 12; $i++) {
                    $sundayStr = $mostRecentSunday->format('Y-m-d');
                    echo "<option value=\"$sundayStr\">$sundayStr</option>";
                    // Go to the previous Sunday
                    $mostRecentSunday->modify("-7 days");
                }
            ?>
        </select>
        <input type="submit" value="View Week">
    </form>
    
    <!-- Calories Chart -->
    <h2>Calories Burned vs Weekly Calorie Goal</h2>
    <canvas id="caloriesChart" width="400" height="200"></canvas>
    <script>
        var ctxCalories = document.getElementById('caloriesChart').getContext('2d');
        var caloriesChart = new Chart(ctxCalories, {
            type: 'bar',
            data: {
                labels: ['Total Calories Burned', 'Weekly Calorie Goal'],
                datasets: [{
                    label: 'Calories',
                    data: [<?php echo $totalCaloriesEntered; ?>, <?php echo $weeklyCalGoal; ?>],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    
    <!-- Exercise Comparison Chart -->
    <h2>Exercise Comparison: Reps & Sets</h2>
    <canvas id="exerciseChart" width="600" height="300"></canvas>
    <script>
        var exerciseData = <?php echo json_encode($exerciseData); ?>;
        
        var labels = exerciseData.map(function(item) {
            return item.exerciseName;
        });
        var repsEntered = exerciseData.map(function(item) {
            return item.repsEntered;
        });
        var repsTemplated = exerciseData.map(function(item) {
            return item.repsTemplated;
        });
        var setsEntered = exerciseData.map(function(item) {
            return item.setsEntered;
        });
        var setsTemplated = exerciseData.map(function(item) {
            return item.setsTemplated;
        });
    
        var ctxEx = document.getElementById('exerciseChart').getContext('2d');
        var exerciseChart = new Chart(ctxEx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Reps (Entered)',
                        data: repsEntered,
                        backgroundColor: 'rgba(255, 159, 64, 0.5)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Reps (Templated)',
                        data: repsTemplated,
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Sets (Entered)',
                        data: setsEntered,
                        backgroundColor: 'rgba(153, 102, 255, 0.5)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Sets (Templated)',
                        data: setsTemplated,
                        backgroundColor: 'rgba(255, 205, 86, 0.5)',
                        borderColor: 'rgba(255, 205, 86, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>