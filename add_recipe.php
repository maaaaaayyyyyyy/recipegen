<?php
session_start();
if (!isset($_SESSION['user_name']) || $_SESSION['is_admin'] !== 1) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "recipe");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Liste des ingrédients groupés par catégorie
$ingredients_by_category = [];
$query = "SELECT * FROM ingredient";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $ingredients_by_category[$row['category']][] = $row['ing_name'];
}

// Traitement de l’envoi du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $recipe_name = trim($_POST['recipe_name']);
    $description = $_POST['description'];
    $time = $_POST['time'];
    $level_name = $_POST['level_name'];
    $type_name = $_POST['type_name'];
    $ingredients = $_POST['ingredients'] ?? [];

    // Vérifie si la recette existe déjà
    $stmt_check = $conn->prepare("SELECT recipe_name FROM recipe WHERE recipe_name = ?");
    $stmt_check->bind_param("s", $recipe_name);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $error = "Recipe with this name already exists!";
    } else {
        // Insertion dans la table recipe
        $stmt = $conn->prepare("INSERT INTO recipe (recipe_name, recipe_details, time, level_name, type_name) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $recipe_name, $description, $time, $level_name, $type_name);
        $stmt->execute();

        // Insertion des ingrédients dans recipe_ingredient
        $stmtIng = $conn->prepare("INSERT INTO recipe_ingredient (recipe_name, ing_name) VALUES (?, ?)");
        foreach ($ingredients as $ing) {
            $stmtIng->bind_param("ss", $recipe_name, $ing);
            $stmtIng->execute();
        }

        // Traitement de l'image si elle est téléchargée
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
            $target_dir = "uploads/";
            $file_name = basename($_FILES["photo"]["name"]);
            $target_file = $target_dir . time() . "_" . $file_name;
            move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file);

            $stmtPhoto = $conn->prepare("INSERT INTO recipes_photo (recipe_name, photo_Url) VALUES (?, ?)");
            $stmtPhoto->bind_param("ss", $recipe_name, $target_file);
            $stmtPhoto->execute();
        }

        header("Location: manage_recipes.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add New Recipe</title>
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

    .container {
      max-width: 900px;
      margin: 0 auto;
      padding: 40px;
      background: white;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      font-size: 24px;
      color: #6c5ce7;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    input, textarea, select {
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 16px;
      width: 100%;
      box-sizing: border-box;
    }

    textarea {
      resize: vertical;
      height: 120px;
    }

    label {
      font-size: 18px;
      color: #333;
    }

    .checkbox-group {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .checkbox-group label {
      font-size: 16px;
      display: inline-block;
      margin-right: 10px;
    }

    .checkbox-group input {
      margin-right: 5px;
    }

    .form-actions {
      text-align: center;
    }

    .form-actions button {
      background-color: #6c5ce7;
      border: none;
      padding: 12px 25px;
      border-radius: 30px;
      color: white;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .form-actions button:hover {
      background-color: #5a4bd3;
    }

    .back-button {
      margin-top: 20px;
      text-align: center;
    }

    .back-button a {
      background-color: #ccc;
      padding: 12px 25px;
      color: #333;
      text-decoration: none;
      font-size: 16px;
      border-radius: 30px;
      transition: background-color 0.3s ease;
    }

    .back-button a:hover {
      background-color: #bbb;
    }

    .error {
      color: red;
      font-size: 14px;
      text-align: center;
    }

  </style>
</head>
<body>

<div class="container">
  <h2>Add a New Recipe</h2>

  <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

  <form method="POST" enctype="multipart/form-data">
    <input type="text" name="recipe_name" placeholder="Recipe Name" required>
    <textarea name="description" placeholder="Recipe Description" required></textarea>
    <input type="number" name="time" placeholder="Time (in minutes)" required>

    <select name="level_name" required>
      <option value="">-- Select Level --</option>
      <?php
      $res = $conn->query("SELECT level_name FROM level");
      while ($row = $res->fetch_assoc()) {
          echo "<option value='{$row['level_name']}'>{$row['level_name']}</option>";
      }
      ?>
    </select>

    <select name="type_name" required>
      <option value="">-- Select Type --</option>
      <?php
      $res = $conn->query("SELECT type_name FROM type");
      while ($row = $res->fetch_assoc()) {
          echo "<option value='{$row['type_name']}'>{$row['type_name']}</option>";
      }
      ?>
    </select>

    <label>Choose Ingredients:</label><br>
    <?php foreach ($ingredients_by_category as $category => $ings): ?>
      <div class="category">
        <h4><?= htmlspecialchars($category) ?></h4>
        <div class="checkbox-group">
          <?php foreach ($ings as $value): ?>
            <label>
              <input type="checkbox" name="ingredients[]" value="<?= htmlspecialchars($value) ?>"> <?= htmlspecialchars($value) ?>
            </label>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>

    <input type="file" name="photo" accept="image/*">
    
    <div class="form-actions">
      <button type="submit">Save Recipe</button>
    </div>
  </form>

  <div class="back-button">
    <a href="manage_recipes.php">Back to Recipe Management</a>
  </div>
</div>

</body>
</html>
