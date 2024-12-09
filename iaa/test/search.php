<?php/*
require 'database.php';

$db = Database::connect();
$email = isset($_GET['email']) ? $_GET['email'] : '';
$date_arrivee = isset($_GET['date_arrivee']) ? $_GET['date_arrivee'] : '';

// Préparation de la requête SQL
$query = 'SELECT arrive.arrive_id, personnel.photo, personnel.nom, personnel.prenom, arrive.date_arrive, arrive.statut, arrive.raison, arrive.nb_abs, SUM(nb_abs) FROM giai.personnel JOIN giai.arrive ON personnel.id_p = arrive.id_p WHERE 1=1';

if ($email) {
    $query .= ' AND personnel.email = :email';
}
if ($date_arrivee) {
    $query .= ' AND arrive.date_arrive = :date_arrivee';
}
$query .= ' ORDER BY nom ASC';

$statement = $db->prepare($query);

// Associer les paramètres
if ($email) {
    $statement->bindValue(':email', $email);
}
if ($date_arrivee) {
    $statement->bindValue(':date_arrivee', $date_arrivee);
}

$statement->execute();

// Générer le contenu HTML des résultats
while ($personnel = $statement->fetch()) {
    echo '<tr>';
    echo '<td>' . $personnel['arrive_id'] . '</td>';
    echo '<td><img src="picture/' . $personnel['photo'] . '" alt="Photo" style="max-width: 60px;"></td>';
    echo '<td>' . $personnel['nom'] . '</td>';
    echo '<td>' . $personnel['prenom'] . '</td>';
    echo '<td>' . $personnel['date_arrive'] . '</td>';
    echo '<td>' . $personnel['statut'] . '</td>';
    echo '<td>' . $personnel['raison'] . '</td>';
    echo '<td>' . $personnel['nb_abs'] . '</td>';
    echo '<td width=300>';
    echo '<a class="btn btn-default" href="view.php?id=' . $personnel['arrive_id'] . '"><span class="glyphicon glyphicon-eye-open"></span> Voir </a>';
    echo ' <a class="btn btn-primary" href="update.php?id=' . $personnel['arrive_id'] . '"><span class="glyphicon glyphicon-pencil"></span> Modifier </a>';
    echo ' <a class="btn btn-danger" href="delete.php?id=' . $personnel['arrive_id'] . '"><span class="glyphicon glyphicon-remove"></span> Supprimer </a>';
    echo '</td>';
    echo '</tr>';
}

Database::disconnect();
?>


















<?php
/*


    require 'database.php';

    if(!empty($_GET['id'])) 
    {
        $id = checkInput($_GET['id']);
    }

    $nameError = $nnameError = $emailError = $departementError = $birthdayError = $sexeError = $contactError = $nidError = $imageError 
    = $name = $lastname = $email = $departement = $birthday = $sexe = $contact = $nid = $image ="";
    if(!empty($_POST))    
       {
        $image =checkInput($_FILES["image"]["name"]);
        $nid =checkInput($_POST['nid']);

           $name =checkInput($_POST['name']);
           $lastname =checkInput($_POST['lastName']);
           $email =checkInput($_POST['email']);
           $departement=checkInput($_POST['departement']);
           $birthday =checkInput($_POST['birthday']);
           $sexe =checkInput($_POST['sexe']);
           $contact =checkInput($_POST['contact']);
           $imagePath ='picture/' . basename($image);                               
           $imageExtension =pathinfo($imagePath, PATHINFO_EXTENSION);
           $isSuccess = true;
           
        
        if(empty($name))
        {
         $nameError='ce champ ne peut pas etre vide';  
                   $isSuccess = false;
        }
        
        
         if(empty($lastname))
        {
         $nnameError='ce champ ne peut pas etre vide'; 
               $isSuccess = false;
        }
        
        
         if(empty($email))
        {
         $emailError='ce champ ne peut pas etre vide'; 
               $isSuccess = false;
        }
        
        if(empty($departement))
        {
         $departementError='ce champ ne peut pas etre vide'; 
               $isSuccess = false;
        }
        
         if(empty($birthday))
        {
         $birthdayError='ce champ ne peut pas etre vide';
               $isSuccess = false;
        }
        
        
         if(empty($sexe))
        {
         $sexeError='ce champ ne peut pas etre vide'; 
               $isSuccess = false;
        }
        
        
         if(empty($contact))
        {
         $contactError='ce champ ne peut pas etre vide'; 
               $isSuccess = false;
        }
        
        
         if(empty($nid))
        {
         $nidError='ce champ ne peut pas etre vide';
               $isSuccess = false;
        }
        
        
         if(empty($image))
        {
        // $imageError='ce champ ne peut pas etre vide';
         $isImageUpdated = false;
        }
        else
        {
             $isUploadSuccess = true;
             $isImageUpdated = true;

            
            if($imageExtension!="jpg" && $imageExtension !="png" && $imageExtension !="jpeg" && $imageExtension!="gif")
            {
                $imageError= "les fichiers autorises sont: .jpg, .jpeg, .png, .gif";
                $isUploadSuccess= false;
            }
            if(file_exists($imagePath))
            {
                $imageError="le fichier esixte deja";
                $isUploadSuccess= false;
            }
            if($_FILES["image"]["size"] > 500000)
            {
                 $imageError="le fichier ne doit pas depasser les 500KB";
                $isUploadSuccess= false;
            }
            if($isUploadSuccess)
            {
                if(!move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath))
                {
                     $imageError="il y'a eu une erreur lors du telechargement";
                     $isUploadSuccess = false;   
                }
                
            }
                
        }
        
    
         
        if (($isSuccess && $isImageUpdated && $isUploadSuccess) || ($isSuccess && !$isImageUpdated)) 
        { 
            $db = Database::connect();
            if($isImageUpdated)

            {
                $statement = $db->prepare("UPDATE   personnel  set  nom = ?, prenom = ?, email = ?, departement = ?, contact = ?,date_naiss = ?, sexe = ?, matricule =?, photo = ? WHERE id_p = ?");
                $statement->execute(array(  $name , $lastname , $email , $departement , $contact , $birthday , $sexe , $nid,$image ,$id  ));
                }
            else
            {
                $statement = $db->prepare("UPDATE personnel  set  nom = ?, prenom = ?, email = ?, departement = ?, contact = ?,date_naiss = ?, sexe = ?, matricule =? WHERE id_p = ?");
                $statement->execute(array( $name , $lastname , $email , $departement , $contact , $birthday , $sexe , $nid ,$id   ));
                }



            Database::disconnect();
            header("Location: list.php");
        }
        else if($isImageUpdated && !$isUploadSuccess)
        { 
            $db = Database::connect();
            $statement = $db->prepare("SELECT image FROM personnel where id_p = ?");
            $statement->execute(array($id));
            $personnel = $statement->fetch();
            $image = $personnel['image'];
            Database::disconnect();
           

      }
    }
    else 
    {
        $db = Database::connect();
        $statement = $db->prepare("SELECT * FROM giai.personnel where id_p = ?");
        $statement->execute(array($id));
        $personnel = $statement->fetch();

        $image =  $personnel['image'];
        $nid   =     $personnel['nid'];
       $name =     $personnel['name'];
        $lastname =  $personnel['lastName'];
        $email   =    $personnel['email'];
        $departement=  $personnel['departement'];
        $birthday =  $personnel['birthday'];
        $sexe    =       $personnel['sexe'];
        $contact =  $personnel['contact'];

               Database::disconnect();
    }


    function checkInput($data) 
    {
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      return $data;
    }

?>



<!Doctype html>
<html>
    <head>
        
        <title>insertion</title>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"> 
        <link href='http://fonts.googleapis.com/css?family=Holtwood+One+SC' rel="stylesheet" type="text/css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script> 
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>             
        <link rel="stylesheet" href="maiPn.css">
</head>

<body>
        <h1 class="text-logo"><span class="glyphicon glyphicon"></span> Systeme de gestion du personnel de l'IAI Cameroun <span class="glyphicon glyicon"></span></h1>
    <header>
        <nav>
            <h1>IAI CAMEROUN</h1>
            <ul id="navli">
                <li><a class="homeblack" href="index.html">Accueil</a></li>
                <li><a class="homered" href="insert.php">Ajouter Personnel</a></li>
                <li><a class="homeblack" href="list.php">Consulter Personnels</a></li>
                <li><a class="homered" href="connadmin.php">Quitter</a></li>
            </ul>
        </nav>
    </header>
    
    <div class="divider"></div>


    <div class="containeradmin">
        <div class="row">
       <!-- <div class="wrapper wrapper--w680">
            <div class="card card-1">
                
                <div class="card-body">-->
                <!--    <h2 class="title"><b><u>Enregistrement</u></b></h2>
                    
                    <form class="form" role="form" action="insert.php" method="POST" enctype="multipart/form-data">


                           
                                <div class="input-group">
                                     <input class="form-control" type="text" placeholder="Cliquez,entrez votre nom" id="name" name="name" required="required" value="<?php echo $name; ?> "> 
                                     <span class="help-inline"><?php echo $nameError; ?> </span>
                                </div>
                            
                           <!-- <div class="col-2">-->
                               <!-- <div class="input-group">
                                    <input class="form-control" type="text" placeholder="votre prenom" id="lastName" name="lastName" required="required"  value="<?php echo $lastname; ?> ">
                                    <span class="help-inline"><?php echo $nnameError; ?> </span>
                                
                                </div>
                        

                        <div class="input-group">  
                            <input class="input--style-1" type="text" placeholder="votre matricule" id="nid" name="nid" required="required" value="<?php echo $nid; ?> ">  
                            <span class="help-inline"><?php echo $nidError; ?> </span>
                        </div>

                        
                        <div class="input-group">
                            <input class="input--style-1" type="email" placeholder="votre email" id="email" name="email" required="required" value="<?php echo $email; ?> ">
                                    <span class="help-inline"><?php echo $emailError; ?> </span>
                        </div>
                        
                        <div class="input-group">
                            <input class="input--style-1" type="text" placeholder="votre departement" id="departement" name="departement" required="required"  value="<?php echo $departement; ?> ">
                                    <span class="help-inline"><?php echo $departementError; ?> </span>
                        </div>
                        
                        
                        <div class="input-group">
                            <input class="input--style-1" type="number" placeholder="votre contact" id="contact" name="contact" required="required" value="<?php echo $contact; ?> ">
                            <span class="help-inline"><?php echo $contactError; ?> </span>
                        </div>
                        
                       <!--  <p>Date naissance</p>-->
                      
                                <div class="input-group">
                                    <input class="input--style-1" type="date" placeholder="votre date naissance" id="birthday" name="birthday" required="required" value="<?php echo $birthday; ?> ">
                                    <span class="help-inline"><?php echo $birthdayError; ?> </span>
                                   
                                    <select id="sexe" name="sexe" value="<?php echo $sexe; ?> ">
                                            <option disabled="disabled" selected="selected">Sexe</option>
                                            <option value="Male">M</option>
                                            <option value="Female">F</option>
                                        </select>
                                 <span class="help-inline"><?php echo $sexeError; ?> </span>

                                </div>
                                
                                
                                <div class="input-group">
                                        <select id="sexe" name="sexe" value="<?php echo $sexe; ?> ">
                                            <option disabled="disabled" selected="selected">Sexe</option>
                                            <option value="Male">M</option>
                                            <option value="Female">F</option>
                                        </select>
                                 <span class="help-inline"><?php echo $sexeError; ?> </span>
                                </div>
                  
                            <div class="input-group">
                                <input class="input--style-1" type="file" placeholder="votre photo" id="image" name="image" <? echo ' ' . $personnel['photo']; ?>
                                <span class="help-inline"> <?php echo $imageError; ?> </span>

                            </div>
                            <br>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-pencil"></span> Modifier</button>
                            <a class="btn btn-primary" href="list.php"><span class="glyphicon glyphicon-arrow-left"></span> Retour</a>
                       </div>
                    
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
    </body>
    </html>