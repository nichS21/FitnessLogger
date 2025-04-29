<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once("scriptsPHP/classes_util.php");

// Make sure sessions are active if you use $_SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load all exercises from DB
$stmt = $db->query("SELECT eid, name FROM Exercise");
$exercises = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $uid = $_SESSION['uid'] ?? 2; // default uid if not logged in
    $templateTitle = trim($_POST['template_title']);
    $exerciseData = json_decode($_POST['template_exercises_json'], true);

    if (!is_array($exerciseData)) {
        die("<p>Error: Invalid or missing exercise data.</p>");
    }

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
                $ex['time'] !== '' ? intval($ex['time']) : null,
                intval($ex['sets']),
                $ex['reps'] !== '' ? intval($ex['reps']) : null,
                $ex['weight'] !== '' ? intval($ex['weight']) : null
            ]);
        }

        showToast("New template created and exercises added!", "success");
    } catch (PDOException $e) {
        showToast("Error: " . $e->getMessage(), "error");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Workout Template</title>
    <?php neededImports(); ?>
    <link rel="stylesheet" href="css/creation.css">
    <script src="js/templates.js" defer></script>
</head>
<body class="site-font">
<?php genNavBar(); ?>

<div class="container mt-5">
    <div class="form-container">
        <h2 class="text-center">New Workout Template</h2>

        <form method="POST" class="mt-4" autocomplete="off">
            <div class="form-group">
                <label for="template_title">Template Title:</label>
                <input type="text" id="template_title" name="template_title" class="form-control" required>
            </div>

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

<!-- Modal for adding exercise details -->
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
