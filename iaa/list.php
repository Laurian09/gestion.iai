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

    <h1><strong>Liste du personnel </strong><a href="insert.php" class="btn btn-success btn-Ig"><span class="glyphicon glyphicon-plus"></span>Ajouter</a></h1>
                
<table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>id_p</th>
                            <th>Photo</th>                                    
                            <th>Matricule</th>
                            <th>Nom</th>
                            <th>Prenom</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Date_connection</th>
                            <th>Date naissance</th>
                            <th>Actions</th>

                        </tr>          
                    </thead> 
                    <tbody> 
                        
                        <?php 
                            require 'database.php';
                            $db = Database::connect();
                            $statement = $db->query('SELECT personnel.id_p, personnel.photo, personnel.matricule, personnel.nom,
                            personnel.prenom, personnel.email, personnel.contact,
                            personnel.date_login, personnel.date_naiss FROM GIAI.personnel ORDER BY nom ASC');
                            while( $personnel = $statement->fetch())
                            {
                            echo '<tr>';
                                echo'<td>' . $personnel['id_p'] . '</td>';
                                echo '<td><img src="picture/' . $personnel['photo'] . '" alt="Photo" style="max-width: 60px;"></td>';
                                echo '<td>' . $personnel['matricule'] . '</td>';
                                echo '<td>' . $personnel['nom'] . '</td>';
                                echo '<td>' . $personnel['prenom'] . '</td>';
                                echo '<td>' . $personnel['email'] . '</td>'; 
//                                echo '<td>' . $personnel['departement'] . '</td>';
                                echo '<td>' . $personnel['contact'] . '</td>';
                                echo '<td>' . $personnel['date_login'] . '</td>';
    //                            echo '<td>' . date('Y-m-d H:i:s', $personnel['heure_arrivee']) . '</td>';
//                                echo '<td>' . $personnel['sexe'] . '</td>';
                                echo '<td>' . $personnel['date_naiss'] . '</td>';
                                echo '<td width=300>';
                                echo '<a class="btn btn-default" href="view.php?id=' . $personnel['id_p'] . '"><span class="glyphicon glyphicon-eye-open"></span> Voir </a>';
                                echo" ";
                                echo '<a class="btn btn-primary" href="update.php?id=' . $personnel['id_p'] . '"><span class="glyphicon glyphicon-pencil"></span> Modifier </a>';
                                echo" ";
                                echo '<a class="btn btn-danger" href="delete.php?id=' . $personnel['id_p'] . '"><span class="glyphicon glyphicon-remove"></span> Supprimer </a>';
                                echo" ";
                                echo '</td>';
                            echo'</tr>'  ;  
                           }
                        ?>
                        
                    </tbody>
            </table>
            </div>
            </div>
    </body>
    </html>
            