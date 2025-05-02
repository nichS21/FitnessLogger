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

// Load all exercises from DB
$stmt = $db->query("SELECT eid, name FROM Exercise");
$exercises = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $uid = $_SESSION['uid']; 
    $templateTitle = trim($_POST['template_title']);
    $exerciseData = json_decode($_POST['template_exercises_json'], true);

    if (!is_array($exerciseData)) {
        die("<p>Error: Invalid or missing exercise data.</p>");
    }

    // Insert new template into the database
    try {
        $stmt = $db->prepare("INSERT INTO Workout_template (tname, uid) VALUES (?, ?)");
        $stmt->execute([$templateTitle, $uid]);
        $templateId = $db->lastInsertId();

        // Insert exercises linked to the template
        $stmt = $db->prepare("INSERT INTO Templated_exercise (tid, eid, time, sets, reps, weight) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($exerciseData as $ex) {
            $stmt->execute([
                $templateId,
                intval($ex['eid']),
                $ex['time'] !== '' ? intval($ex['time']) : 0,
                intval($ex['sets']),
                $ex['reps'] !== '' ? intval($ex['reps']) : 0,
                $ex['weight'] !== '' ? intval($ex['weight']) : 0
            ]);
        }

        showToast("New template created and exercises added!", "success");
    } catch (PDOException $e) {
        showToast("Error: " . $e->getMessage(), "error");
    }
}
?>

<!--HTML formatting-->
<!DOCTYPE html>
<head>
    <title>Create Workout Template</title>
    <link rel="stylesheet" href="css/creation.css">
    <script src="js/templates.js" defer></script>
</head>
<body class="site-font">
    <div class="container mt-5">
        <div class="form-container">
            <!-- UI to create Workout Template -->
            <h2 class="text-center">New Workout Template</h2>

            <form method="POST" class="mt-4" autocomplete="off">
                <div class="form-group">
                    <label for="template_title">Template Title:</label>
                    <input type="text" id="template_title" name="template_title" class="form-control" required>
                </div>

                <!-- Exercise Selection -->
                <h4 class="mt-4">Search & Select Exercises</h4>
                <input type="text" id="exerciseSearch" class="form-control mb-3" placeholder="Search exercises...">

                <div id="exerciseOuterContainer">
                    <div id="exerciseContainer" class="d-flex flex-wrap gap-2 mb-4">
                        <?php foreach ($exercises as $exercise): ?>
                        <button type="button" class="exercise-btn btn btn-light"
                            data-name="<?= htmlspecialchars($exercise['name'], ENT_QUOTES) ?>"
                            onclick="openExerciseModal(<?= $exercise['eid'] ?>, '<?= htmlspecialchars($exercise['name'], ENT_QUOTES) ?>')">
                            <?= htmlspecialchars($exercise['name']) ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Table to show tempalte preview -->
                <h4 class="mt-4">Workout Template Preview</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Exercise</th>
                            <th>Reps</th>
                            <th>Sets</th>
                            <th>Weight</th>
                            <th>Time (sec)</th>
                            <th>Actions</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody id="templateTableBody">
                        <tr><td colspan="7" class="text-center">No exercises added</td></tr>
                    </tbody>
                </table>

                <input type="hidden" name="template_exercises_json" id="templateExercisesJSON">

                <div class="text-center">
                    <button type="submit" class="btn btn-primary mt-3">Create Template</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for adding/editing exercise details -->
    <div class="modal fade" id="exerciseModal" tabindex="-1" aria-labelledby="modalExerciseLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalExerciseLabel">Add Exercise Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="modalEid">

                    <div class="text-center mb-4">
                        <strong id="modalExerciseName" class="fs-4"></strong>
                    </div>

                    <form id="exerciseForm">
                        <div class="mb-3 row align-items-center">
                            <label for="modalReps" class="col-sm-4 col-form-label text-end">Reps:</label>
                            <div class="col-sm-8">
                                <input type="number" id="modalReps" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3 row align-items-center">
                            <label for="modalSets" class="col-sm-4 col-form-label text-end">Sets:</label>
                            <div class="col-sm-8">
                                <input type="number" id="modalSets" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3 row align-items-center">
                            <label for="modalWeight" class="col-sm-4 col-form-label text-end">Weight (lbs):</label>
                            <div class="col-sm-8">
                                <input type="number" id="modalWeight" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3 row align-items-center">
                            <label for="modalTime" class="col-sm-4 col-form-label text-end">Time (sec):</label>
                            <div class="col-sm-8">
                                <input type="number" id="modalTime" class="form-control">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="addExerciseToTemplate()">OK</button>
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>
