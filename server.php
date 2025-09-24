<?php
session_start();

// Connexion à la base de données
$db = mysqli_connect('localhost', 'root', '', 'recipe');
if (!$db) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

// Initialisation
$errors = array();

// INSCRIPTION
if (isset($_POST['signup_user'])) {
    $user_name  = mysqli_real_escape_string($db, $_POST['user_name']);
    $email      = mysqli_real_escape_string($db, $_POST['email']);
    $phone      = mysqli_real_escape_string($db, $_POST['phone']);
    $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
    $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

    // Vérification
    if (empty($user_name)) array_push($errors, "Nom d'utilisateur requis");
    if (empty($password_1)) array_push($errors, "Mot de passe requis");
    if ($password_1 !== $password_2) array_push($errors, "Les mots de passe ne correspondent pas");
    if (empty($email) && empty($phone)) array_push($errors, "Fournir un email ou un téléphone");

    // Unicité
    $check = "SELECT * FROM users WHERE user_name=? OR email=? LIMIT 1";
    $stmt = mysqli_prepare($db, $check);
    mysqli_stmt_bind_param($stmt, "ss", $user_name, $email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($res);

    if ($user) {
        if ($user['user_name'] === $user_name) array_push($errors, "Nom d'utilisateur déjà pris");
        if (!empty($email) && $user['email'] === $email) array_push($errors, "Email déjà utilisé");
    }

    if (count($errors) === 0) {
        $hashed = password_hash($password_1, PASSWORD_DEFAULT);
        $insert = "INSERT INTO users (user_name, email, phone, password) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($db, $insert);
        mysqli_stmt_bind_param($stmt, "ssss", $user_name, $email, $phone, $hashed);
        mysqli_stmt_execute($stmt);

        $_SESSION['user_name'] = $user_name;
        $_SESSION['success'] = "Inscription réussie";
        header('Location: questions.php');
        exit();
    }
}

// CONNEXION
if (isset($_POST['login_user'])) {
    $email    = mysqli_real_escape_string($db, $_POST['email']);
    $password = mysqli_real_escape_string($db, $_POST['password']);

    if (empty($email)) array_push($errors, "Email requis");
    if (empty($password)) array_push($errors, "Mot de passe requis");

    if (count($errors) === 0) {
        $query = "SELECT * FROM users WHERE email=? LIMIT 1";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($res);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_name'] = $user['user_name'];
            $_SESSION['is_admin'] = $user['is_admin']; // stocke le rôle
            $_SESSION['success'] = "Connexion réussie";

            if ($user['is_admin']) {
                header('Location: main_admin.php');
            } else {
                header('Location: questions.php');
            }
            exit();
        } else {
            array_push($errors, "Email ou mot de passe incorrect");
        }
    }
}
?>
