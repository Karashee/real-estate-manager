<?php
$host = 'localhost';
$db   = 'real_estate_db';
$user = 'postgres';
$pass = 'Cbk46800'; // change this
$dsn = "pgsql:host=$host;port=5432;dbname=$db;";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
