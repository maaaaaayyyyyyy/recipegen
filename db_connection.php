<?php
$host = 'localhost';
$dbname = 'recipe'; // nom de ta base
$username = 'root';     // par défaut dans XAMPP
$password = '';         // souvent vide dans XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Options de PDO : meilleures erreurs + sécurité
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
