<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'scriptsPHP/classes_util.php';

// Ensure user is logged in
if (!isset($_SESSION['uid'])) {
    header('Location: login.php');
    exit();
}

// Connect to DB (make sure $db is defined in classes_util.php or here)
$studentUid = (int) $_SESSION['uid'];

// Redirect admins/coaches
$stmt = $db->prepare('SELECT 1 FROM Admin WHERE uid = ? LIMIT 1');
$stmt->execute([$studentUid]);
$isAdmin = (bool) $stmt->fetchColumn();

if ($isAdmin) {
    header('Location: dashboard.php');
    exit();
}

// Get logs for this student
$sql = '
    SELECT l.lid,
           c.name AS courseName,
           DATE(l.date) AS logDate,
           l.feedback
    FROM Log l
    JOIN Workout_template wt ON wt.tid = l.tid
    JOIN Course c ON c.courseID = wt.courseID
    WHERE l.uid = :student
    ORDER BY l.date DESC';

$stmt = $db->prepare($sql);
$stmt->execute(['student' => $studentUid]);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get exercises per log
$exStmt = $db->prepare(
    'SELECT e.name,
            ee.sets,
            ee.reps,
            ee.time,
            ee.weight
     FROM Entered_exercise ee
     JOIN Exercise e ON e.eid = ee.eid
     WHERE ee.lid = ?'
);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Feedback</title>
    <link rel="stylesheet" href="css/feedback.css">
</head>
<body>

<h2>My Feedback History</h2>

<div class="feedback-list">
    <?php foreach ($logs as $log): ?>
        <?php 
            // Skip logs with empty or whitespace-only feedback
            if (!isset($log['feedback']) || trim($log['feedback']) === '') continue;

            $exStmt->execute([$log['lid']]);
            $exercises = $exStmt->fetchAll(PDO::FETCH_ASSOC);

            $exerciseDisplay = [];
            foreach ($exercises as $ex) {
                $detail = [];
                if ($ex['sets'])   $detail[] = $ex['sets'] . ' sets';
                if ($ex['reps'])   $detail[] = $ex['reps'] . ' reps';
                if ($ex['time'])   $detail[] = $ex['time'] . ' min';
                if ($ex['weight']) $detail[] = $ex['weight'] . ' lbs';

                $exerciseDisplay[] = $ex['name'] . ' (' . implode(' / ', $detail) . ')';
            }
        ?>
        <div class="feedback-item">
            <div class="feedback-toggle"><?= htmlspecialchars($log['logDate']) ?></div>
            <div class="feedback-content" style="display: none;">
                <h3>Course: <?= htmlspecialchars($log['courseName']) ?></h3>

                <ul class="exercise-grid">
                    <?php if ($exerciseDisplay): ?>
                        <?php foreach ($exerciseDisplay as $exercise): ?>
                            <li><?= htmlspecialchars($exercise) ?></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>No exercises recorded.</li>
                    <?php endif; ?>
                </ul>

                <div class="feedback-text">
                    <?= nl2br(htmlspecialchars($log['feedback'])) ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
// Toggle feedback visibility
document.querySelectorAll('.feedback-toggle').forEach(toggle => {
    toggle.addEventListener('click', () => {
        const content = toggle.nextElementSibling;
        const isOpen = content.style.display === 'block';
        content.style.display = isOpen ? 'none' : 'block';
        toggle.classList.toggle('open', !isOpen);
    });
});
</script>

</body>
</html>
