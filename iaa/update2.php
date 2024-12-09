
<?php
// Classe de connexion à la base de données
/*class Database
{
    private static $dbHost = "localhost";
    private static $dbName = "nom_de_votre_bd";
    private static $dbUser = "nom_utilisateur";
    private static $dbPass = "mot_de_passe";
    private static $connection = null;

    public static function connect()
    {
        if (self::$connection === null) {
            try {
                self::$connection = new PDO(
                    "mysql:host=" . self::$dbHost . ";dbname=" . self::$dbName . ";charset=utf8",
                    self::$dbUser,
                    self::$dbPass
                );
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (Exception $e) {
                die("Erreur : " . $e->getMessage());
            }
        }
        return self::$connection;
    }

    public static function disconnect()
    {
        self::$connection = null;
    }
}*/
require 'database.php';

// Initialisation des variables
$nameError = $lastnameError = $imageError = "";
$arriveDateError = $statusError = $commentError = $reasonError = "";
$name = $lastname = $image = "";
$arriveDate = $status = $comment = $reason = "";
$id = "";

// Connexion à la base de données
$db = Database::connect();

// Récupération de l'ID dans l'URL
if (!empty($_GET['id'])) {
    $id = checkInput($_GET['id']);
}

// Récupération des données existantes
if (!empty($id)) {
    $statement = $db->prepare("SELECT p.*, a.* 
                               FROM giai.personnel p
                               LEFT JOIN giai.arrive a ON p.id_p = a.arrive_id
                               WHERE p.id_p = ?");
    $statement->execute([$id]);
    $data = $statement->fetch();

    if ($data) {
        $name = $data['nom'];
        $lastname = $data['prenom'];
        $image = $data['photo'];
        $arriveDate = $data['date_arrivee'];
        $status = $data['statut'];
        $comment = $data['commentaire'];
        $reason = $data['raison'];
    }
}

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $name = checkInput($_POST['name']);
    $lastname = checkInput($_POST['lastName']);
    $arriveDate = checkInput($_POST['arriveDate']);
    $status = checkInput($_POST['status']);
    $comment = checkInput($_POST['comment']);
    $reason = checkInput($_POST['reason']);
    $image = $_FILES['image']['name'];
    $imagePath = 'picture/' . basename($image);
    $imageExtension = pathinfo($imagePath, PATHINFO_EXTENSION);

    // Validation des données
    $isSuccess = true;
    if (empty($name)) { $nameError = "Ce champ ne peut pas être vide"; $isSuccess = false; }
    if (empty($lastname)) { $lastnameError = "Ce champ ne peut pas être vide"; $isSuccess = false; }
    if (empty($arriveDate)) { $arriveDateError = "Ce champ ne peut pas être vide"; $isSuccess = false; }
    if (empty($status)) { $statusError = "Ce champ ne peut pas être vide"; $isSuccess = false; }
    if (!empty($image)) {
        if ($imageExtension != "jpg" && $imageExtension != "png" && $imageExtension != "jpeg" && $imageExtension != "gif") {
            $imageError = "Les fichiers autorisés sont: .jpg, .jpeg, .png, .gif";
            $isSuccess = false;
        }
        if ($_FILES["image"]["size"] > 500000) {
            $imageError = "Le fichier ne doit pas dépasser les 500KB";
            $isSuccess = false;
        }
    }

    // Mise à jour des données si tout est valide
    if ($isSuccess) {
        try {
            // Début de la transaction
            $db->beginTransaction();

            // Mise à jour de la table `personnel`
            if (!empty($image)) {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
                    $statement = $db->prepare("UPDATE personnel SET nom = ?, prenom = ?, photo = ? WHERE id_p = ?");
                    $statement->execute([$name, $lastname, $image, $id]);
                } else {
                    $imageError = "Erreur lors du téléchargement de l'image.";
                }
            } else {
                $statement = $db->prepare("UPDATE personnel SET nom = ?, prenom = ? WHERE id_p = ?");
                $statement->execute([$name, $lastname, $id]);
            }

            // Mise à jour de la table `arrive`
            $statement = $db->prepare("UPDATE arrive SET date_arrivee = ?, statut = ?, commentaire = ?, raison = ? WHERE id_personnel = ?");
            $statement->execute([$arriveDate, $status, $comment, $reason, $id]);

            // Validation de la transaction
            $db->commit();

            // Redirection après succès
            header("Location: list.php");
        } catch (Exception $e) {
            // Annulation de la transaction en cas d'erreur
            $db->rollBack();
            die("Erreur : " . $e->getMessage());
        }
    }
}

// Fonction de nettoyage des données
function checkInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mettre à jour</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="text-center">Mettre à jour les informations</h1>
        <form action="update.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
            <!-- Informations personnelles -->
            <h3>Informations personnelles</h3>
            <div class="form-group">
                <label for="name">Nom:</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>">
                <span class="text-danger"><?php echo $nameError; ?></span>
            </div>
            <div class="form-group">
                <label for="lastName">Prénom:</label>
                <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo $lastname; ?>">
                <span class="text-danger"><?php echo $lastnameError; ?></span>
            </div>
            <div class="form-group">
                <label for="image">Photo:</label>
                <input type="file" id="image" name="image">
                <span class="text-danger"><?php echo $imageError; ?></span>
                <?php if (!empty($image)): ?>
                    <p>Image actuelle: <img src="picture/<?php echo $image; ?>" alt="Photo" style="width:100px;"></p>
                <?php endif; ?>
            </div>

            <!-- Informations d'arrivée -->
            <h3>Informations d'arrivée</h3>
            <div class="form-group">
                <label for="arriveDate">Date d'arrivée:</label>
                <input type="date" class="form-control" id="arriveDate" name="arriveDate" value="<?php echo $arriveDate; ?>">
                <span class="text-danger"><?php echo $arriveDateError; ?></span>
            </div>
            <div class="form-group">
                <label for="status">Statut:</label>
                <input type="text" class="form-control" id="status" name="status" value="<?php echo $status; ?>">
                <span class="text-danger"><?php echo $statusError; ?></span>
            </div>
            <div class="form-group">
                <label for="comment">Commentaire:</label>
                <textarea class="form-control" id="comment" name="comment"><?php echo $comment; ?></textarea>
            </div>
            <div class="form-group">
                <label for="reason">Raison:</label>
                <textarea class="form-control" id="reason" name="reason"><?php echo $reason; ?></textarea>
            </div>

            <button type="submit" class="btn btn-success">Mettre à jour</button>
            <a href="list.php" class="btn btn-primary">Retour</a>
        </form>
    </div>
</body>
</html>


