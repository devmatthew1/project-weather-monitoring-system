<?php
//including the database connection file
include("config.php");
include_once('esp-database.php');

$result = mysqli_query($mysqli, "SELECT * FROM timer ORDER BY id DESC");
while($res = mysqli_fetch_array($result)) { 
$date = $res['date'];
$h = $res['h'];
$m = $res['m'];
$s = $res['s'];
}
?>

