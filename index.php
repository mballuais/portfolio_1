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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;700&family=Bebas+Neue&display=swap">
</head>


<body>
    <header class="header">
        <div class="container">
            <div class="logo">
                <a href="#"><img src="images/logo.png" alt="Logo"></a>
            </div>
            <nav>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="#projects">Projets</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">À propos</a></li>
                    <li class="nav-item"><a class="nav-link" href="#skills">Compétences</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    <li class="nav-item admin-login"><a class="nav-link" href="admin/login.php">Connexion Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <section id="main">
        <div id="particles-js" class="particles"></div> <!-- Div pour les particules -->
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
                <p>Bienvenue sur mon site dédié à l’univers du digital ! Je suis Mattéo BALLUAIS, étudiant en Chef de Projet Digital à la Normandie Web School. Explorez avec moi le mariage entre passion et programmation. Découvrez comment mes compétences en développement web prennent vie à travers des projets concrets.</p>
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
                    <p>Mon expertise me permet de créer des sites web interactifs, esthétiques et fonctionnels. Que ce soit pour concevoir des interfaces utilisateur intuitives, optimiser les performances ou assurer la compatibilité multiplateforme, je m’engage à fournir des solutions web de qualité, alignées sur les dernières tendances technologiques.</p>
                </div>
                <div class="skill">
                    <img src="images/project-management.png" alt="Gestion de projet">
                    <h3>Gestion de projet</h3>
                    <p>Doté d’une solide compétence en gestion de projets digitaux, je coordonne avec succès des initiatives numériques, de la planification à la réalisation. Ma méthodologie efficace et ma compréhension approfondie des tendances digitales garantissent la réussite des projets, en optimisant la visibilité en ligne, l’expérience utilisateur et les objectifs commerciaux</p>
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
                        <p><?php echo htmlspecialchars($project['skills']); ?></p>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.js"></script>
    <script src="js/scriptindex.js"></script> 
</body>
</html>
