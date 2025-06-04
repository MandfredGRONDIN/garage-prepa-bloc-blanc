<?php
require_once(__DIR__ . '/../database/db.php');
$conn = connectDB();

$vehiculesResult = $conn->query("
    SELECT vehicules.id, marque, modele, annee, clients.nom AS client_nom
    FROM vehicules
    LEFT JOIN clients ON vehicules.client_id = clients.id
");
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-dark">Liste des Véhicules</h2>
        <a href="crud/ajouter_vehicule.php" class="btn btn-primary">+ Ajouter un véhicule</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Marque</th>
                    <th>Modèle</th>
                    <th>Année</th>
                    <th>Client</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($vehicule = $vehiculesResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= $vehicule['id'] ?></td>
                        <td><?= htmlspecialchars($vehicule['marque']) ?></td>
                        <td><?= htmlspecialchars($vehicule['modele']) ?></td>
                        <td><?= $vehicule['annee'] ?></td>
                        <td><?= htmlspecialchars($vehicule['client_nom']) ?></td>
                        <td>
                            <a href="crud/modifier_vehicule.php?id=<?= $vehicule['id'] ?>" class="btn btn-sm btn-success">Modifier</a>
                            <a href="crud/supprimer_vehicule.php?id=<?= $vehicule['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce véhicule ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>