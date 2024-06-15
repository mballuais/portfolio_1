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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer les informations du formulaire
    $title = $_POST['title'];
    $description = $_POST['description'];
    $technologies = $_POST['technologies'];
    $image = $project['image'];

    // Vérifier si une nouvelle image a été téléchargée
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        move_uploaded_file($image_tmp, "../images/$image");
    }

    // Mettre à jour le projet dans la base de données
    $query = $conn->prepare("UPDATE projects SET title = :title, description = :description, technologies = :technologies, image = :image WHERE id = :id");
    $query->bindParam(':title', $title, PDO::PARAM_STR);
    $query->bindParam(':description', $description, PDO::PARAM_STR);
    $query->bindParam(':technologies', $technologies, PDO::PARAM_STR);
    $query->bindParam(':image', $image, PDO::PARAM_STR);
    $query->bindParam(':id', $project_id, PDO::PARAM_INT);
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
    <title>Modifier un Projet</title>
    <link rel="stylesheet" href="../css/style_edit.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Oswald:wght@300;400;500;700&display=swap">
</head>
<body>
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
                <label for="image">Image actuelle:</label>
                <img src="../images/<?php echo htmlspecialchars($project['image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" width="200">
                <br>
                <label for="image">Nouvelle image:</label>
                <input type="file" name="image" id="image">
            </p>
            <p>
                <button type="submit">Modifier</button>
            </p>
        </form>
    </section>
</body>
</html>
