<?php
session_start();

if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Connexion à la base de données
    $host = 'localhost';
    $db = 'portfolio';
    $user = 'root';
    $pass = 'admin';

    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);

    // Récupérer les informations du formulaire
    $title = $_POST['title'];
    $description = $_POST['description'];
    $technologies = $_POST['technologies'];
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];

    // Déplacer l'image téléchargée dans le dossier des images
    move_uploaded_file($image_tmp, "../images/$image");

    // Ajouter le projet à la base de données
    $query = $conn->prepare("INSERT INTO projects (title, description, technologies, image) VALUES (:title, :description, :technologies, :image)");
    $query->bindParam(':title', $title, PDO::PARAM_STR);
    $query->bindParam(':description', $description, PDO::PARAM_STR);
    $query->bindParam(':technologies', $technologies, PDO::PARAM_STR);
    $query->bindParam(':image', $image, PDO::PARAM_STR);
    $query->execute();

    // Rediriger vers le dashboard
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Projet</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Ajouter un Projet</h1>
    </header>
    <section id="add-project">
        <form action="add_project.php" method="post" enctype="multipart/form-data">
            <p>
                <label for="title">Titre:</label>
                <input type="text" name="title" id="title" required>
            </p>
            <p>
                <label for="description">Description:</label>
                <textarea name="description" id="description" required></textarea>
            </p>
            <p>
                <label for="technologies">Technologies:</label>
                <input type="text" name="technologies" id="technologies" required>
            </p>
            <p>
                <label for="image">Image:</label>
                <input type="file" name="image" id="image" required>
            </p>
            <p>
                <button type="submit">Ajouter</button>
            </p>
        </form>
    </section>
</body>
</html>
