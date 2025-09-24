<?php
$host = "localhost";
$dbname = "recipe"; // Remplace-le par ton vrai nom de base
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if (isset($_GET['delete'])) {
    $userToDelete = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE user_name = ?");
    $stmt->bind_param("s", $userToDelete);
    $stmt->execute();
    header("Location: manage_users.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_admin'])) {
    $user_name = $_POST['user_name'];
    $email_or_phone = $_POST['email_or_phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = filter_var($email_or_phone, FILTER_VALIDATE_EMAIL) ? $email_or_phone : null;
    $phone = !$email ? $email_or_phone : null;

    $stmt = $conn->prepare("INSERT INTO users (user_name, email, phone, password, is_admin) VALUES (?, ?, ?, ?, TRUE)");
    $stmt->bind_param("ssss", $user_name, $email, $phone, $password);
    $stmt->execute();
    header("Location: manage_users.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
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

    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      border-radius: 12px;
      overflow: hidden;
      margin-top: 30px;
    }

    th, td {
      padding: 14px 20px;
      text-align: left;
    }

    th {
      background-color: #6c5ce7;
      color: white;
      font-weight: 600;
    }

    tr:nth-child(even) {
      background-color: #eef2f7;
    }

    tr:hover {
      background-color: #e1e7f0;
    }

    .delete-btn {
      background-color: #e63946;
      color: white;
      padding: 8px 16px;
      border: none;
      border-radius: 20px;
      cursor: pointer;
      font-size: 14px;
      transition: background-color 0.3s ease;
      text-decoration: none;
    }

    .delete-btn:hover {
      background-color: #c62828;
    }

    .form-section {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      max-width: 500px;
      margin: 40px auto;
      padding: 30px;
    }

    .form-section h2 {
      text-align: center;
      color: #6c5ce7;
      margin-bottom: 20px;
    }

    .form-section input[type="text"],
    .form-section input[type="password"] {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 16px;
    }

    .form-section button {
      width: 100%;
      background-color: #6c5ce7;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
    }

    .form-section button:hover {
      background-color: #5a4bd3;
    }

    .back-link {
      text-align: center;
      margin-top: 30px;
    }

    .back-link a {
      background: #ccc;
      color: #333;
      padding: 10px 20px;
      border-radius: 20px;
      text-decoration: none;
      transition: background 0.3s ease;
    }

    .back-link a:hover {
      background: #bbb;
    }

    .success-message {
      text-align: center;
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
      padding: 10px;
      margin: 20px auto;
      max-width: 500px;
      border-radius: 8px;
    }
  </style>
</head>
<body>

<h1>👥 Manage Users</h1>

<?php if (isset($_GET['success'])): ?>
  <div class="success-message">New admin added successfully!</div>
<?php endif; ?>
<table>
  <tr>
    <th>User Name</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Admin</th>
    <th>Action</th>
  </tr>
  <?php
  $result = $conn->query("SELECT * FROM users");
  while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>" . htmlspecialchars($row['user_name']) . "</td>
            <td>" . htmlspecialchars($row['email']) . "</td>
            <td>" . htmlspecialchars($row['phone']) . "</td>
            <td>" . ($row['is_admin'] ? 'Yes' : 'No') . "</td>
            <td><a class='delete-btn' href='manage_users.php?delete=" . urlencode($row['user_name']) . "' onclick='return confirm(\"Are you sure you want to delete this user?\");'>Delete</a></td>
          </tr>";
  }
  ?>
</table>

<div class="form-section">
  <h2>Add New Admin</h2>
  <form method="POST">
    <input type="text" name="user_name" placeholder="User Name" required>
    <input type="text" name="email_or_phone" placeholder="Email or Phone" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="add_admin">Add Admin</button>
  </form>
</div>

<div class="back-link">
  <a href="main_admin.php">⬅️ Back to Admin Home</a>
</div>

</body>
</html>