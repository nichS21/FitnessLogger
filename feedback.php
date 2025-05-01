<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

// Set sessions and include necessary files
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once("scriptsPHP/classes_util.php");

$uid = $_SESSION['uid'] ?? null;

// Query to get logs and exercises
$sql = "SELECT log.lid, log.date, log.feedback, ex.name AS exercise_name
        FROM Log log JOIN Entered_exercise enterEx ON log.lid = enterEx.lid
                     JOIN Exercise ex ON enterEx.eid = ex.eid
        WHERE log.uid = :uid AND log.feedback IS NOT NULL AND log.feedback != ''
        ORDER BY log.date DESC
        LIMIT 10;
        ";

$stmt = $db->prepare($sql);
$stmt->execute(['uid' => $uid]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group feedback by log ID
$log_data = [];
foreach ($results as $row) {
    $lid = $row['lid'];
    if (!isset($log_data[$lid])) {
        $log_data[$lid] = [
            'date' => $row['date'],
            'feedback' => $row['feedback'],
            'exercises' => []
        ];
    }
    $log_data[$lid]['exercises'][] = $row['exercise_name'];
}
?>

<!--HTML formatting-->
<!DOCTYPE html>
<html>
<head>
    <title>Your Feedback</title>
    <link rel="stylesheet" href="css/feedback.css">
</head>
<body>
    <div class="feedback-list">
        <h2>Your Coach's Feedback</h2>

        <!-- Show feedback according to the log data in a toggle list-->
        <?php if (count($log_data) > 0): ?>
            <?php foreach ($log_data as $lid => $log): ?>
                <div class="feedback-item">
                    <div class="feedback-toggle" onclick="toggleFeedback('log-<?php echo $lid; ?>')">
                        <?php echo date('F j, Y', strtotime($log['date'])); ?>
                    </div>
                    <div class="feedback-body" id="log-<?php echo $lid; ?>">
                        <strong>Exercises:</strong>
                        <?php $exerciseClass = count($log['exercises']) >= 6 ? 'exercise-grid' : ''; ?>
                        <ul class="<?php echo $exerciseClass; ?>">
                            <?php foreach ($log['exercises'] as $exercise): ?>
                                <li><?php echo htmlspecialchars($exercise); ?></li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="feedback-text">
                            <?php echo nl2br(htmlspecialchars($log['feedback'])); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No feedback available yet.</p>
        <?php endif; ?>
    </div>

    <script>
        // Function to toggle feedback visibility
        function toggleFeedback(id) {
            const content = document.getElementById(id);
            if (!content) return;

            const isVisible = content.style.display === "block";
            content.style.display = isVisible ? "none" : "block";

            const header = content.previousElementSibling;
            header.classList.toggle("open");
        }
    </script>
</body>
</html>
