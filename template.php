<?php 
    include_once("scriptsPHP/util.php"); 
    neededImports();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/courseCreate.css"> 
    <title>AMNT Fitness Logger - Create Workout Template</title>
    
</head>

<body>
    <?php genNavBar(); ?>
    <div class="container mt-5">
        <h2 class="text-center">New Workout Template</h2>
        <form action="" method="POST" class="mt-4">
            <!-- Template Title -->
            <div class="form-group">
                <label for="template_title">Template Title:</label>
                <input type="text" id="template_title" name="template_title" class="form-control" required>
            </div>

            <!-- Template Description -->
            <div class="form-group">
                <label for="template_description">Template Description:</label>
                <textarea id="template_description" name="template_description" class="form-control" rows="3" required></textarea>
            </div>

            <!-- Workout Entries -->
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

    <!-- JavaScript for dynamic workout entry fields -->
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
    </script>

   
</body>
</html>