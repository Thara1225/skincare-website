<?php

// Example of dbconnection.php
$servername = "localhost";  // or your database server
$username = "root";  // your MySQL username
$password = "";  // your MySQL password
$dbname = "cinnamon_db";  // your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>