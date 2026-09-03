<?php

$username = "root";
$password ="";
$database="kissan_seva";
$server = "localhost";

$con = mysqli_connect($server, $username, $password, $database);

if(!$con){
    die("Error: " . mysqli_connect_error());
}

?>