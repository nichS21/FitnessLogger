<!DOCTYPE html>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once("scriptsPHP/classes_util.php");
neededImports();
$uid = $_SESSION['uid'];

// Handle form POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {   
    $className   = $_POST['name'];
    $classDes    = $_POST['description'];
    $classLength = $_POST['class_length'];
    $templateNames = $_POST['template'] ?? [];
    $imageUrl    = $_POST['class_img'] ?? 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3MzY1NzV8MHwxfHNlYXJjaHwxMHx8c3RyZW5ndGh8ZW58MHx8fHwxNzQ1NjgyMDg3fDA&ixlib=rb-4.0.3&q=80&w=1080';

    createClass($db, $uid, $className, $classDes, $classLength, $imageUrl);
    $newCourseID = $db->lastInsertId(); 
    $_SESSION['courseid'] = $newCourseID;
    $_SESSION['latest_course_id'] = $newCourseID;

    $tname = $_POST['template'] ?? null;
    if ($tname) {
        $stmt = $db->prepare("SELECT tid FROM Workout_template WHERE uid = ? AND courseID = ? AND tname = ?");
        $stmt->execute([$uid, $newCourseID, $tname]);
        $stmt->fetchColumn();

        $stmt = $db->prepare("INSERT INTO Workout_template (uid, courseID, tname) VALUES (?, ?, ?)");
        $stmt->execute([$uid, $newCourseID, $tname]);
    }
}

// Load templates
$stmt = $db->prepare("SELECT DISTINCT tname FROM Workout_template WHERE uid = ? ORDER BY tname ASC");
$stmt->execute([$uid]);
$templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<html lang="en">
<head>
    <link rel="stylesheet" href="css/creation.css"> 
    <title>Create Workout Class</title>
    <script> const noTemplates = <?= (count($templates) === 0 ? 'true' : 'false') ?>; </script>
    <script src="js/classes.js" defer></script>
</head>

<body class="site-font">

    <?php genNavBar(); ?>

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
            <select id="template" name="template" class="form-select">
                <?php
                if (count($templates) > 0) {
                    echo '<option value="" disabled selected>Select a Template</option>';
                    foreach ($templates as $row) {
                        $name = htmlspecialchars(trim($row['tname']));
                        echo "<option value=\"$name\">$name</option>\n";
                    }
                } else {
                    echo '<option value="" disabled selected>No templates available</option>';
                }
                ?>
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
