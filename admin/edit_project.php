<?php
session_start();

if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header('Location: login.php');
    exit();
}

// Connexion à la base de données
$host = 'localhost';
$db = 'portfolio';
$user = 'root';
$pass = 'admin';

$conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);

// Récupérer l'ID du projet depuis l'URL
$project_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Récupérer les informations du projet
$query = $conn->prepare("SELECT * FROM projects WHERE id = :id");
$query->bindParam(':id', $project_id, PDO::PARAM_INT);
$query->execute();
$project = $query->fetch(PDO::FETCH_ASSOC);

// Récupérer les images du projet
$images_query = $conn->prepare("SELECT * FROM project_images WHERE project_id = :project_id");
$images_query->bindParam(':project_id', $project_id, PDO::PARAM_INT);
$images_query->execute();
$images = $images_query->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer les informations du formulaire
    $title = $_POST['title'];
    $description = $_POST['description'];
    $technologies = $_POST['technologies'];

    // Mettre à jour le projet dans la base de données
    $query = $conn->prepare("UPDATE projects SET title = :title, description = :description, technologies = :technologies WHERE id = :id");
    $query->bindParam(':title', $title, PDO::PARAM_STR);
    $query->bindParam(':description', $description, PDO::PARAM_STR);
    $query->bindParam(':technologies', $technologies, PDO::PARAM_STR);
    $query->bindParam(':id', $project_id, PDO::PARAM_INT);
    $query->execute();

    // Supprimer les images sélectionnées
    if (isset($_POST['delete_images'])) {
        foreach ($_POST['delete_images'] as $delete_image_id) {
            // Récupérer le nom de l'image pour la supprimer du serveur
            $delete_image_query = $conn->prepare("SELECT image FROM project_images WHERE id = :id");
            $delete_image_query->bindParam(':id', $delete_image_id, PDO::PARAM_INT);
            $delete_image_query->execute();
            $delete_image = $delete_image_query->fetch(PDO::FETCH_ASSOC);

            if ($delete_image) {
                $image_path = "../images/" . $delete_image['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }

                // Supprimer l'image de la base de données
                $delete_image_query = $conn->prepare("DELETE FROM project_images WHERE id = :id");
                $delete_image_query->bindParam(':id', $delete_image_id, PDO::PARAM_INT);
                $delete_image_query->execute();
            }
        }
    }

    // Vérifier si de nouvelles images ont été téléchargées
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['name'] as $key => $image_name) {
            $image_tmp = $_FILES['images']['tmp_name'][$key];
            move_uploaded_file($image_tmp, "../images/$image_name");

            $image_query = $conn->prepare("INSERT INTO project_images (project_id, image) VALUES (:project_id, :image)");
            $image_query->bindParam(':project_id', $project_id, PDO::PARAM_INT);
            $image_query->bindParam(':image', $image_name, PDO::PARAM_STR);
            $image_query->execute();
        }
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
    <title>Modifier un Projet</title>
    <link rel="stylesheet" href="../css/style_edit.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&display=swap">
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0"></script>
    <script src="../js/particules.js" defer></script>
</head>
<body>
    <div id="particles-js"></div>
    <header>
        <a href="dashboard.php" class="back-button">&larr; Retour</a>
        <h1>Modifier un Projet</h1>
    </header>
    <section id="edit-project">
        <form action="edit_project.php?id=<?php echo $project_id; ?>" method="post" enctype="multipart/form-data">
            <p>
                <label for="title">Titre:</label>
                <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($project['title']); ?>" required>
            </p>
            <p>
                <label for="description">Description:</label>
                <textarea name="description" id="description" required><?php echo htmlspecialchars($project['description']); ?></textarea>
            </p>
            <p>
                <label for="technologies">Technologies:</label>
                <input type="text" name="technologies" id="technologies" value="<?php echo htmlspecialchars($project['technologies']); ?>" required>
            </p>
            <p>
                <label for="current_images">Images actuelles:</label><br>
                <?php foreach ($images as $image): ?>
                    <input type="checkbox" name="delete_images[]" value="<?php echo $image['id']; ?>"> Supprimer
                    <img src="../images/<?php echo htmlspecialchars($image['image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" width="200"><br>
                <?php endforeach; ?>
            </p>
            <p>
                <label for="images">Nouvelles images:</label>
                <input type="file" name="images[]" id="images" multiple>
            </p>
            <p>
                <button type="submit">Modifier</button>
            </p>
        </form>
    </section>
</body>
</html>
