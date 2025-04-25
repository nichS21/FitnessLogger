<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

include_once("scriptsPHP/util.php"); 
neededImports();

// Load all exercises from DB
$stmt = $db->query("SELECT eid, name FROM Exercise");
$exercises = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $uid = $_SESSION['uid'] ?? 2;
    $templateTitle = trim($_POST['template_title']);
    $courseID = $_SESSION['courseid'] ?? 1;
    $exerciseData = json_decode($_POST['template_exercises_json'], true);

    if (!is_array($exerciseData)) {
        die("<p>Error: Invalid or missing exercise data.</p>");
    }

    try {
        // Create a new workout template
        $stmt = $db->prepare("INSERT INTO Workout_template (uid, courseID, tname) VALUES (?, ?, ?)");
        $stmt->execute([$uid, $courseID, $templateTitle]);
        $templateId = $db->lastInsertId();

        // Insert exercises for template
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
<html>
<head>
    <title>Create Workout Template</title>
</head>
<body class="site-font">
  <?php genNavBar(); ?>

  <div class="container mt-5">
    <h2 class="text-center">New Workout Template</h2>
    <form method="POST" class="mt-4" autocomplete="off">
        <div class="form-group">
            <label for="template_title">Template Title:</label>
            <input type="text" id="template_title" name="template_title" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="template_description">Template Description:</label>
            <textarea id="template_description" name="template_description" class="form-control" rows="3" required></textarea>
        </div>

        <h4 class="mt-4">Search & Select Exercises</h4>
        <input type="text" id="exerciseSearch" class="form-control mb-3" placeholder="Search exercises...">

        <div id="exerciseContainer" class="d-flex flex-wrap gap-2 mb-4">
            <?php foreach ($exercises as $exercise): ?>
                <button type="button" class="exercise-btn btn btn-light" 
                        data-name="<?= htmlspecialchars($exercise['name'], ENT_QUOTES) ?>"
                        onclick="openExerciseModal(<?= $exercise['eid'] ?>, '<?= htmlspecialchars($exercise['name'], ENT_QUOTES) ?>')">
                    <?= htmlspecialchars($exercise['name']) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Template preview -->
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
                <tr><td colspan="6">No exercises added</td></tr>
            </tbody>
        </table>

        <input type="hidden" name="template_exercises_json" id="templateExercisesJSON">
        <button type="submit" class="btn btn-primary">Create Template</button>
    </form>
</div>

<!-- Modal for adding exercise details -->
<div class="modal fade" id="exerciseModal" tabindex="-1" aria-labelledby="modalExerciseLabel" aria-hidden="true">
    <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalExerciseLabel">Add Exercise Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modalEid">
                <div class="mb-2"><strong id="modalExerciseName"></strong></div>
                <label>Reps:</label>
                <input type="number" id="modalReps" class="form-control" required>
                <label>Sets:</label>
                <input type="number" id="modalSets" class="form-control" required>
                <label>Weight (lbs):</label>
                <input type="number" id="modalWeight" class="form-control">
                <label>Time (seconds):</label>
                <input type="number" id="modalTime" class="form-control">
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="addExerciseToTemplate()">OK</button>
            </div>
        </form>
    </div>
</div>

<script>
  let selectedExercises = [];
  let draggedIndex = null;

  function openExerciseModal(eid, name) {
    document.getElementById('modalEid').value = eid;
    document.getElementById('modalExerciseName').innerText = name;
    document.getElementById('modalReps').value = '';
    document.getElementById('modalSets').value = '';
    document.getElementById('modalWeight').value = '';
    document.getElementById('modalTime').value = '';

    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('exerciseModal'));
    modal.show();
  }

  function addExerciseToTemplate() {
    const eid = document.getElementById('modalEid').value;
    const name = document.getElementById('modalExerciseName').innerText;
    const reps = document.getElementById('modalReps').value;
    const sets = document.getElementById('modalSets').value;
    const weight = document.getElementById('modalWeight').value;
    const time = document.getElementById('modalTime').value;

    selectedExercises.push({ eid, name, reps, sets, weight, time });

    renderExerciseTable();
    bootstrap.Modal.getInstance(document.getElementById('exerciseModal')).hide();
  }


  function renderExerciseTable() {
    const table = document.getElementById('templateTableBody');
    table.innerHTML = '';

    selectedExercises.forEach((ex, index) => {
      const row = document.createElement('tr');
      row.setAttribute('draggable', 'true');
      row.setAttribute('data-index', index);
      row.classList.add('draggable-row');

      row.innerHTML = `
        <td>${ex.name}</td>
        <td contenteditable="true" oninput="updateField(${index}, 'reps', this.innerText)">${ex.reps}</td>
        <td contenteditable="true" oninput="updateField(${index}, 'sets', this.innerText)">${ex.sets}</td>
        <td contenteditable="true" oninput="updateField(${index}, 'weight', this.innerText)">${ex.weight || '-'}</td>
        <td contenteditable="true" oninput="updateField(${index}, 'time', this.innerText)">${ex.time || '-'}</td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeExercise(${index})">Remove</button>
        </td>
        <td class="drag-handle text-center">
            <span style="cursor: move;">⋮⋮⋮</span>
        </td>
      `;

      // Drag event handlers
      row.addEventListener('dragstart', dragStart);
      row.addEventListener('dragover', dragOver);
      row.addEventListener('drop', dropRow);
      row.addEventListener('dragend', dragEnd);

      table.appendChild(row);
    });
  }

  function updateField(index, field, value) {
    selectedExercises[index][field] = value.trim();
  }

  function removeExercise(index) {
    selectedExercises.splice(index, 1);
    renderExerciseTable();
  }

  function dragStart(e) {
    draggedIndex = parseInt(e.target.getAttribute('data-index'));
    e.target.classList.add('dragging');
  }

  function dragOver(e) {
    e.preventDefault(); // Necessary for drop to work
  }

  function dropRow(e) {
    const targetRow = e.target.closest('tr');
    if (!targetRow) return;

    const targetIndex = parseInt(targetRow.getAttribute('data-index'));

    if (draggedIndex === null || targetIndex === draggedIndex) return;

    const movedItem = selectedExercises.splice(draggedIndex, 1)[0];
    selectedExercises.splice(targetIndex, 0, movedItem);

    draggedIndex = null;
    renderExerciseTable(); // Re-render to update 
  }

  function dragEnd(e) {
    e.target.classList.remove('dragging');
  }

  document.getElementById('exerciseSearch').addEventListener('input', function () {
    const query = this.value.toLowerCase();
    document.querySelectorAll('.exercise-btn').forEach(btn => {
      const name = btn.getAttribute('data-name').toLowerCase();
      btn.style.display = name.includes(query) ? 'inline-block' : 'none';
    });
  });

  document.querySelector('form').addEventListener('submit', function () {
    document.getElementById('templateExercisesJSON').value = JSON.stringify(selectedExercises);
  });
</script>


</body>
</html>