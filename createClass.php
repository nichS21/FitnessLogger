<!DOCTYPE html>
<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

// Set sessions and include necessary files
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once("scriptsPHP/classes_util.php");

//Check if user is logged in
if (!isset($_SESSION['uid'])) {
    header('Location: index.php');
    exit();
}

// Check if user is an admin(coach)
if (!isset($_SESSION['is_admin'])) {
    $stmt = $db->prepare('SELECT 1 FROM Admin WHERE uid = ? LIMIT 1');
    $stmt->execute([$_SESSION['uid']]);
    $_SESSION['is_admin'] = (bool) $stmt->fetchColumn();
}

if (!$_SESSION['is_admin']) {
    header('Location: dashboard.php');
    exit();
}

$uid = $_SESSION['uid'];

// Handle form POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {   
    $className    = $_POST['name'];
    $classDes     = $_POST['description'];
    $classLength  = $_POST['class_length'];
    $imageUrl     = $_POST['class_img']
                 ?? 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixlib=rb-4.0.3&q=80&w=1080';

    createClass($db, $uid, $className, $classDes, $classLength, $imageUrl);
    $newCourseID = $db->lastInsertId(); 
    $_SESSION['courseid']         = $newCourseID;
    $_SESSION['latest_course_id'] = $newCourseID;

    $tname = $_POST['template'] ?? null;
    if ($tname) {
        $stmt = $db->prepare(
            'UPDATE Workout_template
                SET courseID = ?
              WHERE uid      = ?
                AND tname    = ?
                AND courseID IS NULL'
        );
        $stmt->execute([$newCourseID, $uid, $tname]);

        if ($stmt->rowCount() === 0) {
            $stmt = $db->prepare(
                'INSERT INTO Workout_template (uid, courseID, tname)
                 VALUES (?, ?, ?)'
            );
            $stmt->execute([$uid, $newCourseID, $tname]);
        }
    }
}

$stmt = $db->prepare(
    'SELECT DISTINCT wt.tname
       FROM Workout_template wt
      WHERE wt.uid = ?
        AND NOT EXISTS (
              SELECT 1
                FROM Workout_template wt2
               WHERE wt2.uid   = wt.uid
                 AND wt2.tname = wt.tname
                 AND wt2.courseID IS NOT NULL
            )
      ORDER BY wt.tname ASC'
);
$stmt->execute([$uid]);
$templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<head>
    <link rel="stylesheet" href="css/creation.css"> 
    <title>Create Workout Class</title>
    <script> const noTemplates = <?= (count($templates) === 0 ? 'true' : 'false') ?>; </script> 
    <script src="js/classes.js" defer></script>
</head>

<body class="site-font">
    
    <div class="form-container">
        <h2>New Workout Course</h2>

        <form name="createCourse" method="POST" class="form-grid" autocomplete="off" enctype="multipart/form-data">
            <label for="class_name">Class Name:</label>
            <input type="text" id="class_name" name="name" class="form-control" required>

            <label for="class_length">Class Duration (weeks):</label>
            <input type="number" id="class_length" name="class_length" class="form-control" min="1" max="52" required>

            <label for="class_des">Class Description:</label>
            <input type="text" id="class_des" name="description" class="form-control" required>

            <label for="imageSearch">Search a Free Class Image:</label>
            <input type="text" id="imageSearch" class="form-control mb-2" placeholder="e.g. yoga, HIIT, strength training">

            <div id="imageResults" class="d-flex flex-wrap gap-2 mb-3"></div>
            <div id="imagePreview"></div>

            <input type="hidden" name="class_img" id="selectedImage">

            <small class="text-muted">
                Pictures from <a href="https://unsplash.com" target="_blank">Unsplash</a>
            </small>

            <label for="template">Choose a Template:</label>
            <select id="template" name="template" class="form-select" <?= count($templates) ? '' : 'disabled' ?>>
                <?php if (count($templates) > 0): ?>
                <option value="" disabled selected>Select a Template</option>
                <?php foreach ($templates as $row): ?>
                <?php $name = htmlspecialchars(trim($row['tname'])); ?>
                <option value="<?= $name ?>"><?= $name ?></option>
                <?php endforeach; ?>
                <?php else: ?>
                <option value="" disabled selected>No templates available</option>
                <?php endif; ?>
            </select>
            <button type="submit" class="btn btn-primary mt-3">Create Class</button>
        </form>
    </div>

    <div id="modalOverlay" class="modal-overlay" style="display:none;"></div>

    <div id="noTemplatesModal" class="custom-modal" style="display:none;">
        <button id="closeModalBtn" class="close-button">&times;</button>
        <h2>No Templates Found!</h2>
        <p>Please create a template before creating a class.</p>

        <div class="modal-buttons">
            <button id="createTemplateBtn">Create Template</button>
        </div>

        <div class="modal-cancel">
            <button id="cancelBtn">Cancel</button>
        </div>
    </div>
</body>
</html>
