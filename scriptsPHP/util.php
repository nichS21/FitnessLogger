<?php 
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once("scriptsPHP/dbConnect.php");

function debug($str) {
    print "<DIV class='debug'>$str</DIV>\n";
}

/*
* Function to include needed CSS/JS imports for any page on site
*/
function neededImports() 
{ ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="css/site.css" rel="stylesheet">
<?php
} // neededImports()

/*
* Generate navbar at top of page (intended to be used throughout site).
* NOTE: Not used on landing page to eliminate unnecessary 'Account' dropdown button
*/
function genNavBar()
{
?>      
    <div class="container-fluid site-color p-3 text-white"> 
        <div class="row">
            <div class="col-md-1">
                <img src="../images/amntLogo.png" height="50px" width="auto" class="mx-auto d-block" /> 
            </div>
            <div class="col-md-9">
                <p class="fs-1" style="display:inline">AMNT Fitness Logger</p>
            </div>
            <div class="col-md-2">
                <div class="dropdown">
                    <button class="acctBtn mx-auto d-block dropdown-toggle" type="button" id="accountDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Account
                    </button>
                    <ul class="dropdown-menu acctColor" aria-labelledby="accountDropdown">
                        <li><a class="dropdown-item acctColor" href="#">My Account</a></li>
                        <li><a class="dropdown-item acctColor" href="#">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>  
<?php
}   // genNavBar

// Add user function
function addUser($db, $age, $weight, $email, $height, $username, $password, $weeklyCalGoal) {
    if (!$db) {
        debug("Database connection failed.");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO User (age, weight, email, height, username, password, weeklyCalGoal) VALUES ($age, $weight, '$email', $height, '$username', '$hashedPassword', $weeklyCalGoal)";
    debug($query);  

    $res = $db->query($query);

    if ($res) {
        debug("User added successfully.");
    } else {
        debug("Error adding user: " . $db->errorInfo()[2]);
    }
}

function processLogin($db, $formData) {
    $username = $formData['username'];

    $query = "SELECT name FROM User WHERE username";

    $res = $db->query($query);

    if ($res == false || $res->rowCount() != 1) {
        header("refresh:2;url=dashboard.php");
        print "<P>Login as $uid failed</P>\n";    
    }
    else {
        header("refresh:2;url=dashboard.php");
        $row = $res->fetch();
        $_SESSION['username'] = $username;
        print "<P>Successfully logged in</P>\n";
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

function createTemplate($db, $uid, $courseID, $tname) {
    try {
        $sql = "INSERT INTO Workout_template 
                (uid, courseID, tname)
                VALUES (?, ?, ?)";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$uid, $courseID, $tname]);

        //print "<P>Template added successfully</P>\n";
    } catch (PDOException $e) {
        print "<P>Failed to add template: " . $e->getMessage() . "</P>\n";
    }
}

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
?>
