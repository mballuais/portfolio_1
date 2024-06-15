<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin</title>
    <link rel="stylesheet" href="../css/style_login.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Oswald:wght@300;400;500;700&display=swap">
</head>
<body>
    <div id="particles-js"></div>
    <header>
        <a href="../" class="back-button">&larr; Retour</a>
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
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script src="../js/scriptindex.js"></script>
</body>
</html>
