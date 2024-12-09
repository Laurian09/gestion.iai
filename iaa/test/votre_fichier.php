<?php /*
require 'database.php';
$db = Database::connect();

$email = isset($_GET['email']) ? $_GET['email'] : '';
$date_arrive = isset($_GET['date_arrivee']) ? $_GET['date_arrivee'] : '';

$sql = 'SELECT arrive.arrive_id, personnel.photo, personnel.nom, personnel.prenom, 
        arrive.date_arrive, arrive.statut, arrive.raison, arrive.nb_abs, SUM(arrive.nb_abs) AS Nb_total 
        FROM giai.personnel 
        JOIN giai.arrive ON personnel.id_p = arrive.id_p ';

$conditions = [];
$params = [];

if ($email) {
    $conditions[] = 'personnel.email LIKE :email';
    $params[':email'] = '%' . $email . '%';
}

if ($date_arrive) {
    $conditions[] = 'arrive.date_arrive = :date_arrive';
    $params[':date_arrive'] = $date_arrive;
}

if (!empty($conditions)) {
    $sql .= 'WHERE ' . implode(' AND ', $conditions);
}

$sql .= ' GROUP BY personnel.id_p ORDER BY nom ASC';

$statement = $db->prepare($sql);
$statement->execute($params);

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    while ($personnel = $statement->fetch()) {
        echo '<tr>';
        echo '<td>' . $personnel['arrive_id'] . '</td>';
        echo '<td><img src="picture/' . $personnel['photo'] . '" alt="Photo" style="max-width: 60px;"></td>';
        echo '<td>' . $personnel['nom'] . '</td>';
        echo '<td>' . $personnel['prenom'] . '</td>';
        echo '<td>' . $personnel['date_arrive'] . '</td>'; 
        echo '<td>' . $personnel['statut'] . '</td>';
        echo '<td>' . $personnel['raison'] . '</td>';
        echo '<td>' . $personnel['Nb_total'] . '</td>';
        echo '<td width=300>';
        echo '<a class="btn btn-default" href="view.php?id=' . $personnel['arrive_id'] . '"><span class="glyphicon glyphicon-eye-open"></span> Voir </a>';
        echo ' ';
        echo '<a class="btn btn-primary" href="update.php?id=' . $personnel['arrive_id'] . '"><span class="glyphicon glyphicon-pencil"></span> Modifier </a>';
        echo ' ';
        echo '<a class="btn btn-danger" href="delete.php?id=' . $personnel['arrive_id'] . '"><span class="glyphicon glyphicon-remove"></span> Supprimer </a>';
        echo '</td>';
        echo '</tr>';
    }
    exit;
}
?>
