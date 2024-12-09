
<?php 
require 'database.php';

if(!empty($_GET['id'])) {
    $id = checkInput($_GET['id']);
}

$nameError = $nnameError = $emailError = $departementError = $birthdayError = $sexeError = $contactError = $nidError = $imageError = "";
$name = $lastname = $email = $departement = $birthday = $sexe = $contact = $nid = $image = "";

if(!empty($_POST)) {
    $image = checkInput($_FILES["image"]["name"]);
    $nid = checkInput($_POST['nid']);
    $name = checkInput($_POST['name']);
    $lastname = checkInput($_POST['lastName']);
    $email = checkInput($_POST['email']);
    $departement = checkInput($_POST['departement']);
    $birthday = checkInput($_POST['birthday']);
    $sexe = checkInput($_POST['sexe']);
    $contact = checkInput($_POST['contact']);
    $imagePath = 'picture/' . basename($image);
    $imageExtension = pathinfo($imagePath, PATHINFO_EXTENSION);
    $isSuccess = true;
    $isUploadSuccess = true;
    $isImageUpdated = !empty($image);

    // Validation des champs
    if(empty($name)) { $nameError = 'Ce champ ne peut pas être vide'; $isSuccess = false; }
    if(empty($lastname)) { $nnameError = 'Ce champ ne peut pas être vide'; $isSuccess = false; }
    if(empty($email)) { $emailError = 'Ce champ ne peut pas être vide'; $isSuccess = false; }
    if(empty($departement)) { $departementError = 'Ce champ ne peut pas être vide'; $isSuccess = false; }
    if(empty($birthday)) { $birthdayError = 'Ce champ ne peut pas être vide'; $isSuccess = false; }
    if(empty($sexe)) { $sexeError = 'Ce champ ne peut pas être vide'; $isSuccess = false; }
    if(empty($contact)) { $contactError = 'Ce champ ne peut pas être vide'; $isSuccess = false; }
    if(empty($nid)) { $nidError = 'Ce champ ne peut pas être vide'; $isSuccess = false; }

    // Validation de l'image
    if($isImageUpdated) {
        if($imageExtension != "jpg" && $imageExtension != "png" && $imageExtension != "jpeg" && $imageExtension != "gif") {
            $imageError = "Les fichiers autorisés sont: .jpg, .jpeg, .png, .gif";
            $isUploadSuccess = false;
        }
        if(file_exists($imagePath)) {
            $imageError = "Le fichier existe déjà";
            $isUploadSuccess = false;
        }
        if($_FILES["image"]["size"] > 500000) {
            $imageError = "Le fichier ne doit pas dépasser les 500KB";
            $isUploadSuccess = false;
        }
        if($isUploadSuccess && !move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
            $imageError = "Il y a eu une erreur lors du téléchargement";
            $isUploadSuccess = false;
        }
    }

    // Mise à jour des données dans la base
    if (($isSuccess && $isImageUpdated && $isUploadSuccess) || ($isSuccess && !$isImageUpdated)) {
        $db = Database::connect();
        if($isImageUpdated) {
            $statement = $db->prepare("UPDATE giai.personnel SET nom = ?, prenom = ?, email = ?, departement = ?, contact = ?, date_naiss = ?, sexe = ?, matricule = ?, photo = ? WHERE id_p = ?");
            $statement->execute(array($name, $lastname, $email, $departement, $contact, $birthday, $sexe, $nid, $image, $id));
        } else {
            $statement = $db->prepare("UPDATE giai.personnel SET nom = ?, prenom = ?, email = ?, departement = ?, contact = ?, date_naiss = ?, sexe = ?, matricule = ? WHERE id_p = ?");
            $statement->execute(array($name, $lastname, $email, $departement, $contact, $birthday, $sexe, $nid, $id));
        }
        Database::disconnect();
        header("Location: list.php");
    }
} else {
    $db = Database::connect();
    $statement = $db->prepare("SELECT * FROM giai.personnel WHERE id_p = ?");
    $statement->execute(array($id));
    $personnel = $statement->fetch();
    $name = $personnel['nom'];
    $lastname = $personnel['prenom'];
    $email = $personnel['email'];
    $departement = $personnel['departement'];
    $birthday = $personnel['date_naiss'];
    $sexe = $personnel['sexe'];
    $contact = $personnel['contact'];
    $nid = $personnel['matricule'];
    $image = $personnel['photo'];
    Database::disconnect();
}

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
    <title>Mettre à jour le personnel</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="text-center">Mettre à jour le personnel</h1>
        <form action="update.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Nom:</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>">
                <span class="text-danger"><?php echo $nameError; ?></span>
            </div>
            <div class="form-group">
                <label for="lastName">Prénom:</label>
                <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo $lastname; ?>">
                <span class="text-danger"><?php echo $nnameError; ?></span>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>">
                <span class="text-danger"><?php echo $emailError; ?></span>
            </div>
            <div class="form-group">
                <label for="departement">Département:</label>
                <input type="text" class="form-control" id="departement" name="departement" value="<?php echo $departement; ?>">
                <span class="text-danger"><?php echo $departementError; ?></span>
            </div>
            <div class="form-group">
                <label for="contact">Contact:</label>
                <input type="text" class="form-control" id="contact" name="contact" value="<?php echo $contact; ?>">
                <span class="text-danger"><?php echo $contactError; ?></span>
            </div>
            <div class="form-group">
                <label for="birthday">Date de naissance:</label>
                <input type="date" class="form-control" id="birthday" name="birthday" value="<?php echo $birthday; ?>">
                <span class="text-danger"><?php echo $birthdayError; ?></span>
            </div>
            <div class="form-group">
                <label for="sexe">Sexe:</label>
                <select class="form-control" id="sexe" name="sexe">
                    <option value="Male" <?php if($sexe == "Male") echo 'selected'; ?>>M</option>
                    <option value="Female" <?php if($sexe == "Female") echo 'selected'; ?>>F</option>
                </select>
                <span class="text-danger"><?php echo $sexeError; ?></span>
            </div>
            <div class="form-group">
                <label for="nid">Matricule:</label>
                <input type="text" class="form-control" id="nid" name="nid" value="<?php echo $nid; ?>">
                <span class="text-danger"><?php echo $nidError; ?></span>
            </div>
            <div class="form-group">
                <label for="image">Photo:</label>
                <input type="file" id="image" name="image">
                <span class="text-danger"><?php echo $imageError; ?></span>
                <?php if(!empty($image)): ?>
                    <p>Image actuelle: <img src="<?php echo 'picture/' . $image; ?>" alt="Photo actuelle" width="100"></p>
                <?php endif; ?>
                </div>
                <div class="form-action">
                                    <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-pencil"></span> Modifier </button>
                 </div>
            </form>
        </div>
                </body>
                </html>



