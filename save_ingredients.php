<?php
session_start();
$conn = new mysqli("localhost", "root", "", "recipe");
if ($conn->connect_error) die("Erreur: " . $conn->connect_error);

$user = $_SESSION["user_name"] ?? "guest";
$data = json_decode(file_get_contents("php://input"), true);
$ingredients = $data["ingredients"] ?? [];

// Supprimer anciens ingrédients
$stmtDel = $conn->prepare("DELETE FROM user_ingredient WHERE user_name = ?");
$stmtDel->bind_param("s", $user);
$stmtDel->execute();
$stmtDel->close();

// Insérer les nouveaux
$stmt = $conn->prepare("INSERT INTO user_ingredient (user_name, ing_name) VALUES (?, ?)");
foreach ($ingredients as $ing) {
  $cleanIng = trim(preg_replace('/[^\p{L}\p{N} ]+/u', '', $ing));
  $stmt->bind_param("ss", $user, $cleanIng);
  $stmt->execute();
}
$stmt->close();
echo "OK";
