<?php
session_start();
ini_set('display_errors', $_SESSION['uid']);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once("scriptsPHP/dbConnect.php");
include_once("scriptsPHP/util.php");


logout();
?>