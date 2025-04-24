<?php
session_start();
if (!isset($_SESSION['uid'])) {
    header("Location: index.php");
    exit();
}
include_once("scriptsPHP/dbConnect.php");
include_once("scriptsPHP/util.php");

$uid = $_SESSION['uid'];

// Query user's weeklyCalGoal
$stmt = $db->prepare("SELECT weeklyCalGoal FROM User WHERE uid = ?");
$stmt->execute([$uid]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);
$weeklyCalGoal = $userData['weeklyCalGoal'];

// Query total calories burned from Entered_exercise for the current user
$stmt2 = $db->prepare("SELECT SUM(caloriesBurned) AS totalCal FROM Entered_exercise WHERE uid = ?");
$stmt2->execute([$uid]);
$row = $stmt2->fetch(PDO::FETCH_ASSOC);
$totalCaloriesEntered = $row['totalCal'] ? $row['totalCal'] : 0;

// Query joined data for reps and sets from Entered_exercise and Templated_exercise
$stmt3 = $db->prepare("
    SELECT e.eid, e.reps AS repsEntered, e.sets AS setsEntered, 
           te.reps AS repsTemplated, te.sets AS setsTemplated 
    FROM Entered_exercise e 
    JOIN Templated_exercise te ON e.eid = te.eid 
    WHERE e.uid = ?
");
$stmt3->execute([$uid]);
$exerciseData = $stmt3->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Analytics - AMNT Fitness Logger</title>
    <?php neededImports(); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/site.css">
</head>
<body>
    <?php genNavBar(); ?>
    <h1>Analytics</h1>
    <p>User: <?php echo htmlentities($_SESSION['username']); ?></p>

    <!-- Calories Chart: Total Calories Burned vs Weekly Calorie Goal -->
    <h2>Calories Burned vs Weekly Calorie Goal</h2>
    <canvas id="caloriesChart" width="400" height="200"></canvas>
    <script>
        var ctx = document.getElementById('caloriesChart').getContext('2d');
        var caloriesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Total Calories Burned', 'Weekly Calorie Goal'],
                datasets: [{
                    label: 'Calories',
                    data: [<?php echo $totalCaloriesEntered; ?>, <?php echo $weeklyCalGoal; ?>],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)'
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

    <!-- Exercise Chart: Compare Reps and Sets for each exercise between Entered and Templated -->
    <h2>Exercise Comparison: Reps & Sets</h2>
    <canvas id="exerciseChart" width="600" height="300"></canvas>
    <script>
        // Get exercise data from PHP
        var exerciseData = <?php echo json_encode($exerciseData); ?>;
        var labels = exerciseData.map(function(item) {
            return 'Exercise ' + item.eid;
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

        var ctx2 = document.getElementById('exerciseChart').getContext('2d');
        var exerciseChart = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Reps (Entered)',
                        data: repsEntered,
                        backgroundColor: 'rgba(255, 159, 64, 0.2)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Reps (Templated)',
                        data: repsTemplated,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Sets (Entered)',
                        data: setsEntered,
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Sets (Templated)',
                        data: setsTemplated,
                        backgroundColor: 'rgba(255, 205, 86, 0.2)',
                        borderColor: 'rgba(255, 205, 86, 1)',
                        borderWidth: 1
                    }
                ]
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
</body>
</html>