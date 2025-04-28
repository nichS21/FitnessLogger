<?php 

include_once("util.php");

function createClass($db, $uid, $className, $classDes, $classLength, $imageUrl) {
    try {
        $sql = "INSERT INTO Course 
                (uid, name, description, class_length, unsplash_url)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $db->prepare($sql);
        $stmt->execute([$uid, $className, $classDes, $classLength, $imageUrl]);

        showToast("<strong>Course created successfully!</strong>", "success");
    } catch (PDOException $e) {
        showToast("Failed to create course", "error"); // . $e->getMessage();
    }
}

function showToast($message, $type = 'success') {
    echo '
    <div id="toast" class="toast ' . $type . '">' . $message . '</div>
    <script>
        setTimeout(function() {
            const toast = document.getElementById("toast");
            toast.classList.add("show");
            setTimeout(() => toast.classList.remove("show"), 3000);
        }, 1000);
    </script>
    ';
}

?>
