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
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
    <h1><strong>Liste de presence avancée:</strong>  Qui voulez vous trouver, quand ?
    </h1>

<form id="searchForm" method="GET">
    <input type="email" name="email" placeholder="Rechercher par email" class="inputs1">
    <input type="date" name="date_arrivee" placeholder="YYYY-MM-DD" class="inputs2">
    <button class="inputs3" type="submit"><b>Rechercher</b></button>
</form>
         <style>
        form {
            /*display:center;*/
            margin-left: 440px;
            /*margin-right: 500px;*/
            margin-bottom: 20px;  
        }

        .inputs1{
            border-radius: 10px;
            height: 40px;
            width: 250px;

        }

        .inputs2{
            border-radius: 10px;
            height: 40px;
            width: 150px;

        }

        .inputs3{
            border-radius: 10px;
            height: 40px;
            width: 150px;

        }


        </style>
<div id="results">
    <table class="table table-striped table-bordered">
        <!-- ...table headers... -->
        <thead>
                        <tr>
                            <th>id</th>
                            <th>Photo</th>                                    
                            <th>Nom</th>
                            <th>Prenom</th>
                            <th>Date Arrivée</th>
                            <th>Statut</th>
                            <th>Raison</th>
                          
                            <th>Nb Abs</th>
                            <th>Total heure sup</th>
                            <th>Commantaitre</th>

                            <th>Actions</th>

                        </tr>          
                    </thead> 
        <tbody id="tableBody">
            <?php 
                require 'database.php';
                $db = Database::connect();

                // Filtrage des résultats selon les entrées
                $email = isset($_GET['email']) ? $_GET['email'] : '';
                $date_arrivee= isset($_GET['date_arrivee']) ? $_GET['date_arrivee'] : '';
               //  =$daate->{'Y-m-d'};
                // Construction de la requête SQL
                $sql = 'SELECT arrive.arrive_id, personnel.photo, personnel.nom,
                        personnel.prenom, arrive.date_arrive, arrive.statut, arrive.raison, arrive.nb_abs, arrive.nb_h_surp, arrive.commentaire
                        FROM giai.personnel 
                        JOIN giai.arrive ON personnel.id_p = arrive.id_p';

                // Ajouter des conditions si email ou date est entré
                $conditions = [];
                if (!empty($email)) {
                    $conditions[] = 'personnel.email = :email';
                }
                if (!empty($date_arrivee)) {
                    $conditions[] = 'arrive.date_arrive = :date_arrivee';
                }
                if ($conditions) {
                    $sql .= ' WHERE ' . implode(' AND ', $conditions);
                }
                $sql .= ' ORDER BY personnel.nom ASC';

                $statement = $db->prepare($sql);

                // Liaison des paramètres
                if (!empty($email)) {
                    $statement->bindParam(':email', $email);
                }
                if (!empty($date_arrivee)) {
                    $statement->bindParam(':date_arrivee', $date_arrivee);
                }
                
                // Exécution de la requête
                $statement->execute();

                // Affichage des résultats
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
                    echo '<td>' . $personnel['nb_h_surp'] . '</td>';
                    echo '<td>' . $personnel['commentaire'] . '</td>';

                    echo '<td width=100>';
                   // echo '<a class="btn btn-default" href="view.php?id=' . $personnel['arrive_id'] . '"><span class="glyphicon glyphicon-eye-open"></span> Voir </a>';
                    //echo ' ';
                    echo '<a class="btn btn-primary" href="update2.php?id=' . $personnel['arrive_id'] . '"><span class="glyphicon glyphicon-pencil"></span> Modifier </a>';
                    echo ' ';
                    //echo '<a class="btn btn-danger" href="delete.php?id=' . $personnel['arrive_id'] . '"><span class="glyphicon glyphicon-remove"></span> Supprimer </a>';
                    echo ' ';
                    echo '</td>';
                    echo '</tr>';
                }
                Database::disconnect();
            ?>
        </tbody>
    </table>
</div>
 </div>






 <div class="container">
  <h2>Statistiques des Données</h2>
  
  <div>
    <canvas id="barChart"></canvas>
  </div>
  
  <div>
    <canvas id="pieChart"></canvas>
  </div>
  
  <div>
    <canvas id="lineChart"></canvas>
  </div>
 </div>


<script>

// Diagramme à barres (Nombre d'absences par statut)
const ctxBar = document.getElementById('barChart').getContext('2d');
const barChart = new Chart(ctxBar, {
  type: 'bar',
  data: {
    labels: ['Retard', 'En retard', 'Présent', 'Absent'], // Adapter avec les différents statuts
    datasets: [{
      label: 'Nombre d\'absences',
      data: [6, 14, 10, 3], // Adapter avec les données de votre base
      backgroundColor: 'rgba(75, 192, 192, 0.2)',
      borderColor: 'rgba(75, 192, 192, 1)',
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: {
        beginAtZero: true
      }
    }
  }
});

// Diagramme circulaire (Proportion de chaque statut)
const ctxPie = document.getElementById('pieChart').getContext('2d');
const pieChart = new Chart(ctxPie, {
  type: 'pie',
  data: {
    labels: ['Retard', 'En retard', 'Présent', 'Absent'], // Adapter avec les différents statuts
    datasets: [{
      data: [6, 14, 10, 3], // Adapter avec les données de votre base
      backgroundColor: [
        'rgba(255, 99, 132, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(255, 206, 86, 0.2)',
        'rgba(75, 192, 192, 0.2)'
      ],
      borderColor: [
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)'
      ],
      borderWidth: 1
    }]
  },
  options: {
    responsive: true
  }
});

// Diagramme linéaire (Nombre d'absences au fil du temps)
const ctxLine = document.getElementById('lineChart').getContext('2d');
const lineChart = new Chart(ctxLine, {
  type: 'line',
  data: {
    labels: ['2024-11-10 14:43', '2024-11-10 14:52', '2024-11-10 14:54', '2024-11-10 20:22'], // Adapter avec les dates
    datasets: [{
      label: 'Nombre d\'absences',
      data: [6, 6, 12, 12], // Adapter avec les données de votre base
      backgroundColor: 'rgba(153, 102, 255, 0.2)',
      borderColor: 'rgba(153, 102, 255, 1)',
      borderWidth: 1,
      fill: false
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: {
        beginAtZero: true
      }
    }
  }
});

    </script>

<?php
// Exemple de requête SQL pour récupérer les données des absences
$data = [];
$result = $db->query("SELECT statut, COUNT(*) as count FROM giai.arrive GROUP BY statut");
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
echo "<script>const absencesData = " . json_encode($data) . ";</script>";
?>

<script>
    // Utilisez les données PHP dans vos graphiques JavaScript
const labels = absencesData.map(item => item.statut);
const values = absencesData.map(item => item.count);

const barChart = new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Nombre d\'absences',
            data: values,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

    </script>
       


 </body>
   </html>