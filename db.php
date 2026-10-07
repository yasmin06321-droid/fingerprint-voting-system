<?php
$host = 'localhost';     // Database host (usually localhost)
$user = 'root';          // Database username
$password = '';          // Database password
$dbname = 'voting_db'; // Your database name

// Create connection
$conn = mysqli_connect($host, $user, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Optional: echo success message
// echo "Connected successfully";
?>
