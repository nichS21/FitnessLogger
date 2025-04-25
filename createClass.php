<!DOCTYPE html>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("scriptsPHP/util.php");
neededImports();
?>

<html>
<head>
    <link rel="stylesheet" href="css/creation.css"> 
    <title>Create Workout Class</title>
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

            <label for="class_des">Class description:</label>
            <input type="text" id="class_des" name="description" class="form-control" required>

            <label for="imageSearch">Search a Free Class Image:</label>
            <input type="text" id="imageSearch" class="form-control mb-2" placeholder="e.g. yoga, HIIT, strength training">

            <div id="imageResults" class="d-flex flex-wrap gap-2 mb-3"></div>
            <div id="imagePreview"></div>

            <input type="hidden" name="class_img" id="selectedImage">

            <small class="text-muted">Pictures from <a href="https://unsplash.com" target="_blank">Unsplash</a></small>

            <label for="template">Choose a Template:</label>
            <select id="template" name="template[]" class="form-control">
                <?php
                $query = "SELECT tname FROM Workout_template";
                $res = $db->query($query);
                if ($res) {
                    $seenTemplates = [];
                    while ($row = $res->fetch()) {
                        $name = htmlspecialchars(trim($row['tname']));
                        if (in_array($name, $seenTemplates)) continue;
                        $seenTemplates[] = $name;
                        echo "<option value='$name'>$name</option>\n";
                    }
                } else {
                    debug("Database error loading templates.");
                }
                ?>
            </select>

            <button type="submit" class="btn btn-primary mt-3">Create Class</button>
        </form>
    </div>

    <?php 
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $uid         = $_SESSION['uid'] ?? 2;
        $className   = $_POST['name'];
        $classDes    = $_POST['description'];
        $classLength = $_POST['class_length'];
        $templateNames = $_POST['template'] ?? [];
        $imageUrl    = $_POST['class_img'] ?? '';

        // Insert course
        createClass($db, $uid, $className, $classDes, $classLength, $imageUrl);
        $newCourseID = $db->lastInsertId(); 
        $_SESSION['courseid'] = $newCourseID;
        $_SESSION['latest_course_id'] = $newCourseID;

        $tname = $_POST['template'] ?? null;
        if ($tname) {
            $stmt = $db->prepare("SELECT tid FROM Workout_template WHERE uid = ? AND courseID = ? AND tname = ?");
            $stmt->execute([$uid, $newCourseID, $tname]);
            $exists = $stmt->fetchColumn();

        if (!$exists) {
            $stmt = $db->prepare("INSERT INTO Workout_template (uid, courseID, tname) VALUES (?, ?, ?)");
            $stmt->execute([$uid, $newCourseID, $tname]);
        }
    }
    showToast("Class and templates successfully created!", "success");
}
?>
    <script>
    const accessKey = "53M4w0Qe-lhJwfLm2Wmc4D0j0sTSjGu0vGjrwDGp7zQ";

    document.getElementById('imageSearch').addEventListener('input', async (e) => {
        const query = e.target.value.trim();
        const container = document.getElementById('imageResults');
        if (!query) return container.innerHTML = "";

        container.innerHTML = "<p>Searching...</p>";

        const res = await fetch(`https://api.unsplash.com/search/photos?query=${encodeURIComponent(query)}&per_page=10&client_id=${accessKey}`);
        const data = await res.json();
        container.innerHTML = "";
        data.results.forEach(photo => {
            const img = document.createElement('img');
            img.src = photo.urls.thumb;
            img.alt = photo.alt_description;
            img.style.cursor = 'pointer';
            img.style.border = '2px solid transparent';
            img.style.borderRadius = '8px';
            img.onclick = async () => {
                // Highlight selected image
                document.querySelectorAll('#imageResults img').forEach(i => i.style.border = '2px solid transparent');
                img.style.border = '2px solid #2196f3';

                // Set the image URL in the hidden form field
                document.getElementById('selectedImage').value = photo.urls.regular;

                // Track the download (Unsplash requirement)
                const downloadURL = photo.links.download_location;
                const fullURL = downloadURL.includes('?')
                ? `${downloadURL}&client_id=${accessKey}`
                : `${downloadURL}?client_id=${accessKey}`;

            try {
                await fetch(fullURL); // async tracking event
                console.log("Download tracked successfully.");
            } catch (err) {
        console.error("Error tracking download:", err);
    }
};

            container.appendChild(img);
        });
    });
    </script>

</body>
</html>
