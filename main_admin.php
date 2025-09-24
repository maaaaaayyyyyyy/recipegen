<?php
session_start();

// Redirection si l'utilisateur n'est pas connecté ou pas admin
if (!isset($_SESSION['user_name']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: main.php");
    exit();
}

$user_name = $_SESSION['user_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>🔧 Admin Dashboard - Recipe Matcher</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f0f4f8;
      margin: 0;
      padding: 20px;
    }

    h1 {
      text-align: center;
      color: #6c5ce7;
      margin-top: 20px;
    }

    .profile-pic {
      width: 80px;
      height: 80px;
      background: #6c5ce7;
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 20px auto 10px;
      font-size: 36px;
      font-weight: bold;
    }

    .welcome-text {
      text-align: center;
      color: #444;
      font-size: 18px;
      margin-bottom: 40px;
    }

    .admin-menu {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
      max-width: 1000px;
      margin: 0 auto;
    }

    .card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      padding: 30px 20px;
      text-align: center;
      width: 300px;
      transition: transform 0.3s ease;
      cursor: pointer;
    }

    .card:hover {
      transform: translateY(-8px);
    }

    .card h2 {
      color: #333;
      margin-bottom: 15px;
    }

    .card p {
      color: #777;
      margin-bottom: 20px;
    }

    .card button {
      background-color: #6c5ce7;
      border: none;
      padding: 10px 20px;
      border-radius: 25px;
      color: white;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .card button:hover {
      background-color: #5a4bd3;
    }

    .logout-link {
      display: block;
      margin: 40px auto 0;
      text-align: center;
      background: #ccc;
      color: #333;
      text-decoration: none;
      padding: 10px 20px;
      border-radius: 20px;
      max-width: 200px;
    }

    .logout-link:hover {
      background: #bbb;
    }
  </style>
</head>
<body>

<div class="profile-pic">
  <?php echo strtoupper(substr($user_name, 0, 1)); ?>
</div>
<h1>Admin Dashboard - Welcome <?php echo htmlspecialchars($user_name); ?> 🔧</h1>
<p class="welcome-text">You are logged in as an administrator. What would you like to manage?</p>

<div class="admin-menu">
  <div class="card" onclick="location.href='manage_users.php'">
    <h2>👥 Manage Users</h2>
    <p>View, edit, or delete user accounts.</p>
    <button>Manage Users</button>
  </div>
  <div class="card" onclick="location.href='manage_recipes.php'">
    <h2>📋 Manage Recipes</h2>
    <p>Add, update, or remove recipes.</p>
    <button>Manage Recipes</button>
  </div>
  <div class="card" onclick="location.href='site_stats.php'">
    <h2>📊 Site Statistics</h2>
    <p>View usage statistics and data.</p>
    <button>View Stats</button>
  </div>
</div>

<a href="logout.php" class="logout-link">Logout</a>

</body>
</html>
