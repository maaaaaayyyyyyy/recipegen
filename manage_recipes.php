<?php
session_start();
if (!isset($_SESSION['user_name']) || $_SESSION['is_admin'] !== 1) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "recipe");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Suppression via AJAX
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["recipe_name"])) {
    $recipeName = $_POST["recipe_name"];

    // Supprimer l'image
    $stmt = $conn->prepare("SELECT photo_Url FROM recipes_photo WHERE recipe_name = ?");
    $stmt->bind_param("s", $recipeName);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if (file_exists($row['photo_Url'])) {
            unlink($row['photo_Url']);
        }
    }

    $conn->query("DELETE FROM recipes_photo WHERE recipe_name = '$recipeName'");
    $conn->query("DELETE FROM recipe WHERE recipe_name = '$recipeName'");

    echo "success";
    exit();
}

$recipes = $conn->query("SELECT * FROM recipe");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Recipes</title>
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
    #recipesList {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      gap: 40px;
    }
    .recipe-card {
      background: white;
      border-radius: 20px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
      width: 45%;
      padding-bottom: 20px;
      overflow: hidden;
      transition: transform 0.3s ease;
    }
    .recipe-card:hover {
      transform: translateY(-8px);
    }
    .recipe-card img {
      width: 100%;
      height: 250px;
      object-fit: cover;
      border-top-left-radius: 20px;
      border-top-right-radius: 20px;
    }
    .recipe-card h2 {
      text-align: center;
      color: #333;
      margin: 20px 0 10px;
      font-size: 24px;
    }
    .buttons {
      display: flex;
      justify-content: center;
      gap: 15px;
      padding: 0 20px;
      margin-top: 10px;
    }
    .button {
      background-color: #6c5ce7;
      border: none;
      color: white;
      padding: 10px 18px;
      border-radius: 20px;
      font-size: 14px;
      cursor: pointer;
      text-decoration: none;
      transition: background-color 0.3s ease;
    }
    .button:hover {
      background-color: #5a4bd3;
    }
    .button.delete {
      background-color: #e74c3c;
    }
    .button.delete:hover {
      background-color: #c0392b;
    }
    .add-recipe {
      display: block;
      width: fit-content;
      margin: 0 auto 40px;
      padding: 15px 30px;
      background-color: #6c5ce7;
      color: white;
      text-decoration: none;
      font-size: 18px;
      border-radius: 30px;
      text-align: center;
    }
    .add-recipe:hover {
      background-color: #5a4bd3;
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

  <script>
    function deleteRecipe(recipeName, cardElement) {
      if (confirm("Are you sure you want to delete this recipe?")) {
        const formData = new FormData();
        formData.append("recipe_name", recipeName);

        fetch("manage_recipes.php", {
          method: "POST",
          body: formData
        })
        .then(res => res.text())
        .then(response => {
          if (response.trim() === "success") {
            cardElement.remove();
          } else {
            alert("Failed to delete the recipe.");
          }
        })
        .catch(err => {
          alert("Error: " + err);
        });
      }
    }
  </script>
</head>
<body>

<h1>🍳 Manage Recipes</h1>
<a href="add_recipe.php" class="add-recipe">➕ Add New Recipe</a>

<div id="recipesList">
  <?php while($row = $recipes->fetch_assoc()): 
    $recipeName = $row['recipe_name'];
    $photo = "default.jpg";

    $stmt = $conn->prepare("SELECT photo_Url FROM recipes_photo WHERE recipe_name = ?");
    $stmt->bind_param("s", $recipeName);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $photoRow = $res->fetch_assoc()) {
      $photo = $photoRow['photo_Url'];
    }
  ?>
    <div class="recipe-card">
      <img src="<?php echo htmlspecialchars($photo); ?>" alt="Photo of <?php echo htmlspecialchars($recipeName); ?>">
      <h2><?php echo htmlspecialchars($recipeName); ?></h2>
      <div class="buttons">
        <a class="button" href="residetail_admin.php?id=<?php echo urlencode($recipeName); ?>">👁 View</a>
        <a class="button" href="edit_recipe.php?name=<?php echo urlencode($recipeName); ?>">✏️ Edit</a>
        <a class="button delete" href="#" onclick="event.preventDefault(); deleteRecipe('<?php echo htmlspecialchars($recipeName); ?>', this.closest('.recipe-card'));">🗑 Delete</a>
      </div>
    </div>
  <?php endwhile; ?>
</div>

<a href="main_admin.php" class="back-to-admin">🏠 Back to Admin Dashboard</a>

</body>
</html>
