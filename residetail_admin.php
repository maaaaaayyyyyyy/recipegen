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
$photo_res = $conn->query("SELECT photo_Url FROM recipes_photo WHERE recipe_name = '$recipe'");
$photo_url = ($row = $photo_res->fetch_assoc()) ? $row['photo_Url'] : "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($recipe); ?> - Admin Details</title>
  <style>
    body { 
      font-family: 'Poppins', sans-serif; 
      background: #f4f7fc; /* Couleur claire similaire aux pages add/edit */
      padding: 30px;
      margin: 0;
    }
    h1 { 
      color: #5a4bd3; 
      text-align: center;
      margin-bottom: 40px;
    }
    .section { 
      margin-top: 40px; 
      max-width: 800px; 
      margin-left: auto; 
      margin-right: auto; 
    }
    .section h2 { 
      color: #5a4bd3; 
      text-align: center; /* Centrer les titres comme Instructions */
    }
    .ingredients, .instructions {
      background: #fff; 
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
    .back-button {
      display: block; 
      margin: 40px auto; 
      text-align: center;
      background-color: #5a4bd3; 
      color: white; 
      text-decoration: none;
      padding: 15px 25px; 
      border-radius: 30px; 
      font-size: 1.1em;
    }
    .photo-container {
      margin-top: 40px;
      display: flex;
      justify-content: center;
      border: 4px solid #5a4bd3; /* Cadre autour de la photo */
      padding: 10px;
      border-radius: 20px;
      max-width: 450px;
      margin-left: auto;
      margin-right: auto;
    }
    .photo-container img {
      width: 80%; /* Taille réduite */
      max-width: 350px; /* Taille maximale de l'image */
      display: block;
      border-radius: 10px; /* Bords arrondis de l'image */
    }
    .admin-actions {
      text-align: center;
      margin-top: 30px;
    }
    .admin-actions a {
      background-color: #5a4bd3;
      color: white;
      padding: 10px 20px;
      border-radius: 30px;
      text-decoration: none;
      font-size: 1.1em;
      transition: background-color 0.3s ease;
    }
    .admin-actions a:hover {
      background-color: #3b36b1; /* Couleur un peu plus foncée */
    }
  </style>
</head>
<body>

<h1>Recipe Details (Admin) - <?php echo htmlspecialchars($recipe); ?></h1>

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

<?php if ($photo_url): ?>
  <div class="section">
    <h2>📸 Recipe Photo</h2>
    <div class="photo-container">
      <img src="<?php echo htmlspecialchars($photo_url); ?>" alt="Photo of <?php echo htmlspecialchars($recipe); ?>">
    </div>
  </div>
<?php endif; ?>

<div class="admin-actions">
  <a href="edit_recipe.php?name=<?php echo urlencode($recipe); ?>">✏️ Edit Recipe</a>
  <a href="manage_recipes.php">⬅️ Back to Recipe Management</a>
</div>

</body>
</html>
