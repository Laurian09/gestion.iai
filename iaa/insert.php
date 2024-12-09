<?php 
  require 'database.php';
$nameError = $nnameError = $emailError = $departementError = $birthdayError  = $contactError = $nidError = $imageError 
= $name = $lastname = $email = $departement = $birthday  = $contact = $nid = $image ="";
if(!empty($_POST))    
   {
       $name =checkInput($_POST['name']);
       $lastname =checkInput($_POST['lastName']);
       $email =checkInput($_POST['email']);
       $departement=checkInput($_POST['departement']);
       $birthday =checkInput($_POST['birthday']);
    //   $sexe =checkInput($_POST['sexe']);
       $contact =checkInput($_POST['contact']);
       $nid =checkInput($_POST['nid']);
       $image =checkInput($_FILES['image']['name']);
       $imagePath ='picture/' . basename($image);                               
       $imageExtension =pathinfo($imagePath, PATHINFO_EXTENSION);
       $isSuccess = true;
        $UploadSucccess=false;
    
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
     $imageError='ce champ ne peut pas etre vide';
           $isSuccess = false;
    }
    else
    {
         $isUploadSuccess = true;
        
        if($imageExtension!="jpg" && $imageExtension !="png" && $imageExtension !="jpeg" && $imageExtension!="gif")
        {
            $imageError= "les fichiers autorises sont: .jpg, .jpeg, .png, .gif";
            $isUpload= false;
        }
        if(file_exists($imagePath))
        {
            $imageError="le fichier n'esixte pas";
            $isUpload= false;
        }
        if($_FILES["image"]["size"] > 500000)
        {
             $imageError="le fichier ne doit pas depasser les 500KB";
            $isUpload= false;
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
    
    
    if($isSuccess && $isUploadSuccess){
    $db=Database::connect();
        $statement = $db->prepare("insert into giai.personnel (matricule,nom,
                            prenom,email,departement,date_naiss,contact, photo) values(?,?,?,?,?,?,?,?)");
        $statement->execute(array($nid,$name,$lastname,$email,$departement,$birthday,$contact,$image));
        Database::disconnect();
        header("Location: list.php");    
   }
}
   
   function checkInput($data)
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
        
        <title>insertion</title>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"> 
        <link href='http://fonts.googleapis.com/css?family=Holtwood+One+SC' rel="stylesheet" type="text/css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script> 
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>             
        <link rel="stylesheet" href="inser.css">
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
                <li><a class="homeblack" href="list2.php">Consulter Ponctualite</a></li>
                <li><a class="homered" href="connadmin.php">Quitter</a></li>
            </ul>
        </nav>
    </header>
    
    <div class="divider"></div>


    <div class="containeradmin">
        <div class="row">
      
                    <h2 class="title"><b><u>Enregistrement</u></b></h2>
                    
                    <form class="form" role="form" action="" method="POST" enctype="multipart/form-data">


                           
                                <div class="input-group">
                                     Nom:<input class="input--style-1" type="text" placeholder="m" id="name" name="name" required="required" value="<?php echo $name; ?> "> 
                                     <span class="help-inline"><?php echo $nameError; ?> </span>
                                </div>
                            
                                <div class="input-group">
                                    Prenom:<input class="input--style-1" type="text" placeholder="votre prenom" id="lastName" name="lastName" required="required"  value="<?php echo $lastname; ?> ">
                                    <span class="help-inline"><?php echo $nnameError; ?> </span>
                                
                                </div>
                        

                        <div class="input-group">  
                            Matricule:<input class="input--style-1" type="text" placeholder="votre matricule" id="nid" name="nid" required="required" value="<?php echo $nid; ?> ">  
                            <span class="help-inline"><?php echo $nidError; ?> </span>
                        </div>

                        
                        <div class="input-group">
                           Email: <input class="input--style-1" type="email" placeholder="votre email" id="email" name="email" required pattern="^[A-Za-z]+@{1}[A-Za-z]+\.{1}[A-Za-z]{2,}$" value="<?php echo $email; ?> ">
                                    <span class="help-inline"><?php echo $emailError; ?> </span>
                        </div>
                        
                        <div class="input-group">
                            Departement:<input class="input--style-1" type="text" placeholder="votre departement" id="departement" name="departement" required="required"  value="<?php echo $departement; ?> ">
                                    <span class="help-inline"><?php echo $departementError; ?> </span>
                        </div>
                        
                        
                        <div class="input-group">
                            Contact:<input class="input--style-1" type="number" placeholder="votre contact" id="contact" name="contact" required="required" value="<?php echo $contact; ?> ">
                            <span class="help-inline"><?php echo $contactError; ?> </span>
                        </div>
                        
                       <!--  <p>Date naissance</p>-->
                      
                                <div class="input-group">
                                    Date naissance:<input class="input--style-1" type="date" placeholder="votre date naissance" id="birthday" name="birthday" required="required" value="<?php echo $birthday; ?> ">
                                    <span class="help-inline"><?php echo $birthdayError; ?> </span>
                                   
                              
                                </div>
                                
                             
                            <div class="input-group">
                               Image: <input class="input--style-1" type="file" placeholder="votre photo" id="image" name="image" >
                                <span class="help-inline"> <?php echo $imageError; ?> </span>

                            </div>
                            <div class="form-action">
                                    <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-pencil"></span> Ajouter </button>
                                    <!--<a class="btn btn-primary" href="index.php"><span class="glyphicon glyphicon-arrow-left"></span> Retour</a>-->
                                </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
    </body>
    </html>