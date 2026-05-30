<?php
$conn = new mysqli('localhost', 'root', '', 'paypilot');
if ($conn->connect_error) { 
    die('Connection failed: ' . $conn->connect_error); 
}
?>