<?php

// Database connection
$servername = "localhost";

// local
$username = "root";
$password = "";
$dbname = "eduifer";

// prod
// $username = "u343706064_pAxmY";
// $password = "XMuyM9BC5p";
// $dbname = "u343706064_5D1ZI";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
