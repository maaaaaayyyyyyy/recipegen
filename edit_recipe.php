<?php
session_start();
if (!isset($_SESSION['user_name']) || $_SESSION['is_admin'] !== 1) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "recipe");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if (!isset($_GET['name'])) {
    echo "Recipe name not specified.";
    exit();
}
$recipe_name = $_GET['name'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_description = $_POST['description'];
    $new_time = $_POST['time'];
    $new_level = $_POST['level_name'];
    $new_type = $_POST['type_name'];
    $new_ingredients = isset($_POST['ingredients']) ? $_POST['ingredients'] : [];

    $stmt = $conn->prepare("UPDATE recipe SET recipe_details=?, time=?, level_name=?, type_name=? WHERE recipe_name=?");
    $stmt->bind_param("sisss", $new_description, $new_time, $new_level, $new_type, $recipe_name);
    $stmt->execute();

    // Met à jour les ingrédients
    $conn->query("DELETE FROM recipe_ingredient WHERE recipe_name='$recipe_name'");
    $stmtIng = $conn->prepare("INSERT INTO recipe_ingredient (recipe_name, ing_name) VALUES (?, ?)");
    foreach ($new_ingredients as $ing) {
        $stmtIng->bind_param("ss", $recipe_name, $ing);
        $stmtIng->execute();
    }

    // Gérer la photo si elle est uploadée
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $target_dir = "uploads/";
        $file_name = basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . time() . "_" . $file_name;
        move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file);

        $stmtPhoto = $conn->prepare("REPLACE INTO recipes_photo (recipe_name, photo_Url) VALUES (?, ?)");
        $stmtPhoto->bind_param("ss", $recipe_name, $target_file);
        $stmtPhoto->execute();
    }

    header("Location: manage_recipes.php");
    exit();
}

// Récupère les données existantes
$recipe = $conn->query("SELECT * FROM recipe WHERE recipe_name='$recipe_name'")->fetch_assoc();
$current_ingredients = [];
$res = $conn->query("SELECT ing_name FROM recipe_ingredient WHERE recipe_name='$recipe_name'");
while ($row = $res->fetch_assoc()) {
    $current_ingredients[] = $row['ing_name'];
}
$photo_res = $conn->query("SELECT photo_Url FROM recipes_photo WHERE recipe_name='$recipe_name'");
$photo_url = ($row = $photo_res->fetch_assoc()) ? $row['photo_Url'] : "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Recipe</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f4f8;
            padding: 40px;
        }
        form {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #6c5ce7;
            margin-bottom: 30px;
        }
        input[type="text"], input[type="number"], textarea, select, input[type="file"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
        button {
            background-color: #6c5ce7;
            color: white;
            padding: 15px;
            width: 100%;
            border: none;
            border-radius: 20px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #5a4bd3;
        }
        img {
            max-width: 100%;
            margin: 20px auto;
            display: block;
            border-radius: 10px;
        }
        .category {
            margin-bottom: 15px;
        }
        .category strong {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        label input[type="checkbox"] {
            margin-right: 5px;
        }
    </style>
</head>
<body>

<form action="" method="POST" enctype="multipart/form-data">
    <h2>✏️ Edit Recipe: <?= htmlspecialchars($recipe_name) ?></h2>

    <textarea name="description" required><?= htmlspecialchars($recipe['recipe_details']) ?></textarea>
    <input type="number" name="time" value="<?= $recipe['time'] ?>" placeholder="Preparation Time" required>

    <select name="level_name" required>
        <option value="">-- Select Level --</option>
        <?php
        $levels = $conn->query("SELECT level_name FROM level");
        while ($lvl = $levels->fetch_assoc()) {
            $selected = ($lvl['level_name'] == $recipe['level_name']) ? "selected" : "";
            echo "<option value='{$lvl['level_name']}' $selected>{$lvl['level_name']}</option>";
        }
        ?>
    </select>

    <select name="type_name" required>
        <option value="">-- Select Type --</option>
        <?php
        $types = $conn->query("SELECT type_name FROM type");
        while ($typ = $types->fetch_assoc()) {
            $selected = ($typ['type_name'] == $recipe['type_name']) ? "selected" : "";
            echo "<option value='{$typ['type_name']}' $selected>{$typ['type_name']}</option>";
        }
        ?>
    </select>

    <label>Choisissez les ingrédients :</label><br>
    <?php
    $categories = $conn->query("SELECT DISTINCT category FROM ingredient");
    while ($cat = $categories->fetch_assoc()) {
        $cat_name = $cat['category'];
        echo "<div class='category'><strong>$cat_name</strong>";
        $ings = $conn->query("SELECT ing_name FROM ingredient WHERE category='$cat_name'");
        while ($ing = $ings->fetch_assoc()) {
            $ing_name = $ing['ing_name'];
            $checked = in_array($ing_name, $current_ingredients) ? "checked" : "";
            echo "<label><input type='checkbox' name='ingredients[]' value='$ing_name' $checked> $ing_name</label><br>";
        }
        echo "</div>";
    }
    ?>

    <?php if ($photo_url): ?>
        <img src="<?= $photo_url ?>" alt="Current Photo">
    <?php endif; ?>
    <input type="file" name="photo" accept="image/*">

    <button type="submit">💾 Update Recipe</button>
</form>

</body>
</html>
