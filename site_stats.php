<?php
session_start();
if (!isset($_SESSION['user_name']) || $_SESSION['is_admin'] !== 1) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "recipe");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Récupérer les statistiques
$recipe_count = $conn->query("SELECT COUNT(*) AS total FROM recipe")->fetch_assoc()['total'];
$user_count = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$ingredient_count = $conn->query("SELECT COUNT(*) AS total FROM ingredient")->fetch_assoc()['total'];
$level_count = $conn->query("SELECT COUNT(*) AS total FROM level")->fetch_assoc()['total'];
$type_count = $conn->query("SELECT COUNT(*) AS total FROM type")->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Site Stats</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f0f4f8;
      padding: 30px;
    }
    h1 {
      text-align: center;
      color: #6c5ce7;
      font-size: 36px;
      margin-bottom: 40px;
    }
    .stats-container {
      display: flex;
      justify-content: space-around;
      gap: 40px;
      margin-bottom: 40px;
    }
    .stat-card {
      background: white;
      border-radius: 20px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
      width: 20%;
      padding: 20px;
      text-align: center;
    }
    .stat-card h3 {
      font-size: 24px;
      margin-bottom: 10px;
    }
    .stat-card p {
      font-size: 28px;
      font-weight: bold;
      color: #6c5ce7;
    }
    .back-to-admin {
      display: block;
      width: fit-content;
      margin: 40px auto;
      padding: 15px 30px;
      background-color: #e74c3c;
      color: white;
      text-decoration: none;
      font-size: 18px;
      border-radius: 30px;
      text-align: center;
    }
    .back-to-admin:hover {
      background-color: #c0392b;
    }
  </style>
</head>
<body>

<h1>📊 Site Statistics</h1>

<div class="stats-container">
  <div class="stat-card">
    <h3>Total Recipes</h3>
    <p><?= $recipe_count ?></p>
  </div>
  <div class="stat-card">
    <h3>Total Users</h3>
    <p><?= $user_count ?></p>
  </div>
  <div class="stat-card">
    <h3>Total Ingredients</h3>
    <p><?= $ingredient_count ?></p>
  </div>
  <div class="stat-card">
    <h3>Total Levels</h3>
    <p><?= $level_count ?></p>
  </div>
  <div class="stat-card">
    <h3>Total Types</h3>
    <p><?= $type_count ?></p>
  </div>
</div>

<a href="main_admin.php" class="back-to-admin">🏠 Back to Admin Dashboard</a>

</body>
</html>
