<?php
session_start();
require_once 'database.php';

// Initialisation des variables
$email = $matricule = $raison = "";

// Traitement du formulaire
if (isset($_POST['login-submit'])) {
    $email = $_POST['email'];
    $matricule = $_POST['matricule'];
    $raison = $_POST['Raison'];

    // Connexion à la base de données
    $db = Database::connect();

    // Vérification de l'utilisateur
    $stmt = $db->prepare("SELECT * FROM giai.personnel WHERE email = ? AND matricule = ?");
    $stmt->execute([$email, $matricule]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Récupération de la date et l'heure actuelles
        date_default_timezone_set("Africa/Douala");
        $heureActuelle = new DateTime();
        $heureReference = new DateTime('08:00');
        $statut = ($heureActuelle < $heureReference) ? 'ponctuel' : 'en retard';
        $difference = ($heureActuelle > $heureReference) ? $heureActuelle->diff($heureReference)->format('%h:%i') : '00:00';

        // Insertion dans la base de données selon la raison (arrivée ou départ)
        $stmt = $db->prepare("
            INSERT INTO giai.arrive (id_p, date_arrive, raison, statut, nb_abs) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$user['id_p'], $heureActuelle->format('Y-m-d H:i:s'), $raison, $statut, $difference]);

        // Définition des messages de session
        if ($raison === 'Arrivee') {
            $_SESSION['message'] = "Bonjour {$user['nom']} {$user['prenom']}, vous êtes $statut. Heure d'arrivée : {$heureActuelle->format('H:i')}.";
        } else {
            $_SESSION['message'] = "Bonsoir {$user['nom']} {$user['prenom']}, il est {$heureActuelle->format('H:i')}. Au revoir et bonne soirée.";
        }

        // Redirection vers la page principale
        header("Location: connperso.php");
        exit();
    } else {
        $_SESSION['error'] = "Matricule ou email incorrect.";
    }

    // Déconnexion de la base de données
    Database::disconnect();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Système de Gestion du Personnel - Login</title>
    <link rel="stylesheet" type="text/css" href="stylelogin.css">
</head>
<body>
<header>
    <nav>
        <h1>Système de Gestion du Personnel IAI</h1>
        <ul id="navli">
            <li><a class="homeblack" href="index.html">HOME</a></li>
            <li><a class="homered" href="connperso.php">Perso_Log</a></li>
            <li><a class="homeblack" href="connadmin.php">Chef_Log</a></li>
        </ul>
    </nav>
</header>
<div class="divider"></div>

<div class="loginbox">
    <img src="assets/avatar.png" class="avatar">
    <?php
    if (isset($_SESSION['message'])) {
        echo "<p style='color:green;'>{$_SESSION['message']}</p>";
        unset($_SESSION['message']);
    }

    if (isset($_SESSION['error'])) {
        echo "<p style='color:red;'>{$_SESSION['error']}</p>";
        unset($_SESSION['error']);
    }
    ?>
    <h1>Connexion</h1>
    <form action="" method="POST">
        <p>Email</p>
        <input type="email" name="email" placeholder="Entrez votre email" required pattern="^[A-Za-z]+@[A-Za-z]+\.[A-Za-z]{2,}$">
        <p>Matricule</p>
        <input type="text" name="matricule" placeholder="Entrez votre matricule" required>
        <p>Raison</p>
        <select name="Raison" required>
            <option disabled selected>Choisissez une raison</option>
            <option value="Arrivee">Arrivée</option>
            <option value="Depart">Départ</option>
        </select>
        <input type="submit" name="login-submit" value="Se connecter">
    </form>
    <p style="font-size: 14px; color: rgb(119, 119, 119); text-align: right;">
        Date d'aujourd'hui : <span><?php echo date('Y-m-d'); ?></span>
    </p>
</div>
</body>
</html>
