<?php
session_start();

if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header('Location: login.php');
    exit();
}

$host = 'localhost';
$db = 'portfolio';
$user = 'root';
$pass = 'motdepasse';

try {
    // Connexion à la base de données avec gestion des erreurs
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer l'ID du projet depuis l'URL
    $project_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // Vérifier que l'ID du projet est valide
    if ($project_id <= 0) {
        die("ID de projet invalide.");
    }

    // Récupérer les informations du projet
    $query = $conn->prepare("SELECT * FROM projects WHERE id = :id");
    $query->bindParam(':id', $project_id, PDO::PARAM_INT);
    $query->execute();
    $project = $query->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        die("Projet non trouvé.");
    }

    // Récupérer les images associées au projet
    $images_query = $conn->prepare("SELECT * FROM project_images WHERE project_id = :project_id");
    $images_query->bindParam(':project_id', $project_id, PDO::PARAM_INT);
    $images_query->execute();
    $images = $images_query->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Récupérer les données du formulaire
        $title = $_POST['title'];
        $description = $_POST['description'];
        $technologies = $_POST['technologies'];
        $github = isset($_POST['github']) ? $_POST['github'] : null;

        // Mettre à jour le projet dans la base de données
        $query = $conn->prepare("UPDATE projects SET title = :title, description = :description, technologies = :technologies, github_link = :github_link WHERE id = :id");
        $query->bindParam(':title', $title, PDO::PARAM_STR);
        $query->bindParam(':description', $description, PDO::PARAM_STR);
        $query->bindParam(':technologies', $technologies, PDO::PARAM_STR);
        $query->bindParam(':github_link', $github, PDO::PARAM_STR);
        $query->bindParam(':id', $project_id, PDO::PARAM_INT);
        $query->execute();

        // Gestion de la suppression des images
        if (isset($_POST['delete_images'])) {
            foreach ($_POST['delete_images'] as $delete_image_id) {
                $delete_image_query = $conn->prepare("SELECT image FROM project_images WHERE id = :id");
                $delete_image_query->bindParam(':id', $delete_image_id, PDO::PARAM_INT);
                $delete_image_query->execute();
                $delete_image = $delete_image_query->fetch(PDO::FETCH_ASSOC);

                if ($delete_image) {
                    $image_path = dirname(__DIR__) . "/images/" . $delete_image['image'];
                    if (file_exists($image_path)) {
                        unlink($image_path);
                    }

                    $delete_image_query = $conn->prepare("DELETE FROM project_images WHERE id = :id");
                    $delete_image_query->bindParam(':id', $delete_image_id, PDO::PARAM_INT);
                    $delete_image_query->execute();
                }
            }
        }

        // Gestion de l'ajout de nouvelles images
        if (!empty($_FILES['images']['name'][0])) {
            $image_names = $_FILES['images']['name'];
            $image_tmps = $_FILES['images']['tmp_name'];
            $image_errors = $_FILES['images']['error'];

            foreach ($image_names as $key => $image_name) {
                if ($image_errors[$key] === UPLOAD_ERR_OK) {
                    $image_tmp = $image_tmps[$key];
                    $image_name = basename($image_name);
                    $target_directory = dirname(__DIR__) . '/images/';
                    $target_file = $target_directory . $image_name;

                    // Vérifier que le répertoire cible existe
                    if (!file_exists($target_directory)) {
                        mkdir($target_directory, 0755, true);
                    }

                    // Déplacer le fichier téléchargé
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

        header('Location: dashboard.php');
        exit();
    }
} catch (PDOException $e) {
    die("Erreur de connexion ou d'exécution : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un Projet</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- Vos autres liens CSS et scripts -->
</head>
<body>
    <header>
        <a href="dashboard.php" class="back-button">&larr; Retour</a>
        <h1>Modifier un Projet</h1>
    </header>
    <section id="edit-project">
        <form action="edit_project.php?id=<?php echo $project_id; ?>" method="post" enctype="multipart/form-data">
            <p>
                <label for="title">Titre :</label>
                <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($project['title']); ?>" required>
            </p>
            <p>
                <label for="description">Description :</label>
                <textarea name="description" id="description" required><?php echo htmlspecialchars($project['description']); ?></textarea>
            </p>
            <p>
                <label for="technologies">Technologies :</label>
                <input type="text" name="technologies" id="technologies" value="<?php echo htmlspecialchars($project['technologies']); ?>" required>
            </p>
            <p>
                <label for="github">Lien GitHub (facultatif) :</label>
                <input type="url" name="github" id="github" value="<?php echo htmlspecialchars($project['github_link']); ?>">
            </p>
            <p>
                <label>Images actuelles :</label><br>
                <?php foreach ($images as $image): ?>
                    <div class="image-item">
                        <input type="checkbox" name="delete_images[]" value="<?php echo $image['id']; ?>"> Supprimer
                        <img src="../images/<?php echo htmlspecialchars($image['image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" width="200"><br>
                    </div>
                <?php endforeach; ?>
            </p>
            <p>
                <label for="images">Nouvelles images :</label>
                <input type="file" name="images[]" id="images" multiple>
            </p>
            <p>
                <button type="submit">Modifier</button>
            </p>
        </form>
    </section>
</body>
</html>
