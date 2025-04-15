<!DOCTYPE html>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<head>
    <?php
    include_once("scriptsPHP/util.php"); 
    neededImports();
    ?>
    <link rel="stylesheet" href="css/creation.css"> 
    <title>AMNT Fitness Logger - Create Workout Template</title>
</head>

<body>
    <?php genNavBar(); ?>
    <div class="container mt-5">
        <h2 class="text-center">New Workout Template</h2>
        <form action="" method="POST" class="mt-4">
            <div class="form-group">
                <label for="template_title">Template Title:</label>
                <input type="text" id="template_title" name="template_title" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="template_description">Template Description:</label>
                <textarea id="template_description" name="template_description" class="form-control" rows="3" required></textarea>
            </div>

            <h4>Choose Exercises</h4>
            <div class="d-flex flex-wrap gap-2 mb-4">
                <?php foreach ($exercises as $exercise): ?>
                    <button type="button" class="btn btn-outline-secondary" 
                        onclick="openExerciseModal(<?= $exercise['eid'] ?>, '<?= htmlspecialchars($exercise['name'], ENT_QUOTES) ?>')">
                        <?= htmlspecialchars($exercise['name']) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="form-group" id="workouts">
                <h4>Workouts</h4>
                <div class="workout-entry">
                    <label for="workout_title[]">Workout Title:</label>
                    <input type="text" name="workout_title[]" class="form-control" required><br>

                    <label for="target_area[]">Target Area:</label>
                    <input type="text" name="target_area[]" class="form-control" required><br>

                    <label for="reps_sets[]">Reps/Sets:</label>
                    <input type="text" name="reps_sets[]" class="form-control" required><br><br>
                </div>
            </div>

            <button type="button" class="btn btn-info" onclick="addWorkout()">Add Another Workout</button><br><br>
            <button type="submit" class="btn btn-primary">Create Template</button>
        </form>
    </div>

    <!-- Exercise Modal -->
    <div class="modal fade" id="exerciseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="exerciseForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Exercise Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="eid" id="modalEid">
                    <div class="mb-2"><strong id="modalExerciseName"></strong></div>

                    <label>Reps:</label>
                    <input type="number" name="reps" class="form-control" required>

                    <label>Sets:</label>
                    <input type="number" name="sets" class="form-control" required>

                    <label>Weight (lbs):</label>
                    <input type="number" name="weight" class="form-control">

                    <label>Time (seconds):</label>
                    <input type="number" name="time" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Add Exercise</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function addWorkout() {
            var workoutDiv = document.createElement('div');
            workoutDiv.classList.add('workout-entry');
            workoutDiv.innerHTML = `
                <label for="workout_title[]">Workout Title:</label>
                <input type="text" name="workout_title[]" class="form-control" required><br>

                <label for="target_area[]">Target Area:</label>
                <input type="text" name="target_area[]" class="form-control" required><br>

                <label for="reps_sets[]">Reps/Sets:</label>
                <input type="text" name="reps_sets[]" class="form-control" required><br><br>
            `;
            document.getElementById('workouts').appendChild(workoutDiv);
        }

        function openExerciseModal(eid, name) {
            document.getElementById('modalEid').value = eid;
            document.getElementById('modalExerciseName').innerText = name;
            const modal = new bootstrap.Modal(document.getElementById('exerciseModal'));
            modal.show();
        }
    </script>
</body>
</html>