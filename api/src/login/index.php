<?php
$mysqli = new mysqli("db","root","password","logs");

// Check connection
if ($mysqli -> connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
  exit();
}
echo "Connected to db"
?>