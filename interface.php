<?php
if (!isset($_GET['id'])) {
    echo "Recipe not found.";
    exit();
}

$recipe = urldecode($_GET['id']);
$conn = new mysqli("localhost", "root", "", "recipe");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$recipeData = $conn->query("SELECT * FROM recipe WHERE recipe_name = '$recipe'")->fetch_assoc();
$ingredients = $conn->query("SELECT ing_name FROM recipe_ingredient WHERE recipe_name = '$recipe'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($recipe); ?> - Details</title>
  <style>
    body { font-family: 'Poppins', sans-serif; background: #fff9f4; padding: 30px; }
    h1 { color: #d4a373; text-align: center; }
    .section { margin-top: 40px; max-width: 800px; margin-left: auto; margin-right: auto; }
    .section h2 { color: #bc8a5f; }
    .ingredients, .instructions {
      background: #fff5e1; padding: 20px; border-radius: 20px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    ul { padding-left: 20px; }
    li { margin-bottom: 10px; }
    .back-button, .home-button {
      display: block; margin: 40px auto; text-align: center;
      background-color: #d4a373; color: white; text-decoration: none;
      padding: 15px 25px; border-radius: 30px; font-size: 1.1em;
    }
    .home-button {
      background-color: #b97a45; /* Change the color for the home button */
    }
  </style>
</head>
<body>

<h1><?php echo htmlspecialchars($recipe); ?></h1>

<div class="section">
  <h2>🛒 Ingredients</h2>
  <div class="ingredients">
    <ul>
      <?php while ($row = $ingredients->fetch_assoc()): ?>
        <li><?php echo htmlspecialchars($row['ing_name']); ?></li>
      <?php endwhile; ?>
    </ul>
  </div>
</div>

<div class="section">
  <h2>👩‍🍳 Instructions</h2>
  <div class="instructions">
    <?php echo nl2br(htmlspecialchars($recipeData['recipe_details'])); ?>
  </div>
</div>

<a href="recipes.php" class="back-button">← Back to Recipes</a>
<a href="main.php" class="home-button">📋 Home</a>

</body>
</html>
