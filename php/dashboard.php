<?php
session_start();
require_once('database/db.php');

require_once(__DIR__ . '/security/connexion.php');
$conn = connectDB();

if (!isset($_SESSION['token']) || !isTokenValid($_SESSION['token'])) {
    header('Location: ./index.php');
    exit();
}

// Totaux
$result = $conn->query("SELECT COUNT(*) AS total_clients FROM clients");
$row = $result->fetch_assoc();
$totalClients = $row['total_clients'];

$result = $conn->query("SELECT COUNT(*) AS total_vehicules FROM vehicules");
$row = $result->fetch_assoc();
$totalVehicules = $row['total_vehicules'];

$result = $conn->query("SELECT COUNT(*) AS total_rendezvous FROM rendezvous");
$row = $result->fetch_assoc();
$totalRendezvous = $row['total_rendezvous'];

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Garage Train</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        h1,
        h2 {
            color: #333;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #eee;
        }

        a {
            text-decoration: none;
            color: #007BFF;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <h1>Tableau de Bord Garage Train</h1>

    <div>
        <h2>Clients</h2>
        <p>Total Clients : <?= $totalClients ?></p>
    </div>

    <div>
        <h2>Véhicules</h2>
        <p>Total Véhicules : <?= $totalVehicules ?></p>
    </div>

    <div>
        <h2>Rendez-vous</h2>
        <p>Total Rendez-vous : <?= $totalRendezvous ?></p>
    </div>

    <div>
        <?php include('crud/liste_vehicules.php'); ?>
    </div>

</body>

</html>