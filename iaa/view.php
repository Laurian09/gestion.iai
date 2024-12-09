<?php
require 'database.php';

if(!empty($_GET['id']))
{
    $id =  checkInput($_GET['id']);
}

$db= Database::connect();
$statement=$db->prepare('SELECT personnel.id_p, personnel.photo, personnel.matricule, personnel.nom,
                            personnel.prenom, personnel.email, personnel.departement, personnel.contact,
                            personnel.date_login, personnel.date_naiss, personnel.sexe, personnel.mp FROM GIAI.personnel where personnel.id_p = ? ');
$statement->execute(array($id));
$personnel = $statement->fetch();
Database::disconnect();
    
function checkInput ($data)  
   {
       $data =trim($data);
       $data=stripslashes($data);
       $data=htmlspecialchars($data);
       return $data;
   }
?>


<!Doctype html>
<html>
    <head>
        <title>information complet</title>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script> 
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
        <link href='http://fonts.googleapis.com/css?family=Holtwood+One+SC' rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="style.css">
</head>

<body>
<h1 class="text-logo"><span class="glyphicon glyphicon"></span> Systeme de gestion du personnel de l'IAI Cameroun <span class="glyphicon glyicon"></span></h1>
<div class="container admin">
        <div class="row">
            <div class="col-sm-6">
                 <h1><strong>Information du personnel </strong></h1>
        <br>
            <FORM>
                <DIV CLASSE="form-group">
                    <label>Matricule:</label><?php echo ' ' . $personnel['matricule']; ?>
                </DIV>
                <DIV CLASSE="form-group">
                    <label>Nom:</label><?php echo ' ' . $personnel['nom']; ?>
                </DIV>
                <DIV CLASSE="form-group">
                    <label>Prenom:</label><?php echo' ' . $personnel['prenom']; ?>
                </DIV>
                <DIV CLASSE="form-group">
                    <label>Email:</label><?php echo' ' . $personnel['email']; ?>
                </DIV>
                <DIV CLASSE="FORM-GROUP">
                    <label>Departement:</label><?php echo' ' . $personnel['nom']; ?>
                </DIV>
                <DIV CLASSE="FORM-GROUP">
                    <label>Contact:</label><?php echo' ' . $personnel['contact']; ?>
                </DIV>
                <DIV CLASSE="FORM-GROUP">
                    <label>1ere connexion:</label><?php echo' ' . $personnel['date_login']; ?>
                </DIV>
                <DIV CLASSE="FORM-GROUP">
                    <label>Date naissance:</label><?php echo' ' . $personnel['date_naiss']; ?>
                </DIV>
                <DIV CLASSE="FORM-GROUP">
                    <label>Sexe:</label><?php echo' ' . $personnel['sexe']; ?>
                </DIV>
                <DIV CLASSE="FORM-GROUP">
                    <label>Mot de passe:</label><?php echo' ' . $personnel['mp']; ?>
                </DIV>
                 <DIV CLASSE="FORM-GROUP">
                    <label>Photo</label><?php echo' ' . $personnel['photo']; ?>
                </DIV>
            </FORM>
            <div class="form-actions">
                <a class="btn btn-primary" href="list.php"><span class="glyphicon glyphicon-arrow-left"></span>Retour</a>
            </div>
            </div>
            <div class="col-sm-6 site ">
                <div class="thumbnail">
                <img src="<?php echo 'picture/' . $personnel['photo'] ; ?>" alt="...">
                    <div class="caption">
                    <h4><?php echo ' ' . $personnel['nom']; ?></h4>
                    </div>
                </div>
            </div>
            
       
               
            </div>
            </div>
    </body>
    </html>
            