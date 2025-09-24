<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}
$user_name = $_SESSION['user_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>🍽️ Recipe Matcher - Home</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #fef9f4;
      margin: 0;
      padding: 20px;
    }

    h1 {
      text-align: center;
      color: #d4a373;
      margin-top: 20px;
    }

    .welcome-text {
      text-align: center;
      color: #555;
      font-size: 18px;
      margin-bottom: 40px;
    }

    .profile-pic {
      width: 80px;
      height: 80px;
      background: #d4a373;
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 20px auto 10px;
      font-size: 36px;
      font-weight: bold;
    }

    .menu {
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
      background-color: #d4a373;
      border: none;
      padding: 10px 20px;
      border-radius: 25px;
      color: white;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .card button:hover {
      background-color: #bc8a5f;
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
<h1>Welcome, <?php echo htmlspecialchars($user_name); ?>! 🍽️</h1>
<p class="welcome-text">What would you like to cook today?</p>

<div class="menu">
  <div class="card" onclick="location.href='ingredients.php'">
    <h2>🧺 My Ingredients</h2>
    <p>Manage your pantry and see what you can cook.</p>
    <button>Manage Ingredients</button>
  </div>
  <div class="card" onclick="location.href='recipes.php'">
    <h2>🍳 Find Recipes</h2>
    <p>Discover recipes based on your ingredients.</p>
    <button>Find Recipes</button>
  </div>
  <div class="card" onclick="location.href='favorites.php'">
    <h2>❤️ My Favorites</h2>
    <p>View your saved favorite dishes.</p>
    <button>View Favorites</button>
  </div>
</div>

<a href="logout.php" class="logout-link">Logout</a>

</body>
</html>
