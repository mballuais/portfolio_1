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
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Dashboard Admin</h1>
        <a href="logout.php">Déconnexion</a>
    </header>
    <section id="projects">
        <h2>Projets</h2>
        <a href="add_project.php">Ajouter un nouveau projet</a>
        <ul>
            <?php foreach ($projects as $project): ?>
                <li>
                    <?php echo htmlspecialchars($project['title']); ?>
                    <a href="edit_project.php?id=<?php echo $project['id']; ?>">Modifier</a>
                    <a href="delete_project.php?id=<?php echo $project['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?');">Supprimer</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <section id="comments">
        <h2>Commentaires</h2>
        <ul>
            <?php foreach ($comments as $comment): ?>
                <li>
                    <?php echo htmlspecialchars($comment['author']); ?> : <?php echo htmlspecialchars($comment['content']); ?>
                    <a href="delete_comment.php?id=<?php echo $comment['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?');">Supprimer</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</body>
</html>
