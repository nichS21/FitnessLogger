 <?php
    session_start();
    include_once("scriptsPHP/dbConnect.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $courseID = $_POST['courseID'];
        $uid = $_POST['uid'];

        if (!empty($courseID) && !empty($uid)) {
            try {
                $stmt = $db->prepare("INSERT INTO Enrollment (uid, courseID) VALUES (?, ?)");
                $stmt->execute([$uid, $courseID]);

                if (isset($_SESSION['available_courses'])) {
                    $_SESSION['available_courses'] = array_filter($_SESSION['available_courses'], function ($course) use ($courseID) {
                        return $course['courseID'] != $courseID;
                    });
                }
                header("Location: showCourse.php");
                exit;
            } catch (PDOException $e) {
                echo "<script>
                alert('You might already be enrolled in this course.');
                window.location.href = 'showCourse.php';
            </script>";
            }
        } else {
            echo "<script>
            alert('Missing course or user ID');
            window.location.href = 'showCourse.php';
        </script>";
        }
    }
    ?>
