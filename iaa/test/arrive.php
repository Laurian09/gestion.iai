<?php/*
session_start();
require_once 'database.php';

if (isset($_POST['login-submit'])) {
    $email = $_POST['email'];
    $matricule = $_POST['matricule'];
    $raison = $_POST['raison'];
    
    // Connexion à la base de données
    $db = Database::connect();
    
    // Vérifier si l'utilisateur existe
    $stmt = $db->prepare("SELECT * FROM giai.personnel WHERE email = ? AND matricule = ?");
    $stmt->execute([$email, $matricule]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Récupérer la date et l'heure actuelles
        date_default_timezone_set("Africa/Douala");
        $heureArrivee = new DateTime();
        $heureReference = new DateTime('08:00');
        $statut = ($heureArrivee < $heureReference) ? 'ponctuel' : 'retard';

        // Calculer la différence en heures si l'utilisateur est en retard
        $difference = ($heureArrivee > $heureReference) ? $heureArrivee->diff($heureReference)->format('%h:%i') : '00:00';

        // Insérer les informations dans la table arrive
        $stmt = $db->prepare("INSERT INTO giai.arrive (id_p, date_arrive, raison, statut, nb_abs) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user['id_p'], $heureArrivee->format('Y-m-d H:i:s'), $raison, $statut, $difference]);

        // Définir une session pour l'affichage des informations
        $_SESSION['message'] = "Bonjour {$user['nom']} {$user['prenom']}, vous êtes $statut. Heure d'arrivée : {$heureArrivee->format('H:i')}.";

        // Rediriger vers une autre page ou recharger la page pour afficher le message
        header("Location: arrive.php");
        exit();
    } else {
        $_SESSION['error'] = "Matricule ou email incorrect.";
    }

    Database::disconnect();
}
?>

<!-- HTML de la page de connexion -->
<!DOCTYPE html>
<html>
<head>
    <title>Connexion</title>
</head>
<body>
    <?php
    // Afficher un message de bienvenue si l'utilisateur est connecté avec succès
    if (isset($_SESSION['message'])) {
        echo "<script>alert('{$_SESSION['message']}');</script>";
        unset($_SESSION['message']);
    }

    // Afficher un message d'erreur si les informations sont incorrectes
    if (isset($_SESSION['error'])) {
        echo "<p style='color:red;'>{$_SESSION['error']}</p>";
        unset($_SESSION['error']);
    }
    ?>
    <form action="" method="POST">
        <label for="email">Email :</label>
        <input type="text" name="email" required><br>
        
        <label for="matricule">Matricule :</label>
        <input type="text" name="matricule" required><br>
        
        <label for="raison">Raison :</label>
        <input type="text" name="raison" required><br>
        
        <button type="submit" name="login-submit">Se connecter</button>
    </form>
</body>
</html>