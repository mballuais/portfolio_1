<?php
$host = 'localhost';
$db = 'portfolio';
$user = 'root';
$pass = 'motdepasse';

try {
    // Connexion à la base de données avec gestion des erreurs
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
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

// Récupérer les images associées au projet
$images_query = $conn->prepare("SELECT * FROM project_images WHERE project_id = :project_id");
$images_query->bindParam(':project_id', $project_id, PDO::PARAM_INT);
$images_query->execute();
$images = $images_query->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les commentaires du projet
$comments_query = $conn->prepare("SELECT * FROM comments WHERE project_id = :id ORDER BY created_at DESC");
$comments_query->bindParam(':id', $project_id, PDO::PARAM_INT);
$comments_query->execute();
$comments = $comments_query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- Vos autres liens CSS et scripts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&display=swap">
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0"></script>
    <script src="../js/particules.js" defer></script> 
</head>
<body>
    <div id="particles-js"></div>
    <header class="header">
        <div class="container">
        </div>
        <h1 class="title"><?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
    </header>
    <div class="container content">
        <section id="project-details">
            <h2>Description</h2>
            <p><?php echo nl2br(htmlspecialchars($project['description'], ENT_QUOTES, 'UTF-8')); ?></p>
            <h2>Technologies utilisées</h2>
            <p><?php echo htmlspecialchars($project['technologies'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php if (!empty($project['github_link'])): ?>
                <h2>Code Source</h2>
                <p><a href="<?php echo htmlspecialchars($project['github_link'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank">Voir le projet sur GitHub</a></p>
            <?php endif; ?>
            <h2>Images</h2>
            <?php if (!empty($images)): ?>
                <div class="image-gallery">
                    <?php foreach ($images as $image): ?>
                        <img src="../images/<?php echo htmlspecialchars($image['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Aucune image disponible pour ce projet.</p>
            <?php endif; ?>
        </section>
        <section id="comments">
            <h2>Commentaires</h2>
            <?php if (!empty($comments)): ?>
                <ul class="comment-list">
                    <?php foreach ($comments as $comment): ?>
                        <li class="comment-item">
                            <p><strong><?php echo htmlspecialchars($comment['author'], ENT_QUOTES, 'UTF-8'); ?></strong> (<?php echo $comment['created_at']; ?>)</p>
                            <p><?php echo nl2br(htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8')); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Aucun commentaire pour ce projet.</p>
            <?php endif; ?>
            <h2>Ajouter un commentaire</h2>
            <form action="add_comment.php" method="post" id="comment-form">
                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                <div class="form-group">
                    <label for="author">Nom :</label>
                    <input type="text" name="author" id="author" required>
                </div>
                <div class="form-group">
                    <label for="content">Commentaire :</label>
                    <textarea name="content" id="content" required></textarea>
                </div>
                <button type="submit">Ajouter</button>
            </form>
        </section>
        <a href="../index.php" class="btn btn-return">Retour à l'accueil</a>
    </div>
</body>
</html>
