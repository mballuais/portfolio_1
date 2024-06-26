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
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    exit();
}

// Récupérer les données du formulaire
$title = $_POST['title'];
$subtitle = $_POST['subtitle'];
$content = $_POST['content'];

// Mettre à jour la table homepage_content
$sql = "UPDATE homepage_content SET title=:title, subtitle=:subtitle, content=:content WHERE id=1";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':title', $title);
$stmt->bindParam(':subtitle', $subtitle);
$stmt->bindParam(':content', $content);

if ($stmt->execute()) {
    echo "Informations mises à jour avec succès";
} else {
    echo "Erreur lors de la mise à jour : " . $stmt->errorInfo()[2];
}

$conn = null;
?>
