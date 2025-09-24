<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "recipe";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$user = $_SESSION["user_name"] ?? null;
if (!$user) {
    header("Location: login.php");
    exit();
}

$success = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["ingredients"])) {
    $ingredients = json_decode($_POST["ingredients"], true);
    if (is_array($ingredients)) {
        $deleteStmt = $conn->prepare("DELETE FROM user_ingredient WHERE user_name = ?");
        if ($deleteStmt) {
            $deleteStmt->bind_param("s", $user);
            $deleteStmt->execute();
            $deleteStmt->close();
        }

        $stmt = $conn->prepare("INSERT INTO user_ingredient (user_name, ing_name) VALUES (?, ?)");
        if ($stmt) {
            foreach ($ingredients as $ing) {
                $stmt->bind_param("ss", $user, $ing);
                $stmt->execute();
            }
            $stmt->close();
            $success = true;
        }
    }
}

$selected = [];
$res = $conn->prepare("SELECT ing_name FROM user_ingredient WHERE user_name = ?");
$res->bind_param("s", $user);
$res->execute();
$result = $res->get_result();
while ($row = $result->fetch_assoc()) {
    $selected[] = $row['ing_name'];
}
$res->close();

$ingredientsList = [
    "🍖 Meats (Viandes)" => [
        "Eggs" => "🥚 Eggs",
        "Escalope" => "🍗 Escalope",
        "Poulet" => "🍗 Poulet",
        "Viande hachée" => "🥩 Viande hachée",
        "Thon" => "🐟 Thon"
    ],
    "🥦 Vegetables (Légumes)" => [
        "Tomatoes" => "🍅 Tomatoes",
        "Garlic" => "🧄 Garlic",
        "Onion" => "🧅 Onion",
        "Potatoes" => "🥔 Potatoes",
        "Courgettes" => "🥒 Courgettes",
        "Salade" => "🥬 Salade",
        "Lentilles" => "🌰 Lentilles"
    ],
    "🧀 Dairy Products (Laitiers)" => [
        "Fromage" => "🧀 Fromage",
        "Milk" => "🥛 Milk",
        "Butter" => "🧈 Butter",
        "Yaourt" => "🍦 Yaourt"
    ],
    "🛒 Daily Essentials" => [
        "Sucre" => "🍬 Sucre",
        "Salt" => "🧂 Salt",
        "Oil" => "🛢️ Oil",
        "Epice" => "🌶️ Epice",
        "Semoule" => "🌾 Semoule",
        "Vinegar" => "🍶 Vinegar"
    ],
    "🍝 Pasta (Pâtes)" => [
        "Riz" => "🍚 Riz",
        "Lasagnes" => "🍝 Lasagnes",
        "Spaghetti" => "🍝 Spaghetti",
        "Couscous" => "🍛 Couscous"
    ],
    "🍎 Fruits" => [
        "pomme" => "🍎 pomme",
        "citron" => "🍋 citron",
        "Orange" => "🍊 Orange",
        "Banane" => "🍌 Banane",
        "Fraise" => "🍓 Fraise"
    ]
];

$emojiMap = [];
foreach ($ingredientsList as $cat => $ings) {
    foreach ($ings as $key => $label) {
        $emojiMap[$key] = $label;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Ingredients</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; background: #fef9f4; padding: 20px; color: #333; }
    .container { max-width: 1200px; margin: auto; }
    .card { background: white; border-radius: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin-bottom: 30px; padding: 20px; }
    h2 { color: #d4a373; text-align: center; margin-bottom: 20px; }
    .grid { display: flex; flex-wrap: wrap; gap: 15px; justify-content: center; }
    .ingredient { background: #fff5e1; border-radius: 12px; padding: 10px 15px; text-align: center;
      transition: transform 0.3s, background 0.3s; cursor: pointer; min-width: 100px; }
    .ingredient:hover { transform: scale(1.05); background: #ffe8cc; }
    .ingredient.selected { background: #ffe8cc; box-shadow: 0 0 0 2px #d4a373; }
    .selected-ingredients { background: #fff5e1; border-radius: 20px; padding: 20px; margin-bottom: 30px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .selected-item { background: #ffe8cc; display: inline-flex; align-items: center; padding: 8px 12px;
      border-radius: 20px; margin: 5px; font-size: 0.9em; }
    .selected-item button { background: none; border: none; margin-left: 8px; cursor: pointer; font-size: 1em; }
    .button-container { text-align: center; margin-top: 40px; }
    .lets-cook-btn { background: #d4a373; color: white; border: none; padding: 15px 30px; font-size: 1.2em;
      border-radius: 30px; cursor: pointer; transition: background 0.3s; }
    .lets-cook-btn:hover { background: #bc8a5f; }
    .lets-cook-text { margin-top: 10px; font-size: 1em; color: #666; }
    .success-msg {
      background: #d4edda; color: #155724; padding: 10px; border-radius: 10px;
      margin-bottom: 20px; text-align: center;
    }
  </style>
</head>
<body>
<div class="container">
  <?php if ($success): ?>
    <div class="success-msg">✅ Ingredients saved successfully!</div>
  <?php endif; ?>

  <form method="POST">
    <div class="selected-ingredients">
      <h2>🛒 Selected Ingredients</h2>
      <div id="selectedIngredients"></div>
      <input type="hidden" name="ingredients" id="ingredientsInput">
    </div>

    <?php foreach ($ingredientsList as $category => $items): ?>
      <div class="card">
        <h2><?= $category ?></h2>
        <div class="grid">
          <?php foreach ($items as $value => $label): 
            $is_selected = in_array($value, $selected) ? 'selected' : '';
          ?>
            <div class="ingredient <?= $is_selected ?>" onclick="toggleIngredient('<?= addslashes($value) ?>')"><?= $label ?></div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>

    <div class="button-container">
      <button type="submit" class="lets-cook-btn">Let's Cook 🍳</button>
      <div class="lets-cook-text">Click to save and stay on this page</div>

      <div style="margin-top: 20px; display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
        <a href="recipes.php" class="lets-cook-btn" style="text-decoration:none;">🧾 See Compatible Recipes</a>
        <a href="main.php" class="lets-cook-btn" style="text-decoration:none;">📋 Home</a>
      </div>
    </div>
  </form>
</div>

<script>
  let selected = <?= json_encode($selected) ?>;
  let displayMap = <?= json_encode($emojiMap) ?>;

  function toggleIngredient(name) {
    const idx = selected.indexOf(name);
    if (idx === -1) {
      selected.push(name);
    } else {
      selected.splice(idx, 1);
    }
    renderSelectedIngredients();

    document.querySelectorAll('.ingredient').forEach(el => {
      if (el.textContent.trim().includes(displayMap[name])) {
        el.classList.toggle('selected');
      }
    });
  }

  function renderSelectedIngredients() {
    const container = document.getElementById("selectedIngredients");
    container.innerHTML = '';
    selected.forEach(name => {
      const div = document.createElement('div');
      div.classList.add("selected-item");
      div.innerHTML = `${displayMap[name]} <button type="button" onclick="toggleIngredient('${name}')">❌</button>`;
      container.appendChild(div);
    });
    document.getElementById("ingredientsInput").value = JSON.stringify(selected);
  }

  renderSelectedIngredients();
</script>
</body>
</html>

</html>
