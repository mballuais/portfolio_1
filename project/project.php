<?php
// Connexion à la base de données
$host = 'localhost';
$db = 'portfolio';
$user = 'root';
$pass = 'admin';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupérer l'ID du projet depuis l'URL
$project_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Récupérer les informations du projet
$query = $conn->prepare("SELECT * FROM projects WHERE id = :id");
$query->bindParam(':id', $project_id, PDO::PARAM_INT);
$query->execute();
$project = $query->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    die("Projet non trouvé.");
}

// Récupérer les commentaires pour le projet
$comments_query = $conn->prepare("SELECT * FROM comments WHERE project_id = :id ORDER BY created_at DESC");
$comments_query->bindParam(':id', $project_id, PDO::PARAM_INT);
$comments_query->execute();
$comments = $comments_query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/script.js" defer></script>
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
    </header>
    <section id="project-details" class="container">
        <h2>Description</h2>
        <p><?php echo htmlspecialchars($project['description'], ENT_QUOTES, 'UTF-8'); ?></p>
        <h2>Technologies utilisées</h2>
        <p><?php echo htmlspecialchars($project['technologies'], ENT_QUOTES, 'UTF-8'); ?></p>
        <h2>Image</h2>
        <img src="../images/<?php echo htmlspecialchars($project['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?>">
    </section>
    <section id="comments" class="container">
        <h2>Commentaires</h2>
        <ul>
            <?php foreach ($comments as $comment): ?>
                <li>
                    <p><strong><?php echo htmlspecialchars($comment['author'], ENT_QUOTES, 'UTF-8'); ?></strong> (<?php echo $comment['created_at']; ?>)</p>
                    <p><?php echo htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8'); ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
        <h2>Ajouter un commentaire</h2>
        <form action="add_comment.php" method="post" id="comment-form">
            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
            <p><label for="author">Nom:</label> <input type="text" name="author" id="author" required></p>
            <p><label for="content">Commentaire:</label> <textarea name="content" id="content" required></textarea></p>
            <p><button type="submit">Ajouter</button></p>
        </form>
    </section>
</body>
</html>