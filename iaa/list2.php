<!Doctype html>
<html>
    <head>
        <title>Liste du personnel</title>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script> 
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>-->
        <link href='http://fonts.googleapis.com/css?family=Holtwood+One+SC' rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="liste.css">
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
    <h1><strong>Liste de presence</strong>    <a href="rech.php" class="btn btn-success btn-Ig"><span class="glyphicon glyphicon-plus"></span>Recherche Rapide & Approfondie</a>
    </h1>

    <!--<h1><strong>Liste d'arrivée </strong> <a href="" class="btn btn-success btn-Ig"><span class="glyphicon glyphicon-plus"></span>Ajouter</a></h1>-->   
      <div class="table-container">
 <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>Photo</th>                                    
                            <th>Nom</th>
                            <th>Prenom</th>
                           
                            <th>Statut</th>
                            <th>Raison</th>
                            <th>Nbre total Abscence</th>
                            <th>Nbre total heure sup</th>

                            
                           <!-- <th>Actions</th>  <th>Date Arrivée</th>-->

                        </tr>          
                    </thead> 

                    <style>
                        /* Style pour le conteneur du tableau */
                        .table-container {
                            width: 90%; /* Définissez une largeur adaptée, comme 80% ou une valeur fixe comme 800px */
                            max-width: 1400px; /* Largeur maximale pour éviter que le tableau ne soit trop large sur grand écran */
                            margin: 0px 80px; /* Centre le conteneur horizontalement */
                            padding: 10px; /* Ajoute un peu d'espace autour du tableau */
                            border: 1px solid #ddd; /* Optionnel : ajoute une bordure autour du conteneur */
                        }

                        /* Style pour le tableau */
                        table {
                            width: 100%; /* Assure que le tableau utilise toute la largeur du conteneur */
                            border-collapse: collapse; /* Supprime les espaces entre les cellules */
                        }

                        th, td {
                            padding: 10px;
                            text-align: left;
                            border: 1px solid #ddd; /* Bordure pour chaque cellule */
                        }
                    </style>
                    <tbody> 
                        
                    


                        <?php 
                            require 'database.php';
                            $db = Database::connect();
                            $statement = $db->query('SELECT arrive.arrive_id, personnel.photo, personnel.nom,
                            personnel.prenom, /*arrive.date_arrive,*/ arrive.statut, arrive.raison, arrive.nb_abs, SUM(nb_abs) /*AS Nb_total */,  SUM(nb_h_surp) FROM giai.personnel Join giai.arrive ON personnel.id_p = arrive.id_p GROUP by personnel.id_p ORDER BY nom ASC');
                            while( $personnel = $statement->fetch())
                            {
                            echo '<tr>';
                                echo'<td>' . $personnel['arrive_id'] . '</td>';
                                echo '<td><img src="picture/' . $personnel['photo'] . '" alt="Photo" style="max-width: 60px;"></td>';
                                
                                echo '<td>' . $personnel['nom'] . '</td>';
                                echo '<td>' . $personnel['prenom'] . '</td>';
                              //  echo '<td>' . $personnel['date_arrive'] . '</td>'; 
                                echo '<td>' . $personnel['statut'] . '</td>';
                                echo '<td>' . $personnel['raison'] . '</td>';
                               // echo '<td>' . $personnel['nb_abs'] . '</td>';
                                echo '<td>' . $personnel['SUM(nb_abs)'] . '</td>';
                                echo '<td>' . $personnel['SUM(nb_h_surp)'] . '</td>';

    //                            echo '<td>' . date('Y-m-d H:i:s', $personnel['heure_arrivee']) . '</td>';
//                                echo '<td>' . $personnel['sexe'] . '</td>';
 //                               echo '<td>' . $personnel['date_naiss'] . '</td>';
                               // echo '<td width=300>';
                               // echo '<a class="btn btn-default" href="view.php?id=' . $personnel['arrive_id'] . '"><span class="glyphicon glyphicon-eye-open"></span> Voir </a>';
                               // echo" ";
                                //echo '<a class="btn btn-primary" href="update.php?id=' . $personnel['arrive_id'] . '"><span class="glyphicon glyphicon-pencil"></span> Modifier </a>';
                               // echo" ";
                               // echo '<a class="btn btn-danger" href="delete.php?id=' . $personnel['arrive_id'] . '"><span class="glyphicon glyphicon-remove"></span> Supprimer </a>';
                               // echo" ";
                               // echo '</td>';
                            echo'</tr>'  ;  
                           }
                        ?>
                        
                    </tbody>
            </table>
                        
            </div>
            </div>

            
    </body>
    </html>
            