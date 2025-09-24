<?php
session_start();

if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user_name'];
$conn = new mysqli("localhost", "root", "", "recipe");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Récupérer préférences
$stmt = $conn->prepare("SELECT type_name, level_name FROM users WHERE user_name = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$userInfo = $stmt->get_result()->fetch_assoc();
$type = $userInfo['type_name'];
$level = $userInfo['level_name'];

// Ingrédients
$userIngredients = [];
$stmt = $conn->prepare("SELECT ing_name FROM user_ingredient WHERE user_name = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $userIngredients[] = $conn->real_escape_string($row['ing_name']);
}
if (empty($userIngredients)) $userIngredients[] = '---';
$userIngredientsStr = "'" . implode("','", $userIngredients) . "'";

// Recettes compatibles
$sql = "
SELECT r.recipe_name 
FROM recipe r
WHERE r.type_name = ? AND r.level_name = ?
AND NOT EXISTS (
  SELECT 1 FROM recipe_ingredient ri
  WHERE ri.recipe_name = r.recipe_name
  AND ri.ing_name NOT IN ($userIngredientsStr)
)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $type, $level);
$stmt->execute();
$recipes = $stmt->get_result();

// Recettes favorites
$favList = [];
$stmt = $conn->prepare("SELECT recipe_name FROM favorites WHERE user_name = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$favRes = $stmt->get_result();
while ($row = $favRes->fetch_assoc()) {
    $favList[] = $row["recipe_name"];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Recipes You Can Cook</title>
  <style>
body {
  font-family: 'Poppins', sans-serif;
  background: #fef9f4;
  margin: 0;
  padding: 30px;
}

h1 {
  text-align: center;
  color: #d4a373;
  font-size: 36px;
  margin-bottom: 40px;
}

#recipesList {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 40px;
}

.recipe-card {
  background: white;
  border-radius: 20px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
  width: 600px;  /* carte plus large */
  padding-bottom: 20px;
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.recipe-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
}

.recipe-card img {
  width: 100%;
  height: 250px; /* photo rectangulaire horizontale */
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
  background-color: #d4a373;
  border: none;
  color: white;
  padding: 10px 18px;
  border-radius: 20px;
  font-size: 14px;
  cursor: pointer;
  text-decoration: none;
  transition: background 0.3s ease, transform 0.2s ease;
}

.button:hover {
  background-color: #bc8a5f;
  transform: scale(1.05);
}

.button.remove {
  background-color: #e74c3c;
}

.button.remove:hover {
  background-color: #c0392b;
}

.empty {
  text-align: center;
  font-size: 20px;
  color: #888;
  margin-top: 60px;
}

a.button {
  display: inline-block;
}


  </style>
</head>
<body>
<h1>🍽️ Recipes You Can Cook</h1>
<div id="recipesList">
<?php if ($recipes->num_rows > 0): ?>
  <?php while($row = $recipes->fetch_assoc()):
    $recipeName = $row['recipe_name'];
    $photo = "default.jpg";

    $stmt = $conn->prepare("SELECT photo_Url FROM recipes_photo WHERE recipe_name = ?");
    $stmt->bind_param("s", $recipeName);
    $stmt->execute();
    $photoRes = $stmt->get_result();
    if ($photoRes && $photoRow = $photoRes->fetch_assoc()) {
        $photo = $photoRow['photo_Url'];
    }

    $isFavorite = in_array($recipeName, $favList);
    $btnClass = $isFavorite ? "remove" : "add";
    $btnLabel = $isFavorite ? "Remove from Favorites" : "Add to Favorites";
  ?>
    <div class="recipe-card" data-recipe="<?php echo htmlspecialchars($recipeName); ?>">
      <img src="<?php echo htmlspecialchars($photo); ?>" alt="Photo of <?php echo htmlspecialchars($recipeName); ?>">
      <h2><?php echo htmlspecialchars($recipeName); ?></h2>
      <div class="buttons">
        <a class="button" href="interface.php?id=<?php echo urlencode($recipeName); ?>">View Recipe</a>
        <button class="button <?php echo $btnClass; ?>" data-name="<?php echo htmlspecialchars($recipeName); ?>">
          <?php echo $btnLabel; ?>
        </button>
      </div>
    </div>
  <?php endwhile; ?>
<?php else: ?>
  <p style="text-align:center;">No recipes match your ingredients and preferences.</p>
<?php endif; ?>
</div>

<div style="text-align: center; margin-top: 40px;">
    <a href="main.php" class="button" style="text-decoration: none;">📋 Home</a>
</div>

<script>
document.querySelectorAll(".button.add, .button.remove").forEach(button => {
  button.addEventListener("click", function () {
    const name = this.getAttribute("data-name");
    const action = this.classList.contains("add") ? "add" : "remove";

    fetch("favorites.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ recipe: name, action: action })
    })
    .then(res => res.text())
    .then(() => location.reload())
    .catch(err => console.error("Error:", err));
  });
});
</script>
</body>
</html>
