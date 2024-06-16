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

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer l'ID du projet depuis l'URL
    $project_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // Supprimer les images associées au projet
    $delete_images = $conn->prepare("DELETE FROM project_images WHERE project_id = :project_id");
    $delete_images->bindParam(':project_id', $project_id, PDO::PARAM_INT);
    $delete_images->execute();

    // Supprimer les commentaires associés au projet
    $delete_comments = $conn->prepare("DELETE FROM comments WHERE project_id = :project_id");
    $delete_comments->bindParam(':project_id', $project_id, PDO::PARAM_INT);
    $delete_comments->execute();

    // Supprimer le projet de la base de données
    $delete_project = $conn->prepare("DELETE FROM projects WHERE id = :id");
    $delete_project->bindParam(':id', $project_id, PDO::PARAM_INT);
    $delete_project->execute();

    // Rediriger vers le dashboard
    header('Location: dashboard.php');
    exit();

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
