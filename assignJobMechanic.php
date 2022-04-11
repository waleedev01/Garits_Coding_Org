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
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<head>
<style>
        body{text-align: center; }
</style>
<body>
    <h1 class="my-5">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to GARITS.</h1>
    <p>
        <a href="logout.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>
        <a href="../<?php echo $role ?>.php" class="btn btn-info ml-3">Open Dashboard</a>
    <meta charset="UTF-8">

<?php
if (isset($_POST['update'])) {
    $pick_job_id = $_POST['update'];   
}
    $query = "SELECT * FROM Job where status!='completed' and job_type !='stock_order'";
    $resultJobs = $conn->query($query);

    $query = "SELECT * FROM Mechanic";
    $resultMec = $conn->query($query);
?>
<form action = '' method = 'post'>
  <div class="form-group">
    <label for="chooseJob">Choose Job</label>
    <select name="chooseJob"  class="form-control" required>
    <option selected required disabled>Choose...</option>
    <?php 
    while($row = $resultJobs->fetch_assoc()) {
      echo "<option value=$row[job_id]>$row[job_id] $row[status] $row[book_in_date] </option>";
    } 
    ?>
    </select>
  </div>
  <div class="form-group">
    <label for="chooseMec">Choose Mechanic</label>
    <select name="chooseMec"  class="form-control" required>
      <option selected disabled>Choose...</option>
    <?php 
    while($row = $resultMec->fetch_assoc()) {
      echo "<option value=$row[username]>$row[username]</option>";
    } 
    ?>
    </select>
  </div>
 
  <button type="submit" name='assignJob' class="btn btn-primary">Submit</button>
</form>
<?php
if (isset($_POST['assignJob'])) {
    $username = $_POST['chooseMec'];
    $job_id = $_POST['chooseJob'];

    $query = "UPDATE Job SET username='$username' where job_id = '$job_id'";
    $result= mysqli_query($conn, $query);
    $location="$role.php"; // If role is admin this will be admin.php, if student this will be student.php and more.
    echo "<script language='javascript'>
    alert('Job Assigned')
    window.location.href='../$location';
    </script>";
    echo "<meta http-equiv='refresh' content='0'>";

}