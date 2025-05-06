<?php 
    include_once("scriptsPHP/util.php");
    neededImports();
    genNavBar();

    echo '<div class="col-md-12 p-4">
    <a class="gnrlBtn" href="dashboard.php">To Dashboard</a>
    </div>';

    // Function to create a new class
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

    // Add template ID to the course
    function updateTemp(PDO $db, int $uid, int $courseID, string $tname): void {
        $stmt = $db->prepare(
        'UPDATE Workout_template
            SET courseID = ?
          WHERE uid      = ?
            AND tname    = ?
            AND courseID IS NULL'
        );
    
        $stmt->execute([$courseID, $uid, $tname]);

        if ($stmt->rowCount() === 0) {
            $stmt = $db->prepare(
                'INSERT INTO Workout_template (uid, courseID, tname)
                VALUES (?, ?, ?)'
            );
        
            $stmt->execute([$uid, $courseID, $tname]);
        }
    }

    // Fetch all available templates coaches can choose from for a new class
    function fetchTemps(PDO $db, int $uid): array {
        $stmt = $db->prepare(
            'SELECT DISTINCT wt.tname
            FROM Workout_template wt
            WHERE wt.uid = ?
            AND NOT EXISTS (
                SELECT 1
                    FROM Workout_template wt2
                    WHERE wt2.uid   = wt.uid
                    AND wt2.tname = wt.tname
                    AND wt2.courseID IS NOT NULL
                )
                ORDER BY wt.tname ASC'
            );
        $stmt->execute([$uid]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete feedback log entry
    function deleteLog(PDO $db, int $lid, int $coachUid): void {
        $stmt = $db->prepare(
            'DELETE l FROM Log l
            JOIN Workout_template wt ON wt.tid = l.tid
            JOIN Course c ON c.courseID = wt.courseID
            WHERE l.lid = :lid AND c.uid = :coach'
        );

        $stmt->execute(['lid' => $lid,'coach' => $coachUid]);
    }

    // Update feedback log entry
    function updateLog(PDO $db, int $lid, int $coachUid, string $feedback): void {
        $stmt = $db->prepare(
            'UPDATE Log l
            JOIN Workout_template wt ON wt.tid = l.tid
            JOIN Course c ON c.courseID = wt.courseID
            SET l.feedback = :fb
            WHERE l.lid = :lid AND c.uid = :coach'
        );

        $stmt->execute(['fb' => $feedback,'lid' => $lid,'coach' => $coachUid]);
    }

    // Get all the courses a coach is teaching
    function getCoachCourses(PDO $db, int $coachUid): array {
        $stmt = $db->prepare('SELECT courseID, name FROM Course WHERE uid = ? ORDER BY name');
        $stmt->execute([$coachUid]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    // Get all students enrolled in the coach's courses
    function getUsers(PDO $db, int $coachUid): array {
        $stmt = $db->prepare(
            'SELECT DISTINCT u.uid, u.username
            FROM Enrollment e
            JOIN Course c ON c.courseID = e.courseID
            JOIN User u ON u.uid = e.uid
            WHERE c.uid = ?
            ORDER BY u.username'
        );
        
        $stmt->execute([$coachUid]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);   
    }
    
?>
