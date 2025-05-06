<?php

// Set sessions and include necessary files
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'scriptsPHP/classes_util.php';

// Ensure user is logged in
if (!isset($_SESSION['uid'])) {
    header('Location:index.php');
    exit();
}

// Check if user is an admin (coach)
if (!isset($_SESSION['is_admin'])) {
    $stmt = $db->prepare('SELECT 1 FROM Admin WHERE uid = ? LIMIT 1');
    $stmt->execute([$_SESSION['uid']]);
    $_SESSION['is_admin'] = (bool) $stmt->fetchColumn();
}

// Redirect to dashboard if not an admin
if (!$_SESSION['is_admin']) {
    header('Location:dashboard.php');
    exit();
}

$coachUid = (int) $_SESSION['uid'];

// Handle Save or Delete for feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lid'])) {
    $lid = (int) $_POST['lid'];

    if (isset($_POST['delete'])) {
        deleteLog($db, $lid, $coachUid);      
    } elseif (isset($_POST['save']) && isset($_POST['feedback'])) {
        $feedback = trim($_POST['feedback']);
        updateLog($db, $lid, $coachUid, $feedback);
    }

    header('Location: ' . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);
    exit();
}

$courses = getCoachCourses($db, $coachUid);
$students = getUsers($db, $coachUid);

// Get filters from GET parameters for user and course
$courseFilter = isset($_GET['course']) && ctype_digit($_GET['course']) ? (int) $_GET['course'] : null;
$userFilter = isset($_GET['user']) && ctype_digit($_GET['user']) ? (int) $_GET['user'] : null;

$sql = '
    SELECT l.lid,
           u.username,
           c.courseID,
           c.name AS courseName,
           DATE(l.date) AS logDate,
           l.feedback
    FROM Log l
    JOIN Workout_template wt ON wt.tid = l.tid
    JOIN Course c ON c.courseID = wt.courseID
    JOIN User u ON u.uid = l.uid
    WHERE c.uid = :coach';

$params = ['coach' => $coachUid];

if ($courseFilter) {
    $sql .= ' AND c.courseID = :course';
    $params['course'] = $courseFilter;
}
if ($userFilter) {
    $sql .= ' AND l.uid = :user';
    $params['user'] = $userFilter;
}
$sql .= ' ORDER BY l.date DESC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

<!-- HTML starts here -->

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/feedback.css">
</head>
<body class="site-font">

    <h2>Logs: Add Feedback</h2>

    <div class="filter-container">
        <form method="get" class="filter-bar">
            <label>Course:
                <select name="course">
                    <option value="">All</option>
                    <?php foreach ($courses as $cid => $cname): ?>
                        <option value="<?= $cid ?>" <?= $cid === $courseFilter ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cname) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>User:
            <select name="user">
                <option value="">All</option>
                    <?php foreach ($students as $sid => $sname): ?>
                <option value="<?= $sid ?>" <?= $sid === $userFilter ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sname) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit" class = "gnrlBtn">Filter</button>
    </form>
</div>

<?php if ($courseFilter || $userFilter): ?>
<div style="width: 90%; margin: 0 auto 1rem; font-weight: 600;">
    <?php if ($courseFilter): ?>
        <div>Course: <?= htmlspecialchars($courses[$courseFilter] ?? 'Unknown Course') ?></div>
    <?php endif; ?>
    <?php if ($userFilter): ?>
        <div>User: <?= htmlspecialchars($students[$userFilter] ?? 'Unknown User') ?></div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if (!$logs): ?>
    <p>No logs found for the chosen criteria.</p>
<?php else: ?>
<table>
    <thead>
        <tr>
            <?php if (!$courseFilter): ?><th>Course</th><?php endif; ?>
            <th>Date</th>
            <?php if (!$userFilter): ?><th>User</th><?php endif; ?>
            <th>Exercises</th>
            <th>Feedback</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($logs as $row): ?>
    <?php
        $exStmt->execute([$row['lid']]);
        $exercises = $exStmt->fetchAll(PDO::FETCH_ASSOC);

        $parts = [];
        foreach ($exercises as $ex) {
            $detail = [];
            if ($ex['sets'])   $detail[] = $ex['sets'] . 'sets';
            if ($ex['reps'])   $detail[] = $ex['reps'] . 'reps';
            if ($ex['time'])   $detail[] = $ex['time'] . 'm';
            if ($ex['weight']) $detail[] = $ex['weight'] . 'lbs';
            $parts[] = $ex['name'] . ' (' . implode('/', $detail) . ')';
        }
    ?>
        <form method="post">
        <tr>
            <?php if (!$courseFilter): ?>
                <td><?= htmlspecialchars($row['courseName']) ?></td>
            <?php endif; ?>
            <td><?= htmlspecialchars($row['logDate']) ?></td>
            <?php if (!$userFilter): ?>
                <td><?= htmlspecialchars($row['username']) ?></td>
            <?php endif; ?>
            <td><?= $parts ? htmlspecialchars(implode('; ', $parts)) : '—' ?></td>
            <td>
                <textarea name="feedback"><?= htmlspecialchars($row['feedback'] ?? '') ?></textarea>
                <input type="hidden" name="lid" value="<?= (int) $row['lid'] ?>">
            </td>
            <td>
                <div class="action-buttons">
                    <button type="submit" name="save">Save</button>
                    <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this log?')">Delete</button>
                </div>
            </td>
        </tr>
        </form>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

</body>
</html>
