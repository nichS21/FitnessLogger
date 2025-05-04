<?php 
include_once("scriptsPHP/util.php");
neededImports();
genNavBar();

// echo '
// <a href="dashboard.php" class="back-button">Back to Dashboard</a>
// <style>
// .back-button {
//     display: inline-block;
//     padding: 8px 16px;
//     margin: 20px;
//     font-size: 14px;
//     font-weight: 500;
//     background-color: #ccc;
//     color: #000;
//     text-decoration: none;
//     border-radius: 4px;
//     border: 1px solid #aaa;
//     transition: background-color 0.2s;
// }
// .back-button:hover {
//     background-color: #bbb;
// }
// </style>
// ';

function createClass($db, $uid, $className, $classDes, $classLength, $imageUrl) {
    try {
        $sql = "INSERT INTO Course 
                (uid, name, description, class_length, unsplash_url)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $db->prepare($sql);
        $stmt->execute([$uid, $className, $classDes, $classLength, $imageUrl]);

        $_SESSION['toastClass'] = ['message' => 'New template created and exercises added!', 'type' => 'success'];

        showToast("<strong>Course created successfully!</strong>", "success");
    } catch (PDOException $e) {
        showToast("Failed to create course", "error"); // . $e->getMessage();
    }
}

?>
