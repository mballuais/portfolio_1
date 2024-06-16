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

    // Ajouter le projet à la base de données
    $query = $conn->prepare("INSERT INTO projects (title, description, technologies) VALUES (:title, :description, :technologies)");
    $query->bindParam(':title', $title, PDO::PARAM_STR);
    $query->bindParam(':description', $description, PDO::PARAM_STR);
    $query->bindParam(':technologies', $technologies, PDO::PARAM_STR);
    $query->execute();

    $project_id = $conn->lastInsertId();

    // Handle multiple image uploads
    foreach ($_FILES['images']['name'] as $key => $image_name) {
        $image_tmp = $_FILES['images']['tmp_name'][$key];
        move_uploaded_file($image_tmp, "../images/$image_name");

        $image_query = $conn->prepare("INSERT INTO project_images (project_id, image) VALUES (:project_id, :image)");
        $image_query->bindParam(':project_id', $project_id, PDO::PARAM_INT);
        $image_query->bindParam(':image', $image_name, PDO::PARAM_STR);
        $image_query->execute();
    }

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
    <link rel="stylesheet" href="../css/style_add.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&display=swap">
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0"></script>
    <script src="../js/particules.js" defer></script> <!-- Link to the external JS file -->
</head>
<body>
    <div id="particles-js"></div>
    <header>
        <a href="dashboard.php" class="back-button">&larr; Retour</a>
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
                <label for="images">Images:</label>
                <input type="file" name="images[]" id="images" multiple required>
            </p>
            <p>
                <button type="submit">Ajouter</button>
            </p>
        </form>
    </section>
</body>
</html>
