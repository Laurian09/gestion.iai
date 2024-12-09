<?php
session_start();
require_once 'database.php';

// Fonction pour calculer la distance entre deux points géographiques
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    // Rayon moyen de la Terre en mètres
    $earthRadius = 6371000;

    // Conversion des degrés en radians
    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);

    // Différences des coordonnées
    $dLat = $lat2 - $lat1;
    $dLon = $lon2 - $lon1;

    // Formule de Haversine
    $a = sin($dLat / 2) * sin($dLat / 2) +
         cos($lat1) * cos($lat2) *
         sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    // Distance en mètres
    return $earthRadius * $c;
}

$email = $matricule = $raison = "";

if (isset($_POST['login-submit'])) {
    // Récupération des données POST avec validation
    $email = $_POST['email'];
    $matricule =  $_POST['matricule'];
    $raison =  $_POST['Raison'];

     // Récupération des coordonnées géographiques
     $latitude = $_POST['latitude'];
     $longitude = $_POST['longitude'];
    // Connexion à la base de données
    $db = Database::connect();

    // Coordonnées de la structure (latitude et longitude connues à l'avance)
    $structureLatitude = 3.825330; // Exemple : Douala
    $structureLongitude = 11.48200;

    if ($email && $matricule && is_numeric($latitude) && is_numeric($longitude)) {
        // Calculer la distance
        $distance = calculateDistance($latitude, $longitude, $structureLatitude, $structureLongitude);

        // Vérifier si l'utilisateur est dans un rayon de 200 m
        if ($distance <= 200) {
            // Vérifier si l'utilisateur existe dans la table `personnel`
            if ($stmt = $db->prepare("SELECT id_p FROM giai.personnel WHERE email = ? AND matricule = ?")) {
                $stmt->bind_param("ss", $email, $matricule);
                if ($stmt->execute()) {
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        // Utilisateur trouvé
                        $row = $result->fetch_assoc();
                        $id_p = intval($row['id_p']); // Assurez-vous que c'est un entier

                        // Récupérer la date et l'heure actuelles
                        date_default_timezone_set("Africa/Douala");
                        $heureActuelle = new DateTime();
                        $heureReferenceArrivee = new DateTime('08:00');
                        $heureReferenceDepart = new DateTime('17:00');

                        // Définition des messages de session selon la raison (arrivée ou départ)
                        if ($raison === 'Arrivee') {
                            // Vérifier si l'utilisateur est en retard
                            $statut = ($heureActuelle < $heureReferenceArrivee) ? 'ponctuel' : 'en retard';
                            $difference = ($heureActuelle > $heureReferenceArrivee) ? 
                                $heureActuelle->diff($heureReferenceArrivee)->format('%h:%i') : '00:00';

                            // Insertion dans la base de données pour l'arrivée
                            if ($stmtInsert = $db->prepare("
                                INSERT INTO giai.arrive (id_p, date_arrive, raison, statut, nb_abs) 
                                VALUES (?, ?, ?, ?, ?)
                            ")) {
                                $stmtInsert->execute([$id_p, 
                                                      $heureActuelle->format('Y-m-d H:i:s'), 
                                                      'Arrivée', 
                                                      $statut, 
                                                      $difference]);
                                $_SESSION['message'] = "Bonjour {$row['nom']} {$row['prenom']}, vous êtes {$statut}. Heure d'arrivée : {$heureActuelle->format('H:i')}.";
                            }
                        } else {
                            // Gestion du départ
                            if ($heureActuelle < $heureReferenceDepart) {
                                $_SESSION['message'] = "Bonsoir {$row['nom']} {$row['prenom']}, il est {$heureActuelle->format('H:i')}. Au revoir et bonne soirée.";
                            } else {
                                $_SESSION['message'] = "Bonsoir {$row['nom']} {$row['prenom']}, il est {$heureActuelle->format('H:i')}. Au revoir et bonne soirée.";
                            }

                            // Insertion dans la base de données pour le départ
                            if ($stmtInsertDepart = $db->prepare("
                                INSERT INTO giai.arrive (id_p, date_arrive, raison, statut) 
                                VALUES (?, ?, ?, ?)
                            ")) {
                                // Statut pour le départ (peut être ajusté selon les besoins)
                                $_stmtInsertDepart->execute([$id_p,
                                                              date('Y-m-d H:i:s'),
                                                              'Départ',
                                                              'Départ']);
                            }
                        }

                        // Rediriger vers une autre page ou recharger la page pour afficher le message
                        header("Location: connperso.php");
                        exit();
                    } else {
                        $_SESSION['error'] = "Matricule ou email incorrect.";
                    }
                } else {
                    $_SESSION['error'] = "Erreur lors de l'exécution de la requête : " . htmlspecialchars($stmt->error);
                }
                $stmt->close();
            } else {
                $_SESSION['error'] = "Erreur lors de la préparation de la requête.";
            }
        } else {
            $_SESSION['error'] = "Vous êtes hors du rayon de 200 mètres de la structure.";
        }
        
        Database::disconnect();
        
    } else {
        $_SESSION['error'] = "Données invalides. Veuillez vérifier les informations envoyées.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion avec Géolocalisation</title>
</head>
<body>
<h1>Connexion avec Géolocalisation</h1>

<form id="geoForm" onsubmit="event.preventDefault(); connectUser();">
    <label for="email">Email :</label>
    <input type="email" name="email" id="email" required><br><br>

    <label for="matricule">Matricule :</label>
    <input type="text" name="matricule" id="matricule" required><br><br>

    <label for="Raison">Raison :</label>
    <select name="Raison" required>
        <option value="Arrivee">Arrivée</option>
        <option value="Depart">Départ</option>
    </select><br><br>

    <button type="submit">Connexion</button>
</form>

<script>
function connectUser() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            const email = document.getElementById('email').value;
            const matricule = document.getElementById('matricule').value;
            const raison = document.querySelector('select[name="Raison"]').value;

            const formData = new FormData();
            formData.append('email', email);
            formData.append('matricule', matricule);
            formData.append('Raison', raison);
            formData.append('latitude', latitude);
            formData.append('longitude', longitude);

            fetch('', { // Envoie les données au même fichier
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data); // Affiche le message de retour
            })
            .catch(error => {
                console.error('Erreur:', error);
            });
        }, function(error) {
            alert("Erreur de géolocalisation : " + error.message);
        });
    } else {
        alert("La géolocalisation n'est pas supportée par ce navigateur.");
    }
}
</script>
</body>
</html>