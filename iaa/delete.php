<?php
    require 'database.php';
 
    if(!empty($_GET['id'])) 
    {
        $id = checkInput($_GET['id']);
    }

    if(!empty($_POST)) 
    {
        $id = checkInput($_POST['id']);
        $db = database::connect();
        $statement = $db->prepare("DELETE FROM giai.personnel WHERE id_p = ?");
        $statement->execute(array($id));
        database::disconnect();
        header("Location: list.php"); 
    }

    function checkInput($data) 
    {
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      return $data;
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Supprimmer un article</title>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
        <link href='http://fonts.googleapis.com/css?family=Holtwood+One+SC' rel='stylesheet' type='text/css'>
        
        <link rel="stylesheet" href="../css/styles.css"> 
    
    </head>
    
    <body>
    <h1 class="text-logo"><span class="glyphicon glyphicon"></span> Systeme de gestion du personnel de l'IAI Cameroun <span class="glyphicon glyicon"></span></h1>
         <div class="container admin">
            <div class="row">
                <h1><strong>Supprimer un article</strong></h1>
                <br>
                <form class="form" action="delete.php" role="form" method="post">
                    <input type="hidden" name="id" value="<?php echo $id;?>"/>
                    <p class="alert alert-warning">Etes vous sur de vouloir supprimer ?</p>
                    <div class="form-actions">
                      <button type="submit" class="btn btn-warning">Oui</button>
                      <a class="btn btn-default" href="list.php">Non</a>
                    </div>
                </form>
            </div>
        </div>   
    </body>
</html>

