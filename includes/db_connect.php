<?php
// Database credentials — update these as needed
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'open2learn';

// Create mysqli connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}
?>