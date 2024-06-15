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

// Récupérer les projets
$projects_query = $conn->query("SELECT * FROM projects");
$projects = $projects_query->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les commentaires
$comments_query = $conn->query("SELECT * FROM comments");
$comments = $comments_query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../css/dashboard_css.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Oswald:wght@300;400;500;700&display=swap">
</head>
<body>
    <header class="navbar">
        <div class="container">
            <h1 class="navbar-brand">Dashboard Admin</h1>
            <a class="btn btn-outline-light" href="logout.php">Déconnexion</a>
        </div>
    </header>
    <div class="container">
        <section id="projects">
            <div class="section-header">
                <h2>Projets</h2>
                <a class="btn btn-success" href="add_project.php">Ajouter un nouveau projet</a>
            </div>
            <ul class="list-group">
                <?php foreach ($projects as $project): ?>
                    <li class="list-group-item">
                        <span><?php echo htmlspecialchars($project['title']); ?></span>
                        <div class="btn-group">
                            <a class="btn btn-sm btn-primary" href="edit_project.php?id=<?php echo $project['id']; ?>">Modifier</a>
                            <a class="btn btn-sm btn-danger" href="delete_project.php?id=<?php echo $project['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?');">Supprimer</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <section id="comments">
            <h2>Commentaires</h2>
            <ul class="list-group">
                <?php foreach ($comments as $comment): ?>
                    <li class="list-group-item">
                        <div>
                            <strong><?php echo htmlspecialchars($comment['author']); ?></strong> : <?php echo htmlspecialchars($comment['content']); ?>
                        </div>
                        <a class="btn btn-sm btn-danger" href="delete_comment.php?id=<?php echo $comment['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?');">Supprimer</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </div>
</body>
</html>
