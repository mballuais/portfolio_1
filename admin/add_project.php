<?php
session_start();

if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Informations de connexion à la base de données
    $host = 'localhost';sss
    $db = 'portfolio';
    $user = 'root';
    $pass = 'motdepasse';

    try {
        // Connexion à la base de données avec gestion des erreurs
        $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupérer les données du formulaire
        $title = $_POST['title'];
        $description = $_POST['description'];
        $technologies = $_POST['technologies'];
        $github = isset($_POST['github']) ? $_POST['github'] : null;

        // Préparer et exécuter la requête pour insérer le projet
        $query = $conn->prepare("INSERT INTO projects (title, description, technologies, github_link) VALUES (:title, :description, :technologies, :github_link)");
        $query->bindParam(':title', $title, PDO::PARAM_STR);
        $query->bindParam(':description', $description, PDO::PARAM_STR);
        $query->bindParam(':technologies', $technologies, PDO::PARAM_STR);
        $query->bindParam(':github_link', $github, PDO::PARAM_STR);
        $query->execute();

        // Récupérer l'ID du projet inséré
        $project_id = $conn->lastInsertId();

        // Gestion des images
        if (isset($_FILES['images']) && $_FILES['images']['error'][0] != UPLOAD_ERR_NO_FILE) {
            $image_names = $_FILES['images']['name'];
            $image_tmps = $_FILES['images']['tmp_name'];
            $image_errors = $_FILES['images']['error'];

            foreach ($image_names as $key => $image_name) {
                // Vérifier s'il y a une erreur lors du téléchargement
                if ($image_errors[$key] === UPLOAD_ERR_OK) {
                    $image_tmp = $image_tmps[$key];
                    $image_name = basename($image_name);
                    $target_directory = dirname(__DIR__) . '/images/';
                    $target_file = $target_directory . $image_name;

                    // Vérifier que le répertoire cible existe, sinon le créer
                    if (!file_exists($target_directory)) {
                        mkdir($target_directory, 0755, true);
                    }

                    // Déplacer le fichier téléchargé vers le répertoire cible
                    if (move_uploaded_file($image_tmp, $target_file)) {
                        // Enregistrer le nom de l'image dans la base de données
                        $image_query = $conn->prepare("INSERT INTO project_images (project_id, image) VALUES (:project_id, :image)");
                        $image_query->bindParam(':project_id', $project_id, PDO::PARAM_INT);
                        $image_query->bindParam(':image', $image_name, PDO::PARAM_STR);
                        $image_query->execute();
                    } else {
                        echo "Échec du téléchargement de l'image : $image_name";
                    }
                } else {
                    echo "Erreur lors du téléchargement de l'image : $image_name";
                }
            }
        }

        // Rediriger vers le tableau de bord après l'ajout
        header('Location: dashboard.php');
        exit();
    } catch (PDOException $e) {
        die("Erreur de connexion ou d'exécution : " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Projet</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- Vos autres liens CSS et scripts -->
</head>
<body>
    <header>
        <a href="dashboard.php" class="back-button">&larr; Retour</a>
        <h1>Ajouter un Projet</h1>
    </header>
    <section id="add-project">
        <form action="add_project.php" method="post" enctype="multipart/form-data">
            <p>
                <label for="title">Titre :</label>
                <input type="text" name="title" id="title" required>
            </p>
            <p>
                <label for="description">Description :</label>
                <textarea name="description" id="description" required></textarea>
            </p>
            <p>
                <label for="technologies">Technologies :</label>
                <input type="text" name="technologies" id="technologies" required>
            </p>
            <p>
                <label for="images">Images :</label>
                <input type="file" name="images[]" id="images" multiple required>
            </p>
            <p>
                <label for="github">Lien GitHub (facultatif) :</label>
                <input type="url" name="github" id="github" placeholder="https://github.com/votre-projet">
            </p>
            <p>
                <button type="submit">Ajouter</button>
            </p>
        </form>
    </section>
</body>
</html>
