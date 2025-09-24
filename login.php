<?php include('server.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Recipe Matcher</title>
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
      max-width: 400px;
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

    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
    }

    .actions {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 14px;
      margin-top: 10px;
      margin-bottom: 20px;
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

    .signup {
      margin-top: 20px;
      font-size: 14px;
    }

    .signup a {
      color: #d4a373;
      text-decoration: none;
      font-weight: bold;
    }

    .signup a:hover {
      text-decoration: underline;
    }

    .error {
      color: red;
      margin-bottom: 15px;
      font-size: 14px;
      text-align: left;
    }

    label {
      cursor: pointer;
    }

    a {
      text-decoration: none;
      color: #888;
    }

    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="container">
  <h1>Welcome Back!</h1>
  <p class="slogan">Log in to match recipes to your ingredients 🥕🍳</p>

  <?php if (!empty($errors)): ?>
    <div class="error">
      <?php foreach ($errors as $error): ?>
        <p>⚠️ <?php echo htmlspecialchars($error); ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form action="login.php" method="post" autocomplete="off">
    <input type="email" name="email" placeholder="Enter your email" required>
    <input type="password" name="password" placeholder="Password" required>

    <div class="actions">
      <label><input type="checkbox" name="remember_me"> Remember me</label>
      <a href="#">Forgot?</a>
    </div>

    <button type="submit" name="login_user">Login</button>
  </form>

  <div class="social-login">
    <button type="button" onclick="alert('Google login coming soon!')">Sign in with Google</button>
    <button type="button" onclick="alert('Apple login coming soon!')">Sign in with Apple</button>
  </div>

  <div class="signup">
    Don’t have an account? <a href="signin.php">Sign up</a>
  </div>
</div>

</body>
</html>
