<?  /* php
// Importe la classe Database
require_once 'database.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupère les données du formulaire
    $email = $_POST['email'];
    $matricule = $_POST['matricule'];
    $raison = $_POST['raison'];

    // Validation des entrées
    if (!empty($email) && !empty($matricule)) {
        // Connexion à la base de données
        $db = Database::connect();

        // Préparation de la requête SQL pour vérifier si l'utilisateur existe
        $stmt = $db->prepare("SELECT * FROM giai.personnel WHERE email = :email AND matricule = :matricule");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':matricule', $matricule, PDO::PARAM_STR);

        $stmt->execute();

        // Récupère les résultats de la requête
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Si l'utilisateur est trouvé, démarrer une session et rediriger
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['matricule'] = $user['matricule'];
            $_SESSION['raison'] = $raison;

            // Redirection vers la page principale ou tableau de bord
            header("Location: dashboard.php");
            exit;
        } else {
            // Affiche un message d'erreur si l'email ou le matricule est incorrect
            $error = "Email ou matricule incorrect.";
        }

        // Fermer la connexion
        Database::disconnect();
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>
<!--
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
</head>
<body>
    <? /*php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
    <a href="connperso.php">Retour</a>
</body>
</html>




















<!--/*php
// Importe la classe Database
require_once 'database.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupère les données du formulaire
    $email = $_POST['email'];
    $matricule = $_POST['matricule'];
    $raison = $_POST['raison'];

    // Validation des entrées
    if (!empty($email) && !empty($matricule)) {
        // Connexion à la base de données
        $db = Database::connect();

        // Préparation de la requête SQL pour vérifier si l'utilisateur existe
        $stmt = $db->prepare("SELECT * FROM giai.personnel WHERE email = :email AND matricule = :matricule");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':matricule', $matricule, PDO::PARAM_STR);

        $stmt->execute();

        // Récupère les résultats de la requête
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Si l'utilisateur est trouvé, démarrer une session et rediriger
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['matricule'] = $user['matricule'];
            $_SESSION['raison'] = $raison;

            // Redirection vers la page principale ou tableau de bord
            header("Location: dashboard.php");
            exit;
        } else {
            // Affiche un message d'erreur si l'email ou le matricule est incorrect
            $error = "Email ou matricule incorrect.";
        }

        // Fermer la connexion
        Database::disconnect();
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}*/


/*
// Importe la classe Database
require_once 'database.php';

session_start();

$error = ""; // Variable pour stocker le message d'erreur

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupère les données du formulaire
    $email = $_POST['email'];
    $matricule = $_POST['matricule'];
    $raison = $_POST['raison'];

    // Validation des entrées
    if (!empty($email) && !empty($matricule)) {
        // Connexion à la base de données
        $db = Database::connect();

        // Préparation de la requête SQL pour vérifier si l'utilisateur existe
        $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE email = :email AND matricule = :matricule");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->execute();

        // Récupère les résultats de la requête
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Si l'utilisateur est trouvé, récupérer ses informations personnelles
            $stmt_personnel = $db->prepare("SELECT nom, prenom FROM personnel WHERE id = :user_id");
            $stmt_personnel->bindParam(':user_id', $user['id'], PDO::PARAM_INT);
            $stmt_personnel->execute();
            $personnel = $stmt_personnel->fetch(PDO::FETCH_ASSOC);

            if ($personnel) {
                // Récupère l'heure actuelle
                date_default_timezone_set('Africa/Douala'); // Définir le fuseau horaire
                $heure_arrivee = new DateTime();
                $heure_limite = new DateTime('08:00');
                
                // Calcul du statut et de la différence d'heure
                $statut = $heure_arrivee <= $heure_limite ? "Ponctuel" : "Retard";
                $diff = $heure_arrivee > $heure_limite ? $heure_arrivee->diff($heure_limite)->format('%H:%I') : "00:00";

                // Insérer les données dans la table 'arrivee'
                $stmt_insert = $db->prepare("INSERT INTO arrivee (user_id, heure_arrivee, raison, statut, difference_heure) VALUES (:user_id, :heure_arrivee, :raison, :statut, :difference_heure)");
                $stmt_insert->bindParam(':user_id', $user['id'], PDO::PARAM_INT);
                $stmt_insert->bindParam(':heure_arrivee', $heure_arrivee->format('H:i:s'), PDO::PARAM_STR);
                $stmt_insert->bindParam(':raison', $raison, PDO::PARAM_STR);
                $stmt_insert->bindParam(':statut', $statut, PDO::PARAM_STR);
                $stmt_insert->bindParam(':difference_heure', $diff, PDO::PARAM_STR);
                $stmt_insert->execute();

                // Stocker les informations dans la session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['matricule'] = $user['matricule'];
                $_SESSION['raison'] = $raison;

                // Affichage du message de bienvenue
                echo "<!-<script>
                    alert('Bienvenue " . htmlspecialchars($personnel['nom']) . " " . htmlspecialchars($personnel['prenom']) . "! Vous êtes " . $statut . ". Heure d\'arrivée : " . $heure_arrivee->format('H:i') . ".');
                    window.location.href = 'dashboard.php';
                </script>";--
                exit;
            } else {
                $error = "Informations utilisateur introuvables dans la base de données.";
            }
        } else {
            // Message d'erreur si l'email ou le matricule est incorrect
            $error = "Email ou matricule incorrect.";
        }

        // Fermer la connexion
        Database::disconnect();
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}*/
-->