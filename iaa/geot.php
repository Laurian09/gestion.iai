<?php
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Connexion à la base de données
    $host = "127.0.0.1";
    $user = "root";
    $password = "";
    $dbname = "giai"; // Remplacez par le nom de votre base de données

    $conn = new mysqli($host, $user, $password, $dbname);

    // Vérification de la connexion
    if ($conn->connect_error) {
        die("Connexion échouée : " . htmlspecialchars($conn->connect_error));
    }

    // Récupération des données POST avec validation
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $matricule = filter_input(INPUT_POST, 'matricule', FILTER_SANITIZE_STRING);
    $latitude = filter_input(INPUT_POST, 'latitude', FILTER_VALIDATE_FLOAT);
    $longitude = filter_input(INPUT_POST, 'longitude', FILTER_VALIDATE_FLOAT);

    // Coordonnées de la structure (latitude et longitude connues à l'avance)
    $structureLatitude = 3.825330; // Exemple : Douala
    $structureLongitude = 11.48200;

    if ($email && $matricule && is_numeric($latitude) && is_numeric($longitude)) {
        // Calculer la distance
        $distance = calculateDistance($latitude, $longitude, $structureLatitude, $structureLongitude);

        // Vérifier si l'utilisateur est dans un rayon de 200 m
        if ($distance <= 200) {
            // Vérifier si l'utilisateur existe dans la table `personnel`
            if ($stmt = $conn->prepare("SELECT id_p FROM personnel WHERE email = ? AND matricule = ?")) {
                $stmt->bind_param("ss", $email, $matricule);
                if ($stmt->execute()) {
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        // Utilisateur trouvé
                        $row = $result->fetch_assoc();
                        $id_p = intval($row['id_p']); // Assurez-vous que c'est un entier

                        // Enregistrer dans la table `arrive`
                        if ($stmtInsert = $conn->prepare("INSERT INTO arrive (id_p, latitude, longitude, date_arrive) VALUES (?, ?, ?, NOW())")) {
                            if ($stmtInsert->bind_param("idd", $id_p, floatval($latitude), floatval($longitude)) && 
                                $stmtInsert->execute()) {
                                echo "Coordonnées enregistrées avec succès.";
                            } else {
                                echo "Erreur lors de l'enregistrement : " . htmlspecialchars($stmtInsert->error);
                            }
                            $stmtInsert->close();
                        } else {
                            echo "Erreur lors de la préparation de l'insertion.";
                        }
                    } else {
                        echo "Utilisateur introuvable dans la base de données.";
                    }
                } else {
                    echo "Erreur lors de l'exécution de la requête : " . htmlspecialchars($stmt->error);
                }
                $stmt->close();
            } else {
                echo "Erreur lors de la préparation de la requête.";
            }
        } else {
            echo "Vous êtes hors du rayon de 200 mètres de la structure.";
        }
        
        // Affichage des coordonnées et de la distance
        echo "Vos coordonnées : Latitude = " . htmlspecialchars($latitude) . ", Longitude = " . htmlspecialchars($longitude) . "<br>";
        echo "Distance jusqu'à la structure : " . round($distance, 2) . " mètres.<br>";
        
    } else {
        echo "Données invalides. Veuillez vérifier les informations envoyées.";
    }

    // Fermer la connexion à la base de données
    $conn->close();
    exit;
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

                    const formData = new FormData();
                    formData.append('email', email);
                    formData.append('matricule', matricule);
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