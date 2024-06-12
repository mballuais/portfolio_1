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

// Récupérer l'ID du commentaire depuis l'URL
$comment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Supprimer le commentaire de la base de données
$query = $conn->prepare("DELETE FROM comments WHERE id = :id");
$query->bindParam(':id', $comment_id, PDO::PARAM_INT);
$query->execute();

// Rediriger vers le dashboard
header('Location: dashboard.php');
exit();
