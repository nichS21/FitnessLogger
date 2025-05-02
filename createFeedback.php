<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'scriptsPHP/classes_util.php';

if (!isset($_SESSION['uid'])) {
    header('Location:index.php');
    exit();
}

if (!isset($_SESSION['is_admin'])) {
    $stmt = $db->prepare('SELECT 1 FROM Admin WHERE uid = ? LIMIT 1');
    $stmt->execute([$_SESSION['uid']]);
    $_SESSION['is_admin'] = (bool) $stmt->fetchColumn();
}
if (!$_SESSION['is_admin']) {
    header('Location:dashboard.php');
    exit();
}

$coachUid = (int) $_SESSION['uid'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lid'], $_POST['feedback'])) {
    $lid      = (int) $_POST['lid'];
    $feedback = trim($_POST['feedback']);

    $stmt = $db->prepare(
        'UPDATE Log l
         JOIN Workout_template wt ON wt.tid = l.tid
         JOIN Course c            ON c.courseID = wt.courseID
         SET l.feedback = :fb
         WHERE l.lid = :lid
           AND c.uid = :coach'
    );
    $stmt->execute([
        'fb'    => $feedback,
        'lid'   => $lid,
        'coach' => $coachUid,
    ]);

    header('Location: ' . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);
    exit();
}

$stmt = $db->prepare('SELECT courseID, name FROM Course WHERE uid = ? ORDER BY name');
$stmt->execute([$coachUid]);
$courses = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$stmt = $db->prepare(
    'SELECT DISTINCT u.uid, u.username
     FROM Enrollment e
     JOIN Course c ON c.courseID = e.courseID
     JOIN User  u ON u.uid      = e.uid
     WHERE c.uid = ?
     ORDER BY u.username'
);
$stmt->execute([$coachUid]);
$students = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$courseFilter = isset($_GET['course']) && ctype_digit($_GET['course'])
    ? (int) $_GET['course'] : null;
$userFilter   = isset($_GET['user']) && ctype_digit($_GET['user'])
    ? (int) $_GET['user'] : null;

$sql = '
    SELECT l.lid,
           u.username,
           c.name        AS courseName,
           DATE(l.date)  AS logDate,
           l.feedback
    FROM Log l
    JOIN Workout_template wt ON wt.tid = l.tid
    JOIN Course c            ON c.courseID = wt.courseID
    JOIN User   u            ON u.uid      = l.uid
    WHERE c.uid = :coach';

$params = ['coach' => $coachUid];

if ($courseFilter) {
    $sql               .= ' AND c.courseID = :course';
    $params['course'] = $courseFilter;
}
if ($userFilter) {
    $sql               .= ' AND l.uid = :user';
    $params['user']   = $userFilter;
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Workout Logs &amp; Feedback</title>
    <link rel="stylesheet" href="css/feedback.css">
</head>
<body class="site-font">

<h2>Logs: Add Feedback</h2>

<form method="get">
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

    <button type="submit">Filter</button>
</form>

<?php if (!$logs): ?>
    <p>No logs found for the chosen criteria.</p>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Course</th>
            <th>User</th>
            <th>Exercises</th>
            <th>Feedback</th>
            <th>Save</th>
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
        if ($ex['sets'])   $detail[] = $ex['sets'].'s';
        if ($ex['reps'])   $detail[] = $ex['reps'].'r';
        if ($ex['time'])   $detail[] = $ex['time'].'m';
        if ($ex['weight']) $detail[] = $ex['weight'].'lb';
        $parts[] = $ex['name'].' ('.implode('/', $detail).')';
    }
?>
        <form method="post">
        <tr>
            <td><?= htmlspecialchars($row['logDate']) ?></td>
            <td><?= htmlspecialchars($row['courseName']) ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= $parts ? htmlspecialchars(implode('; ', $parts)) : '—' ?></td>
            <td>
                <textarea name="feedback"><?= htmlspecialchars($row['feedback'] ?? '') ?></textarea>
                <input type="hidden" name="lid" value="<?= (int) $row['lid'] ?>">
            </td>
            <td><button type="submit">Save</button></td>
        </tr>
        </form>
<?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

</body>
</html>
