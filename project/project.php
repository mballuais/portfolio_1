<?php
$host = 'localhost';
$db = 'portfolio';
$user = 'root';
$pass = 'motdepasse';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$project_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$query = $conn->prepare("SELECT * FROM projects WHERE id = :id");
$query->bindParam(':id', $project_id, PDO::PARAM_INT);
$query->execute();
$project = $query->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    die("Projet non trouvé.");
}

$images_query = $conn->prepare("SELECT * FROM project_images WHERE project_id = :project_id");
$images_query->bindParam(':project_id', $project_id, PDO::PARAM_INT);
$images_query->execute();
$images = $images_query->fetchAll(PDO::FETCH_ASSOC);

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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&display=swap">
</head>
<body>
    <header class="header">
        <div class="container">
        </div>
        <h1 class="title"><?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
    </header>
    <div class="container content">
        <section id="project-details">
            <h2>Description</h2>
            <p><?php echo htmlspecialchars($project['description'], ENT_QUOTES, 'UTF-8'); ?></p>
            <h2>Technologies utilisées</h2>
            <p><?php echo htmlspecialchars($project['technologies'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php if (!empty($project['github_link'])): ?>
            <h2>Code Source</h2>
            <p><a href="<?php echo htmlspecialchars($project['github_link'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank">Voir sur GitHub</a></p>
            <?php endif; ?>
            <h2>Images</h2>
            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    <?php foreach ($images as $index => $image): ?>
                        <li data-target="#carouselExampleIndicators" data-slide-to="<?php echo $index; ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>"></li>
                    <?php endforeach; ?>
                </ol>
                <div class="carousel-inner">
                    <?php foreach ($images as $index => $image): ?>
                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <img src="../images/<?php echo htmlspecialchars($image['image'], ENT_QUOTES, 'UTF-8'); ?>" class="d-block w-100" alt="<?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
                <a class="carousel-control-prev custom-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next custom-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </section>
        <section id="comments">
            <h2>Commentaires</h2>
            <ul class="comment-list">
                <?php foreach ($comments as $comment): ?>
                    <li class="comment-item">
                        <p><strong><?php echo htmlspecialchars($comment['author'], ENT_QUOTES, 'UTF-8'); ?></strong> (<?php echo $comment['created_at']; ?>)</p>
                        <p><?php echo htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
            <h2>Ajouter un commentaire</h2>
            <form action="add_comment.php" method="post" id="comment-form">
                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                <div class="form-group">
                    <label for="author">Nom:</label>
                    <input type="text" name="author" id="author" required>
                </div>
                <div class="form-group">
                    <label for="content">Commentaire:</label>
                    <textarea name="content" id="content" required></textarea>
                </div>
                <button type="submit">Ajouter</button>
            </form>
        </section>
        <a href="../" class="btn btn-return">Retour à l'accueil</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
