<?php 

//include database connection
include_once("scriptsPHP/dbConnect.php");

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
    <link href="../css/site.css" rel="stylesheet">
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
                    <li><a class="dropdown-item acctColor" href="#">My Account</a></li>
                    <li><a class="dropdown-item acctColor" href="#">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>  
<?php
}   // genNavBar

?>