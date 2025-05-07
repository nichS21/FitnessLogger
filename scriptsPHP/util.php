<?php 

// session_start();
include_once("dbConnect.php");

function debug($str) {
    print "<DIV class='debug'>$str</DIV>\n";
}

/*
* Function to include needed CSS/JS imports for any page on site
* Made by Nick
*/
function neededImports() 
{ ?>
     <!-- Required meta tags for Bootstrap -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" >
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="css/site.css" rel="stylesheet">
<?php
} // neededImports()

/*
* Generate navbar at top of page (intended to be used throughout site).
* NOTE: Not used on landing page to eliminate unnecessary 'Account' dropdown button
* Made by Nick
*/
function genNavBar()
{
?>
        
<div class="container-fluid site-color p-3 text-white"> 
    <div class="row">
        <div class="col-md-1">
            <img src="images/amntLogo.png" height="50px" width="auto" class="mx-auto d-block" /> 
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
                    <li><a class="dropdown-item acctColor" href="account.php">My Account</a></li>
                    <li><a class="dropdown-item acctColor" href="analytics.php">My Analytics</a></li>
                    <li><a class="dropdown-item acctColor" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>  
<?php
}

// Add user function
function addUser($db, $birthday, $weight, $email, $height, $username, $password, $weeklyCalGoal) {
    if (!$db) {
        debug("Database connection failed.");
        return;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Correctly enclose $birthday, $email, $username, and $hashedPassword in quotes
    $query = "INSERT INTO User (birthDay, weight, email, height, username, password, weeklyCalGoal)
              VALUES ('$birthday', $weight, '$email', $height, '$username', '$hashedPassword', $weeklyCalGoal)";
    debug($query);

    $res = $db->query($query);

    if ($res) {
        debug("User added successfully.");
    } else {
        debug("Error adding user: " . $db->errorInfo()[2]);
    }
}

function processLogin($db, $username, $password) {
    session_unset();

    if (!$db) {
        debug("Database connection failed.");
    }
    $query = "SELECT uid, username, password FROM User WHERE username = '$username'";
    $res = $db->query($query);
    if ($res == false || $res->rowCount() != 1) {
        header("refresh:2;url=index.php");
        print "<p>Login as $username failed</p>\n";    
    } 
    else {
        $row = $res->fetch();
        $uid = $row['uid']; 
        $hashedPassword = $row['password'];

        if (password_verify($password, $hashedPassword)) {
            session_regenerate_id(true);
            $_SESSION['username'] = $username;
            $_SESSION['uid'] = $uid;
            header("refresh:2;url=dashboard.php");
        } else {
            header("refresh:2;url=index.php");   
        }
    }
}

function showDetails($db, $uid) {
    $stmt = $db->prepare("SELECT birthDay, weight, email, height, username, weeklyCalGoal FROM User WHERE uid = ?");
    $stmt->execute([$uid]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function logout() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    session_unset();
    session_destroy();
    header("Location:index.php");
    exit();
}

function updateAccount($db, $uid, $weight, $height, $weeklyCalGoal) {
    if (!$db) {
        debug("Database connection failed.");
        return;
    }

    $query = "UPDATE User SET weight = ?, height = ?, weeklyCalGoal = ? WHERE uid = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$weight, $height, $weeklyCalGoal, $uid]);

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
