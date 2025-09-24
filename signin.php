<?php 
include('server.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up - Recipe Matcher</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #fef9f4;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }

    .container {
      background: white;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 500px;
      text-align: center;
    }

    h1 {
      margin-bottom: 10px;
      font-size: 26px;
      color: #d4a373;
    }

    p.slogan {
      font-size: 14px;
      color: #888;
      margin-bottom: 30px;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
    }

    button {
      width: 100%;
      padding: 12px;
      background: #d4a373;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
    }

    button:hover {
      background: #bc8a5f;
    }

    .social-login {
      margin-top: 20px;
    }

    .social-login button {
      background: #f1f1f1;
      color: #333;
      margin-top: 10px;
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: none;
      cursor: pointer;
    }

    .login-link {
      margin-top: 20px;
      font-size: 14px;
    }

    .login-link a {
      color: #d4a373;
      text-decoration: none;
      font-weight: bold;
    }

    .login-link a:hover {
      text-decoration: underline;
    }

    .error {
      color: red;
      margin-bottom: 15px;
      font-size: 14px;
      text-align: left;
    }

    #notification {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      text-align: left;
    }
  </style>
</head>
<body>

<div class="container">
  <h1>Create Account</h1>
  <p class="slogan">Your perfect recipe based on your ingredients 🥗</p>

  <?php if (isset($_SESSION['success'])) : ?>
    <div id="notification"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
  <?php endif ?>

  <?php if (isset($errors) && count($errors) > 0): ?>
    <div class="error">
      <?php foreach ($errors as $error): ?>
        <p>⚠️ <?php echo htmlspecialchars($error); ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post" action="" onsubmit="return checkContactInfo()">
    <input type="text" name="user_name" placeholder="Username" required value="<?php echo isset($_POST['user_name']) ? htmlspecialchars($_POST['user_name']) : ''; ?>">

    <input type="email" name="email" id="email" placeholder="Email (optional)" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" pattern="^[^@\s]+@[^@\s]+\.[^@\s]+$">

    <input type="text" name="phone" id="phone" placeholder="Phone number (optional)" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">

    <input type="password" name="password_1" placeholder="Password" required>
    <input type="password" name="password_2" placeholder="Confirm Password" required>

    <button type="submit" name="signup_user">Sign Up</button>
  </form>

  <div class="social-login">
    <button type="button" onclick="alert('Sign up with Google coming soon!')">Sign up with Google</button>
    <button type="button" onclick="alert('Sign up with Apple coming soon!')">Sign up with Apple</button>
  </div>

  <div class="login-link">
    Already have an account? <a href="login.php">Login</a>
  </div>
</div>

<script>
  const notif = document.getElementById('notification');
  if (notif) {
    setTimeout(() => {
      notif.style.display = 'none';
    }, 3000);
  }

  function checkContactInfo() {
    const email = document.getElementById("email").value.trim();
    const phone = document.getElementById("phone").value.trim();
    if (!email && !phone) {
      alert("Please provide at least an email or a phone number.");
      return false;
    }
    return true;
  }
</script>

</body>
</html>
