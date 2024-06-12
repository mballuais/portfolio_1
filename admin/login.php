<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Connexion à la base de données
    $host = 'localhost';
    $db = 'portfolio';
    $user = 'root';
    $pass = 'admin';

    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);

    // Récupérer les informations du formulaire
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Vérifier les informations de l'utilisateur
    $query = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $query->bindParam(':username', $username, PDO::PARAM_STR);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin'] = true;
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'Nom d\'utilisateur ou mot de passe incorrect';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin</title>
    <link rel="stylesheet" href="../css/style_login.css">
</head>
<body>
    <header>
        <h1>Connexion Admin</h1>
    </header>
    <section id="login">
        <form action="login.php" method="post">
            <p>
                <label for="username">Nom d'utilisateur:</label>
                <input type="text" name="username" id="username" required>
            </p>
            <p>
                <label for="password">Mot de passe:</label>
                <input type="password" name="password" id="password" required>
            </p>
            <p>
                <button type="submit">Connexion</button>
            </p>
            <?php if (isset($error)): ?>
                <p id="error"><?php echo $error; ?></p>
            <?php endif; ?>
        </form>
    </section>
    <script src="../js/script.js"></script>
</body>
</html>
