<?php
// Start the session
session_start();

// Database connection
$servername = "localhost"; 
$username = "your_username"; 
$password = "your_password"; 
$dbname = "your_database"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Check if recipe name is set in the URL
if (isset($_GET['name']) && !empty($_GET['name'])) {
    $recipe_name = $_GET['name'];

    // Fetch recipe details from the database
    $sql = "SELECT r.recipe_name, r.time, r.recipe_details, r.level_name, r.type_name
            FROM recipe r
            WHERE r.recipe_name = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $recipe_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $recipe = $result->fetch_assoc();
        
        // Get recipe ingredients using the recipe_ingredient junction table
        $ingredientsSql = "SELECT ri.ing_name 
                          FROM recipe_ingredient ri 
                          WHERE ri.recipe_name = ?";
        $ingredientsStmt = $conn->prepare($ingredientsSql);
        $ingredientsStmt->bind_param("s", $recipe_name);
        $ingredientsStmt->execute();
        $ingredientsResult = $ingredientsStmt->get_result();
        
        $ingredients = [];
        while ($row = $ingredientsResult->fetch_assoc()) {
            $ingredients[] = $row['ing_name'];
        }
        
        // Check if this recipe is in user's favorites
        $favoritesSql = "SELECT COUNT(*) as is_favorite 
                        FROM favorites 
                        WHERE user_name = ? AND recipe_name = ?";
        $favoritesStmt = $conn->prepare($favoritesSql);
        $favoritesStmt->bind_param("ss", $username, $recipe_name);
        $favoritesStmt->execute();
        $favoritesResult = $favoritesStmt->get_result()->fetch_assoc();
        $isFavorite = $favoritesResult['is_favorite'] > 0;
        
        // Note: Based on your SQL schema, there's no "Saved" table
        // This is a placeholder - you'll need to create this table or adjust as needed
        $savedSql = "SELECT 0 as is_saved"; // Temporary query since saved functionality isn't in the schema
        $savedStmt = $conn->prepare($savedSql);
        $savedStmt->execute();
        $savedResult = $savedStmt->get_result()->fetch_assoc();
        $isSaved = false; // Default to false until table exists
    } else {
        $recipe = null; // Recipe not found
    }
} else {
    $recipe = null; // No recipe name provided
}

// Get a placeholder image if no actual image available
$imagePath = "recipe_images/" . urlencode($recipe_name ?? 'default') . ".jpg";
if (!file_exists($imagePath)) {
    $imagePath = "recipe_images/default.jpg"; // Default image
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Recipe Details</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      background-color: #fff9f4;
      color: #333;
    }
    .header {
      width: 100%;
      height: 300px;
      background-size: cover;
      background-position: center;
      border-bottom-left-radius: 40px;
      border-bottom-right-radius: 40px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .container {
      padding: 30px;
      max-width: 1000px;
      margin: auto;
    }
    h1 {
      font-size: 2.5em;
      color: #d4a373;
      margin-top: 20px;
      text-align: center;
    }
    .recipe-meta {
      display: flex;
      justify-content: center;
      gap: 30px;
      margin: 20px 0;
      color: #666;
      font-size: 1.1em;
    }
    .section {
      margin-top: 40px;
    }
    .section h2 {
      font-size: 1.8em;
      color: #bc8a5f;
      margin-bottom: 20px;
    }
    .ingredients, .instructions {
      background: #fff5e1;
      padding: 20px;
      border-radius: 20px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    ul {
      padding-left: 20px;
    }
    li {
      margin-bottom: 10px;
    }
    .button {
      display: inline-block;
      background-color: #d4a373;
      color: white;
      text-decoration: none;
      padding: 12px 25px;
      border-radius: 30px;
      font-size: 1.1em;
      transition: background 0.3s;
      border: none;
      cursor: pointer;
      margin: 5px;
    }
    .button:hover {
      background-color: #bc8a5f;
    }
    .button.remove {
      background-color: #e74c3c;
    }
    .button.remove:hover {
      background-color: #c0392b;
    }
    .actions {
      text-align: center;
      margin-top: 40px;
    }
    .back-button {
      display: block;
      width: 200px;
      margin: 20px auto 0;
      text-align: center;
    }
  </style>
</head>
<body>

<!-- Recipe Image -->
<div class="header" style="background-image: url('<?php echo htmlspecialchars($imagePath); ?>');"></div>

<!-- Main Content -->
<div class="container">
  <?php if ($recipe): ?>
    <h1><?php echo htmlspecialchars($recipe['recipe_name']); ?></h1>
    
    <div class="recipe-meta">
      <span>Preparation Time: <?php echo htmlspecialchars($recipe['time']); ?> minutes</span>
      <span>Level: <?php echo htmlspecialchars($recipe['level_name']); ?></span>
      <span>Type: <?php echo htmlspecialchars($recipe['type_name']); ?></span>
    </div>

    <!-- Ingredients Section -->
    <div class="section">
      <h2>🛒 Ingredients</h2>
      <div class="ingredients">
        <ul>
          <?php foreach ($ingredients as $ingredient): ?>
            <li><?php echo htmlspecialchars($ingredient); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <!-- Instructions Section -->
    <div class="section">
      <h2>👩‍🍳 Instructions</h2>
      <div class="instructions">
        <p><?php echo nl2br(htmlspecialchars($recipe['recipe_details'])); ?></p>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="actions">
      <form action="toggle_favorite.php" method="post" style="display: inline;">
        <input type="hidden" name="recipe_name" value="<?php echo htmlspecialchars($recipe['recipe_name']); ?>">
        <button type="submit" class="button <?php echo $isFavorite ? 'remove' : ''; ?>">
          <?php echo $isFavorite ? 'Remove from Favorites' : 'Add to Favorites'; ?>
        </button>
      </form>
      
      <form action="toggle_saved.php" method="post" style="display: inline;">
        <input type="hidden" name="recipe_name" value="<?php echo htmlspecialchars($recipe['recipe_name']); ?>">
        <button type="submit" class="button <?php echo $isSaved ? 'remove' : ''; ?>">
          <?php echo $isSaved ? 'Remove from Saved' : 'Save Recipe'; ?>
        </button>
      </form>
      
      <a href="my_recipes.php" class="button back-button">Back to Recipes</a>
    </div>
    
  <?php else: ?>
    <h1>Recipe not found</h1>
    <p style="text-align: center;">The recipe you're looking for doesn't exist or has been removed.</p>
    <div class="actions">
      <a href="my_recipes.php" class="button back-button">Back to Recipes</a>
    </div>
  <?php endif; ?>
</div>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>