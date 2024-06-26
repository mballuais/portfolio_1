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

// Initialiser les variables pour les messages d'erreur
$titleErr = $subtitleErr = $contentErr = "";
$title = $subtitle = $content = "";

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["title"])) {
        $titleErr = "Le titre est requis";
    } else {
        $title = $_POST["title"];
    }

    if (empty($_POST["subtitle"])) {
        $subtitleErr = "Le sous-titre est requis";
    } else {
        $subtitle = $_POST["subtitle"];
    }

    if (empty($_POST["content"])) {
        $contentErr = "Le contenu est requis";
    } else {
        $content = $_POST["content"];
    }

    // Si aucun champ n'est vide, mettre à jour la base de données
    if ($title && $subtitle && $content) {
        $sql = "UPDATE homepage_content SET title=:title, subtitle=:subtitle, content=:content WHERE id=1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':subtitle', $subtitle);
        $stmt->bindParam(':content', $content);

        if ($stmt->execute()) {
            $successMsg = "Informations mises à jour avec succès";
        } else {
            $errorMsg = "Erreur lors de la mise à jour : " . $stmt->errorInfo()[2];
        }
    }
}

// Récupérer les informations actuelles de la page d'accueil
$result = $conn->query("SELECT * FROM homepage_content LIMIT 1");
$row = $result->fetch(PDO::FETCH_ASSOC);

// Initialiser les valeurs par défaut si aucune donnée n'est trouvée
if (!$row) {
    $row = [
        'title' => '',
        'subtitle' => '',
        'content' => ''
    ];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modifier la page d'accueil</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&display=swap">
    <link rel="stylesheet" href="../css/style_edit_home.css">
</head>
<body>
    <header>
        <h1>Modifier la page d'accueil</h1>
        <a class="back-button" href="dashboard.php">Retour</a>
    </header>
    <?php
    if (isset($successMsg)) {
        echo "<p style='color:green;'>$successMsg</p>";
    }
    if (isset($errorMsg)) {
        echo "<p style='color:red;'>$errorMsg</p>";
    }
    ?>
    <div id="add-project">
        <form action="admin_homepage_edit.php" method="POST">
            <label for="title">Titre :</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($row['title']); ?>">
            <span style="color:red;"><?php echo $titleErr;?></span><br>
            
            <label for="subtitle">Sous-titre :</label>
            <input type="text" id="subtitle" name="subtitle" value="<?php echo htmlspecialchars($row['subtitle']); ?>">
            <span style="color:red;"><?php echo $subtitleErr;?></span><br>
            
            <label for="content">Contenu :</label>
            <textarea id="content" name="content"><?php echo htmlspecialchars($row['content']); ?></textarea>
            <span style="color:red;"><?php echo $contentErr;?></span><br>
            
            <button type="submit">Mettre à jour</button>
        </form>
    </div>
    <div id="particles-js"></div>
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script src="../js/particules.js"></script>
</body>
</html>

<?php
$conn = null;
?>
