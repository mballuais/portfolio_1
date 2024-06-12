<?php
// Connexion à la base de données
$host = 'localhost';
$db = 'portfolio';
$user = 'root';
$pass = 'admin';

$conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);

// Récupérer les informations de l'utilisateur
$query = $conn->prepare("SELECT * FROM users WHERE username = 'admin'");
$query->execute();
$user_info = $query->fetch(PDO::FETCH_ASSOC);

// Récupérer les projets
$projects_query = $conn->query("SELECT * FROM projects");
$projects = $projects_query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio - Matteo Balluais</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <img src="images/logo.png" alt="Logo">
            </div>
            <nav>
                <ul>
                    <li><a href="#projects">Projets</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <section id="main">
        <div class="background-image"></div>
        <div class="main-content">
            <h1>Matteo Balluais</h1>
            <p>Développeur Web</p>
        </div>
    </section>
    <section id="about">
        <div class="container">
            <div class="profile-photo">
                <img src="images/photo.jpg" alt="Ma photo">
            </div>
            <div class="bio">
                <h2>Bienvenue sur mon portfolio</h2>
                <p>Je suis Matteo Balluais, étudiant en Chef de Projet Digital à la Normandie Web School. Explorez avec moi le mariage entre passion et programmation.</p>
                <a href="#projects" class="btn">En savoir plus</a>
            </div>
        </div>
    </section>
    <section id="skills">
        <div class="container">
            <h2>Ce que je sais faire</h2>
            <div class="skills-grid">
                <div class="skill">
                    <img src="images/web-development.png" alt="Développement Web">
                    <h3>Développement Web</h3>
                    <p>Création de sites web interactifs, esthétiques et fonctionnels.</p>
                </div>
                <div class="skill">
                    <img src="images/project-management.png" alt="Gestion de projet">
                    <h3>Gestion de projet</h3>
                    <p>Coordination des initiatives numériques de la planification à la réalisation.</p>
                </div>
            </div>
        </div>
    </section>
    <section id="projects">
        <div class="container">
            <h2>Mes Projets</h2>
            <div class="projects-grid">
                <?php foreach ($projects as $project): ?>
                    <div class="project-item">
                        <a href="project/project.php?id=<?php echo $project['id']; ?>">
                            <img src="images/<?php echo htmlspecialchars($project['image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
                            <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <section id="contact">
        <div class="container">
            <h2>Intéressé par mon profil?</h2>
            <p>N'hésitez pas à prendre contact</p>
            <a href="mailto:contact@matteoballuais.fr" class="btn">Contactez-moi</a>
            <p class="contact-info">Contact: matteoballuais@normandiewebschool.fr</p>
        </div>
    </section>
    <footer>
        <div class="container">
            <p>&copy; 2023 Matteo Balluais. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
