<?php
// Connexion à la base de données
$host = 'localhost';
$db = 'portfolio';
$user = 'root';
$pass = 'admin';

$conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);

// Récupérer les données du formulaire
$project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
$author = isset($_POST['author']) ? trim($_POST['author']) : '';
$content = isset($_POST['content']) ? trim($_POST['content']) : '';

// Ajouter le commentaire à la base de données
if ($project_id > 0 && !empty($author) && !empty($content)) {
    $query = $conn->prepare("INSERT INTO comments (project_id, author, content) VALUES (:project_id, :author, :content)");
    $query->bindParam(':project_id', $project_id, PDO::PARAM_INT);
    $query->bindParam(':author', $author, PDO::PARAM_STR);
    $query->bindParam(':content', $content, PDO::PARAM_STR);
    $query->execute();
}

// Rediriger vers la page du projet
header("Location: project.php?id=$project_id");
exit();
