<?php
// Initialize the session
session_start();
require_once "config.php";
$username = $_SESSION['username'];
$role = $_SESSION['role'];
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}


//$itemID = $conn->real_escape_string($_POST["InputID"]);
$partName = $conn->real_escape_string($_POST["PartName"]);
$quantity =  $conn->real_escape_string($_POST["quantity"]);
$yearS =  $conn->real_escape_string($_POST["year"]);
$price =  $conn->real_escape_string($_POST["price"]);
$manufacturerName =  $conn->real_escape_string($_POST["manufacturerName"]);
$vehicle_type =  $conn->real_escape_string($_POST["vehicleType"]);
$threshold_level =  $conn->real_escape_string($_POST["thresholdLevel"]);
if($threshold_level==null)  
    $threshold_level=10;

// changed variable name of year in the database to year_made(change in orignal code to work)
// Check to see that no fields are left empty
 if(!empty($partName) || !empty($quantity) || !empty($yearS) || !empty($price) || !empty($manufacturerName) || !empty($vehicle_type) || !empty($threshold_level) ){
    $insertQuery = "INSERT INTO Stock(part_name,quantity,year,price,manufacturer_name,vehicle_type,threshold_level) Values ('$partName','$quantity','$yearS','$price','$manufacturerName','$vehicle_type','$threshold_level')";
    mysqli_query($conn,$insertQuery);
    $location="$role.php"; // If role is admin this will be admin.php, if student this will be student.php and more.
    echo "<script language='javascript'>
    alert('Stock item Created')
    window.location.href='$location';
    </script>";
    echo "<meta http-equiv='refresh' content='0'>";

} 

?>