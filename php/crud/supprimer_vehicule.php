<?php
require_once('../database/db.php');
$conn = connectDB();

if (isset($_GET['id'])) {
    $vehiculeId = intval($_GET['id']);

    // Supprimer les rendez-vous liés à ce véhicule
    $stmt1 = $conn->prepare("DELETE FROM rendezvous WHERE vehicule_id = ?");
    $stmt1->bind_param("i", $vehiculeId);
    $stmt1->execute();

    // Puis supprimer le véhicule
    $stmt2 = $conn->prepare("DELETE FROM vehicules WHERE id = ?");
    $stmt2->bind_param("i", $vehiculeId);
    $stmt2->execute();
}

header("Location: ../index.php");
exit;
