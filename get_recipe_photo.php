<?php
if (!isset($_GET['name'])) {
    http_response_code(400);
    echo "Missing recipe name";
    exit();
}

$recipeName = $_GET['name'];

$conn = new mysqli("localhost", "root", "", "recipe");
if ($conn->connect_error) {
    http_response_code(500);
    echo "Database connection failed";
    exit();
}

$stmt = $conn->prepare("SELECT photo_Url FROM recipes_photo WHERE recipe_name = ?");
$stmt->bind_param("s", $recipeName);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo $row['photo_Url'];
} else {
    echo "default.jpg"; // lien vers une image par défaut
}

$stmt->close();
$conn->close();
