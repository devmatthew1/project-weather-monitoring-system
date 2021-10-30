<?php
include_once('esp-database.php');

if(isset($_POST['update']))
{ 

$datebf=  $_POST['date'];
$date = date('Y-m-d', strtotime("+$datebf days"));

$h= $_POST['h'];
$m= $_POST['m'];
$s= $_POST['s'];
    //updating the table
$result = mysqli_query($mysqli, "UPDATE timer SET date='$date' WHERE id=1");
$result = mysqli_query($mysqli, "UPDATE timer SET h='$h' WHERE id=1");
$result = mysqli_query($mysqli, "UPDATE timer SET m='$m' WHERE id=1");
$result = mysqli_query($mysqli, "UPDATE timer SET s='$s' WHERE id=1");  
//redirectig to the display page. In our case, 
echo "Timer updated";
}
?>