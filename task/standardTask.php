<?php
ini_set('display_errors', 0);
error_reporting(E_ERROR | E_WARNING | E_PARSE); 
// Initialize the session
session_start();
require_once "../config.php";
$username = $_SESSION['username'];
$role = $_SESSION['role'];
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body{ font: 14px sans-serif; text-align: center; }
    </style>
</head>
<body>
    <h1 class="my-5">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to GARITS.</h1>
    <p>
        <a href="../logout.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>
        <a href="../<?php echo $role ?>.php" class="btn btn-info ml-3">Open Dashboard</a>
    <form action='' method='post'><!-- Form for creating a task-->
    <div class="form-group">
        <label for="inputTaskDescription">Task Description</label>
        <input type="text" class="form-control" required name="inputTaskDescription" placeholder="Task Description">
    </div>
    <button type="submit" name = "createAccount" class="btn btn-primary">Add task</button>
  <form>
  <form>
<?php
//check if form has been submitted
if (isset($_POST['createAccount'])) {
    $taskDescription = $_POST['inputTaskDescription'];//get task description
    
    $query = "INSERT INTO Task (description) VALUES (?)";//insert task in the db
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s',$taskDescription);
    /* Execute the statement */
    $stmt->execute();
    $row = $stmt->affected_rows;
    if ($row > 0) { 
        echo "<script type='text/javascript'>alert('Task Created');</script>";
    } else {
        "error";
    }
       
}