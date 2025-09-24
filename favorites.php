<?php
session_start();

if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user_name'];
$conn = new mysqli("localhost", "root", "", "recipe");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Gérer ajout/suppression via requête POST JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['recipe'], $data['action'])) {
        $recipe = $conn->real_escape_string($data['recipe']);
        $action = $data['action'];

        if ($action === "add") {
            $stmt = $conn->prepare("INSERT IGNORE INTO favorites (user_name, recipe_name) VALUES (?, ?)");
            $stmt->bind_param("ss", $user, $recipe);
            $stmt->execute();
        } elseif ($action === "remove") {
            $stmt = $conn->prepare("DELETE FROM favorites WHERE user_name = ? AND recipe_name = ?");
            $stmt->bind_param("ss", $user, $recipe);
            $stmt->execute();
        }
    }

    exit(); // Empêche l’affichage HTML lors d’une requête AJAX
}

// Si ce n’est pas une requête POST, afficher la page des favoris
$sql = "
SELECT r.recipe_name, COALESCE(p.photo_Url, 'default.jpg') AS photo
FROM favorites f
JOIN recipe r ON f.recipe_name = r.recipe_name
LEFT JOIN recipes_photo p ON r.recipe_name = p.recipe_name
WHERE f.user_name = '$user'
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>❤️ My Favorite Recipes</title>
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
#favoritesList {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 40px;
  min-height: 60vh; /* hauteur minimale pour centrer si peu de contenu */
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
  min-height: 60vh;
  display: flex;
  align-items: center;
  justify-content: center;
}


a.button {
  display: inline-block;
}

  </style>
</head>
<body>
<h1>❤️ My Favorite Recipes</h1>
<div id="favoritesList">
<?php if ($result->num_rows > 0): ?>
  <?php while($row = $result->fetch_assoc()): ?>
    <div class="recipe-card">
      <img src="<?php echo htmlspecialchars($row['photo']); ?>" alt="Photo of <?php echo htmlspecialchars($row['recipe_name']); ?>">
      <h2><?php echo htmlspecialchars($row['recipe_name']); ?></h2>
      <div class="buttons">
        <a href="interface.php?id=<?php echo urlencode($row['recipe_name']); ?>" class="button">View Recipe</a>
        <button class="button remove" onclick="removeFavorite('<?php echo htmlspecialchars($row['recipe_name']); ?>')">Remove</button>
      </div>
    </div>
  <?php endwhile; ?>
<?php else: ?>
  <p class="empty">You have no favorite recipes yet.</p>
<?php endif; ?>
</div>

<script>
function removeFavorite(name) {
  fetch("favorites.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ recipe: name, action: "remove" })
  })
  .then(res => res.text())
  .then(() => location.reload())
  .catch(err => console.error("Error:", err));
}
</script>
</body>
</html>
