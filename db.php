<?php
$host = "localhost";
$username = "root";      
$password = ""; 
$database = "news_system";

try {
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES utf8");//هذا السطر مهم لضمان عرض النصوص العربية بشكل صحيح
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>