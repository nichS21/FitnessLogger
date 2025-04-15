<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection
include("scriptsPHP/util.php");
neededImports();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/creation.css"> 
    <title>Create Workout Class</title>
</head>

<body class="site-font">
    <?php genNavBar(); ?>

    <div class="form-container">
    <h2>New Workout Course </h2>
    <form name = createCourse method="POST" action="" enctype="multipart/form-data" class="form-grid">

        <label for="class_name">Class Name:</label>
        <input type="text" id="class_name" name="name" class="form-control" required>

        <label for="class_length">Class Duration (weeks):</label>
        <input type="number" id="class_length" name="class_length" class="form-control" min = "1" max = "52">

        <label for="class_goal">Class description:</label>
        <input type="text" id="class_des" name="description" class="form-control" required>

        <label for="imageSearch">Search a Free Class Image:</label>
        <input type="text" id="imageSearch" class="form-control mb-2" placeholder="e.g. yoga, HIIT, strength training">

        <!-- Where the image results will show -->
        <div id="imageResults" class="d-flex flex-wrap gap-2 mb-3"></div>

        <!-- Hidden input to store selected image URL -->
        <input type="hidden" name="class_img" id="selectedImage">

        <small class="text-muted">Pictures from <a href="https://unsplash.com" target="_blank">Unsplash</a></small>
        <br>
        <label for="template">Choose a Template:</label>
        <select id="template" name="template" class="form-control">
            <option value="">-- Select Template --</option>
        </select>

        <button type="submit" class="btn btn-primary">Create Class</button>
    </form>
    </div>

    <?php 
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get current instructor ID from session (adjust as needed)
    $instructorID = $_SESSION['uid'] ?? 2;

    // Collect form data safely
    $uid         = $instructorID; 
    $className   = $_POST['name'];
    $classDes    = $_POST['description'];
    
    // Call function to insert into database
    createClass($db, $uid, $className, $classDes);
    }
    ?>

    <script>
    const accessKey = "53M4w0Qe-lhJwfLm2Wmc4D0j0sTSjGu0vGjrwDGp7zQ"; 

    document.getElementById('imageSearch').addEventListener('input', async (e) => {
        const query = e.target.value.trim();
        const container = document.getElementById('imageResults');
        if (!query) return container.innerHTML = "";

        container.innerHTML = "<p>Searching...</p>";

        const res = await fetch(`https://api.unsplash.com/search/photos?query=${encodeURIComponent(query)}&per_page=6&client_id=${accessKey}`);
        const data = await res.json();

        container.innerHTML = "";
        data.results.forEach(photo => {
            const img = document.createElement('img');
            img.src = photo.urls.thumb;
            img.alt = photo.alt_description;
            img.style.cursor = 'pointer';
            img.style.border = '2px solid transparent';
            img.style.borderRadius = '8px';
            img.onclick = () => {
                const selectedUrl = photo.urls.regular;

                // Set hidden input value
                document.getElementById('selectedImage').value = selectedUrl;

                // Highlight selected image
                document.querySelectorAll('#imageResults img').forEach(i => i.style.border = '2px solid transparent');
                img.style.border = '2px solid #2196f3';

                // Show preview immediately
                document.getElementById('imagePreview').innerHTML = `
                <div class="mt-3">
                <strong>Selected Image Preview:</strong><br>
                <img src="${selectedUrl}" style="max-width: 100%; height: auto; border-radius: 8px; margin-top: 5px;">
                </div>
                `;
            };      


            container.appendChild(img);
        }); 
    });
</script>

</body>
</html>

<?php

?>