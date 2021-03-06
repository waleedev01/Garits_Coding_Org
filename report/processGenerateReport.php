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
$today = date("Y-m-d");//get today date
//if job report has been asked
if (isset($_GET['CreateJobs'])) {
    $month = $_GET['month'];//get the range
    $year = $_GET['year'];
    //if the user wants a report with the date range, month and year should not be null
    if(isset($_GET['month']) && isset($_GET['year'])){
        $query = "SELECT job_id
        FROM Job
        WHERE username is not null and SUBSTRING(book_in_date,6, 2) = $month AND SUBSTRING(book_in_date,1, 4) = $year";
        $result = mysqli_query($conn, $query);
        $total_jobs_in_month = mysqli_num_rows($result);
        //select all jobs in the data range  
        $query = "SELECT job_type, COUNT(*) AS 'count'
        FROM Job WHERE username is not null and SUBSTRING(book_in_date,6, 2) = $month AND SUBSTRING(book_in_date,1, 4) = $year
        GROUP BY job_type;";
        $result = mysqli_query($conn, $query);
        $i = 0;
        //count jobs for each job_type
        while ($row = mysqli_fetch_array($result)){
            if($row['job_type']=='MoT')
            $MoT_count_month = $row['count'];
            if($row['job_type']=='annual service')
            $annual_service_count_month = $row['count'];
            if($row['job_type']=='repair')
            $repair_count_month = $row['count'];
            $i++;
        }  
    
        //query to get total account holder customers    
        $query = "SELECT *
        FROM Job j, AccountHolder a
        WHERE where job_type!='stock_order' and SUBSTRING(book_in_date,6, 2) = $month AND SUBSTRING(book_in_date,1, 4) = $year and j.customer_id IN(SELECT a.customer_id FROM AccountHolder);";
        $result = mysqli_query($conn, $query);
        $total_account_customers_month = mysqli_num_rows($result);
        
        //query to get total normal customers    
        $query = "SELECT *
        FROM Job where job_type!='stock_order' and SUBSTRING(book_in_date,6, 2) = $month AND SUBSTRING(book_in_date,1, 4) = $year";
        $result = mysqli_query($conn, $query);
        $total_normal_customers_month = mysqli_num_rows($result)-$total_account_customers_month;        
        
    }
    //count jobs for each job_type
    $query = "SELECT job_type, COUNT(*) AS 'count'
    FROM Job where job_type!='stock_order'
    GROUP BY job_type;";
    $result = mysqli_query($conn, $query);
    $i = 0;
    while ($row = mysqli_fetch_array($result)){
        if($row['job_type']=='MoT')
            $MoT_count = $row['count'];
        if($row['job_type']=='annual service')
            $annual_service_count = $row['count'];
        if($row['job_type']=='repair')
            $repair_count = $row['count'];
        $i++;
    }  
    
    //query to get total account holder customers    
    $query = "SELECT *
    FROM Job j, AccountHolder a
    where job_type!='stock_order' and j.customer_id IN(SELECT a.customer_id FROM AccountHolder);";
    $result = mysqli_query($conn, $query);
    $total_account_customers = mysqli_num_rows($result);
    
    //query to get total normal customers    
    $query = "SELECT *
    FROM Job where job_type!='stock_order'";
    $result = mysqli_query($conn, $query);
    $total_normal_customers = mysqli_num_rows($result)-$total_account_customers;

    //get total
    $query = "SELECT * FROM Job where job_type!='stock_order'";
    $result = mysqli_query($conn, $query);
    $total_jobs = mysqli_num_rows($result);
    
    //get average of the jobs
    $query = "SELECT AVG(time_spent) 'average' FROM Job where job_type!='stock_order'";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_array($result)){
        $average_time = $row['average'];
    }  
 
    //get average of the amount of jobs
    $query = "SELECT AVG(amount) 'average' FROM Invoice;";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_array($result)){
        $average_price = $row['average'];
    }  

    //get average time for each job  type  
    $query = "SELECT job_type, AVG(time_spent) AS 'average'
    FROM Job where job_type!='stock_order'
    GROUP BY job_type;";
        $result = mysqli_query($conn, $query);
        $i = 0;
    while ($row = mysqli_fetch_array($result)){
        if($row['job_type']=='MoT')
            $MoT_average_time_spent = $row['average'];
        if($row['job_type']=='annual service')
            $annual_service_average_time_spent = $row['average'];
        if($row['job_type']=='repair')
            $repair_average_time_spent = $row['average'];
        $i++;
    }  

    //get average amount for each job    
    $query = "SELECT job_type, AVG(amount) AS 'average'
    FROM Job j,Invoice i where j.job_id = i.job_id and job_type!='stock_order'
    GROUP BY job_type;";
    $result = mysqli_query($conn, $query);
    $i = 0;
    while ($row = mysqli_fetch_array($result)){
        if($row['job_type']=='MoT')
            $MoT_average_amount = $row['average'];
        if($row['job_type']=='annual service')
            $annual_service_average_amount = $row['average'];
        if($row['job_type']=='repair')
            $repair_average_amount = $row['average'];
        $i++;
    }  

    //get average time, amount foe each job
    $query = "SELECT username, AVG(time_spent) AS 'average_time',  AVG(amount) AS 'average_amount'
    FROM Job j, Invoice i where j.job_id = i.job_id and job_type!='stock_order' and username is not null
    GROUP BY username;";
    $result_mechanic_query = mysqli_query($conn, $query);
    $i = 0;

    


    


}
?>

<!DOCTYPE html>
<html lang="en">
<script type="text/javascript" src="print.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css'>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<script>
  window.onload = fnExcelReport2() {
    startcountdown();
  };
</script>
<head>
<style>
        body{text-align: center; }
</style>
<body>
    <h1 class="my-5">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to GARITS.</h1>
    <p>
        <a href="../logout.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>
        <a href="../<?php echo $role ?>.php" class="btn btn-info ml-3">Open Dashboard</a>
        <?php
            echo "<h3 class='my-5'>Jobs Report</h1>";
            echo "<div class='container'>";
            echo "<div class='row-fluid'>";
                echo "<div class='col-xs-12'>";
                echo "<div class='table-responsive'>";    
                    echo "<table id='jobsOverallReport' cellpadding='0' cellspacing='0' class='table table-hover table-inverse'>";
                    echo "<tr>";
                    echo "<th>Total jobs</th>";
                    echo "<th>MoT</th>";
                    echo "<th>Repair</th>";
                    echo "<th>Annual service</th>";
                    echo "<th>Account holder</th>";
                    echo "<th>Normal Customer</th>";
                    echo "</tr>";
                    
                    echo "<tr>";
                    echo "<td>$total_jobs</td>";
                    echo "<td>$MoT_count</td>";
                    echo "<td>$repair_count</td>";
                    echo "<td>$annual_service_count</td>";
                    echo "<td>$total_account_customers</td>";
                    echo "<td>$total_normal_customers</td>";
                    echo "</tr>";

                    

                    echo "</table>";
                    echo"
                    <button onclick=fnExcelReport()>
                       <span class='glyphicon glyphicon-download'></span>
                       Download Report
                    </button>";
                echo "</div>";
                echo "</div>";
        
        if(isset($_GET['month']) && isset($_GET['year'])    && isset($total_jobs_in_month)){
            echo "<br><h3 class='my-5'>$month / $year Jobs Report</h1>";
            echo "<div class='container'>";
            echo "<div class='row-fluid'>";
                echo "<div class='col-xs-12'>";
                echo "<div class='table-responsive'>";    
                    echo "<table id='jobsMonthlyReport' cellpadding='0' cellspacing='0' class='table table-hover table-inverse'>";
                    echo "<tr>";
                    echo "<th>Total jobs in $month / $year</th>";
                    echo "<th>MoT</th>";
                    echo "<th>Repair</th>";
                    echo "<th>Annual service</th>";
                    echo "<th>Account holder</th>";
                    echo "<th>Normal Customer</th>";
                    echo "</tr>";
                    
                    echo "<tr>";
                    echo "<td>$total_jobs_in_month</td>";
                    echo "<td>$MoT_count_month</td>";
                    echo "<td>$repair_count_month</td>";
                    echo "<td>$annual_service_count_month</td>";
                    echo "<td>$total_account_customers_month</td>";
                    echo "<td>$total_normal_customers_month</td>";
                    echo "</tr>";
                    echo "</table>";
                    echo"
                    <button onclick=fnExcelReport2()>
                       <span class='glyphicon glyphicon-download'></span>
                       Download Report
                    </button>";
                echo "</div>";
                echo "</div>";
        }
        echo "<br><h3 class='my-5'>Jobs Report Average</h1>";
            echo "<div class='container'>";
            echo "<div class='row-fluid'>";
                echo "<div class='col-xs-12'>";
                echo "<div class='table-responsive'>";    
                    echo "<table id='jobsAverageReport' cellpadding='0' cellspacing='0' class='table table-hover table-inverse'>";
                    echo "<tr>";
                    echo "<th>Average time</th>";
                    echo "<th>Average price</th>";
                    echo "</tr>";
                    
                    echo "<tr>";
                    echo "<td>$average_time</td>";
                    echo "<td>$average_price</td>";
                    echo "</tr>";
                    echo "</table>";
                    echo"
                    <button onclick=fnExcelReport3()>
                       <span class='glyphicon glyphicon-download'></span>
                       Download Report
                    </button>";
                echo "</div>";
                echo "</div>";
        
                echo "<br><h3 class='my-5'>Jobs Report Average by Job Type</h1>";
            echo "<div class='container'>";
            echo "<div class='row-fluid'>";
                echo "<div class='col-xs-12'>";
                echo "<div class='table-responsive'>";    
                    echo "<table id='jobsTypeAverageReport' cellpadding='0' cellspacing='0' class='table table-hover table-inverse'>";
                    echo "<tr>";
                    echo "<th>Average time MoT</th>";
                    echo "<th>Average price MoT</th>";
                    echo "<th>Average time repair</th>";
                    echo "<th>Average price repair</th>";
                    echo "<th>Average time annual service</th>";
                    echo "<th>Average price annual service</th>";
                    echo "</tr>";
                    
                    echo "<tr>";
                    echo "<td>$MoT_average_time_spent</td>";
                    echo "<td>$MoT_average_amount</td>";
                    echo "<td>$annual_service_average_time_spent</td>";
                    echo "<td>$annual_service_average_amount</td>";
                    echo "<td>$repair_average_time_spent</td>";
                    echo "<td>$repair_average_amount</td>";

                    echo "</tr>";
                    echo "</table>";
                    echo"
                    <button onclick=fnExcelReport4()>
                       <span class='glyphicon glyphicon-download'></span>
                       Download Report
                    </button>";
                echo "</div>";
                echo "</div>";
        
                
                echo "<br><h3 class='my-5'>Jobs Report Average by Mechanic</h1>";
                echo "<div class='container'>";
                echo "<div class='row-fluid'>";
                    echo "<div class='col-xs-12'>";
                    echo "<div class='table-responsive'>";    
                        echo "<table id='mechanicJobsAverageReport' cellpadding='0' cellspacing='0' class='table table-hover table-inverse'>";
                        echo "<tr>";
                        echo "<th>Username</th>";
                        echo "<th>Average Time spent</th>";
                        echo "<th>Average Amount</th>";
                        echo "</tr>";
                        if ($result_mechanic_query->num_rows > 0) {
                            // output data of each row
                            while($row = $result_mechanic_query ->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["username"] . "</td>";
                                echo "<td>" . $row["average_time"] . "</td>";
                                echo "<td>" . $row["average_amount"] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "0 results";
                        }
                        
                        echo "</table><button onclick=fnExcelReport5()>
                        <span class='glyphicon glyphicon-download'></span>
                        Download Report
                     </button>";
            
                    echo "</div>";
                    echo "</div>";

                