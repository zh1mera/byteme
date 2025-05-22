<?php
$host = 'sqlXXX.infinityfree.com';
$db   = 'epiz_XXXXXXX_byteme';
$user = 'epiz_XXXXXXX';
$pass = 'your_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
