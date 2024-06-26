<?php
session_start();

if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header('Location: login.php');
    exit();
}

$host = 'localhost';
$db = 'portfolio';
$user = 'root';
$pass = 'admin';

$conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);

$comment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$query = $conn->prepare("DELETE FROM comments WHERE id = :id");
$query->bindParam(':id', $comment_id, PDO::PARAM_INT);
$query->execute();

header('Location: dashboard.php');
exit();
