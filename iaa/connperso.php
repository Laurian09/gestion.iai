
<?php
session_start();
require_once 'database.php';

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

$email=$matricule=$raison="";
if (isset($_POST['login-submit'])) {
   /* $email = checkInput($_POST['email']);
    $matricule = checkInput( $_POST['matricule']);
    $raison = checkInput( $_POST['raison']);
    */

    $email = $_POST['email'];
    $matricule =  $_POST['matricule'];
    $raison =  $_POST['Raison'];

     // Récupération des coordonnées géographiques
     $latitude = $_POST['latitude'];
     $longitude = $_POST['longitude'];
     //$locationName = $_POST['locationName'];
 
    
    // Connexion à la base de données
    $db = Database::connect();
    
    if ($email && $matricule && is_numeric($latitude) && is_numeric($longitude)) {
        // Calculer la distance
        $distance = calculateDistance($latitude, $longitude, $structureLatitude, $structureLongitude);

        // Vérifier si l'utilisateur est dans un rayon de 200 m
        if ($distance <= 200) {

                // Vérifier si l'utilisateur existe
                $stmt = $db->prepare("SELECT * FROM giai.personnel WHERE email = ? AND matricule = ?");
                $stmt->execute([$email, $matricule]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user) {
                    // Si l'utilisateur est trouvé, démarrer une session et rediriger
                    

                    // Récupérer la date et l'heure actuelles
                    date_default_timezone_set("Africa/Douala");
                    $heureArrivee = new DateTime();
                    $heuredepart = new DateTime();
                    $heureActuelle = new DateTime();
                    $heureReference = new DateTime('08:00');
                    $heureReference2 = new DateTime('17:00');
                    // $heureReference2 = new DateTime('06:00');


 
// Définition des messages de session
if ($raison === 'Arrivee') {
      // Calculer la différence en heures si l'utilisateur est en retard
                
      $statut = ($heureArrivee < $heureReference) ? 'ponctuel' : 'en retard';
      $difference = ($heureArrivee > $heureReference) ? $heureArrivee->diff($heureReference)->format('%h:%i') : '00:00';
 // Insertion dans la base de données selon la raison (arrivée ou départ)
 $stmt = $db->prepare("
 INSERT INTO giai.arrive (id_p, date_arrive, raison, statut, nb_abs, latitude, longitude) 
 VALUES (?, ?, ?, ?, ?,?,?)");
/*
$stmt = $db->prepare("
            INSERT INTO giai.arrive (id_p, date_arrive,raison, statut, nb_abs, latitude, longitude, location_name)
            VALUES (?, ?, ?, ?, ?, ?,?,?)
        ");*/

/*$stmt->execute([$user['id_p'], $heureActuelle->format('Y-m-d H:i:s'), $raison, $statut, $difference]);
*/

$stmt->execute([$user['id_p'], $heureActuelle->format('Y-m-d H:i:s'), $raison, $statut, $difference, $latitude, $longitude]);



  $_SESSION['message'] = "Bonjour {$user['nom']} {$user['prenom']}, vous êtes $statut. Heure d'arrivée : {$heureActuelle->format('H:i')}.";

} else {
  // Calculer la différence en heures si l'utilisateur est en retard
                
  $statut = ($heuredepart < $heureReference2) ? 'depart-tot' : 'aurevoir';

  if($statut=== 'depart-tot'){
        $difference = ($heuredepart > $heureReference2|| $heuredepart < $heureReference2 ) ? $heuredepart->diff($heureReference2)->format('%h:%i') : '00:00';
        // Insertion dans la base de données selon la raison (arrivée ou départ)
        $stmt = $db->prepare("
        INSERT INTO giai.arrive (id_p, date_arrive, raison, statut, nb_abs,latitude, longitude) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

       /* $stmt = $db->prepare("
        INSERT INTO giai.arrive (id_p, date_arrive,raison, statut, nb_abs, latitude, longitude, location_name)
        VALUES (?, ?, ?, ?, ?, ?,?,?)
    ");*/

        //$stmt->execute([$user['id_p'], $heureActuelle->format('Y-m-d H:i:s'), $raison, $statut, $difference]);
        $stmt->execute([$user['id_p'], $heureActuelle->format('Y-m-d H:i:s'), $raison, $statut, $difference,
        $latitude,
        $longitude]);
        


        $_SESSION['message'] = "Bonsoir {$user['nom']} {$user['prenom']}, il est {$heureActuelle->format('H:i')}. Au revoir et bonne soirée.";

  }else{

    $difference = ($heuredepart > $heureReference2|| $heuredepart < $heureReference2 ) ? $heuredepart->diff($heureReference2)->format('%h:%i') : '00:00';
    // Insertion dans la base de données selon la raison (arrivée ou départ)
    $stmt = $db->prepare("
    INSERT INTO giai.arrive (id_p, date_arrive, raison, statut, nb_h_surp, latitude, longitude)
        VALUES (?, ?, ?, ?, ?, ? , ?)
    ");
   
   $stmt->execute([$user['id_p'], $heureActuelle->format('Y-m-d H:i:s'), $raison, $statut, $difference, $latitude,
   $longitude
]);
   
   
     $_SESSION['message'] = "Bonsoir {$user['nom']} {$user['prenom']}, il est {$heureActuelle->format('H:i')}. Au revoir et bonne soirée.";
   
  }
 }


        // Rediriger vers une autre page ou recharger la page pour afficher le message
        header("Location: connperso.php");
        exit();
    } else {
        $_SESSION['error'] = "Matricule ou email incorrect.";
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
    Database::disconnect();
}
    

?>


<!DOCTYPE html>
<html>
<head><meta charset="utf-8">
	<title>cobLog In Syst gestion </title>
	<link rel="stylesheet" type="text/css" href="stylelogin.css">
</head>
<body>
	<header>
		<nav>
			<h1>syst de gestion du personnel IAI</h1>
			<ul id="navli">
				<li><a class="homeblack" href="index.html">HOME</a></li>
				<li><a class="homered" href="connperso.php">Perso_Log</a></li>
				<li><a class="homeblack" href="connprochcollab.php">Proch collab</a></li>
                <li><a class="homeblack" href="connadmin.php">Chef_Log</a></li>
			</ul>
		</nav>
	</header>
	<div class="divider"></div>

	<div class="loginbox">
    <img src="assets/avatar.png" class="avatar">
    <?php
    // Afficher un message de bienvenue si l'utilisateur est connecté avec succès
    if (isset($_SESSION['message'])) {
    //    echo "<script>alert('{$_SESSION['message']}');</script>";
        echo "<p style='color:green;'>{$_SESSION['message']}</p>";
        unset($_SESSION['message']);
    }

    // Afficher un message d'erreur si les informations sont incorrectes
    if (isset($_SESSION['error'])) {
        echo "<p style='color:red;'>{$_SESSION['error']}</p>";
        unset($_SESSION['error']);
    }
    ?>
        <h1>Login Here</h1>
        <!--<form id="loginForm" action="" method="POST">-->
        <form id="geoForm" onsubmit="event.preventDefault(); connectUser();">
            <p>Email</p>
            <input type="text" name="email" id="email" placeholder="Enter Email Address@." required pattern="^[A-Za-z]+@{1}[A-Za-z]+\.{1}[A-Za-z]{2,}$">
            <p>Matricule</p>
            <input type="text" name="matricule" id="matricule" placeholder="Enter votre Matricule(au moin $ caracteres)" required="required">
			<p>Veuillez choisir la raison de votre connection</p>
           
            <!-- Affiche le message d'erreur si présent -->
            <?php /*if (!empty($error)) : ?>
                <p  style="color:red;  "><?= htmls2,pecialchars($error); ?></p>
            <?php endif;*/ ?>
            <select name="Raison"  required Style= "padding:10px;">
                <!-- <option disabled="disabled" selected="selected">Raison</option>
                <option disabled selected>Choisissez une raison</option>-->

                <option value="Arrivee">Arrivée</option>
                <option value="Depart">Départ</option>
            </select>
 
            <button type="submit">Connexion</button>

<!--
    <input type="submit" name="login-submit" value="Login" onclick="getLocation()"> -->


          <!--  <input type="submit" name="login-submit" value="Login"> -->
          
       </form>
       
  <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
	Today's Date
</p>
<p class="heading-sub12" style="padding: 0;margin: 0;">
	<?php 
date_default_timezone_set('Africa/douala');
$today = date('Y-m-d');
echo $today;
?>
</p>

    </div>
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